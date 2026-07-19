<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Shows;
use App\Movies;
use App\Showtimes;
use App\Bookings;
use DB;
use Carbon\Carbon;

class ShowsController extends Controller
{
    public function index(){

        $availableTimeSlots = Showtimes::where('status', 1)->get();
        $scheduledShows = Shows::whereDate('date', '>=', now()->toDateString())->get();
        $bookedTimesByDate = [];
                foreach ($scheduledShows as $show) {
                    $dateKey = $show->date;
                         if (!isset($bookedTimesByDate[$dateKey])) {
                             $bookedTimesByDate[$dateKey] = [];
                            }
                            $bookedTimesByDate[$dateKey][] = $show->time;
                            }
    
                     // Find fully booked dates
                     $fullyBookedDates = [];
                     foreach ($bookedTimesByDate as $date => $bookedTimes) {
                         // Count unique booked times for the date
                         $uniqueBookedTimes = array_unique($bookedTimes);
                            
                            // Check if all available time slots are booked for this date
                            if (count($uniqueBookedTimes) >= count($availableTimeSlots)) {
                                $fullyBookedDates[] = $date;
                         }
                      }



        $shows = Shows::whereRaw("STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i:%s') >= ?", [now()->subHours(2)])
                ->get();
        $movies = Movies::where('status', 1)->where('screening_status', 1)->where('release_date','<=',today()->get)->get();
        $showtimes = Showtimes::where('status',1)->get();
        $shows = $shows->map(function ($show) use ($movies) {
            $show->movie_name = $movies->where('movie_id', $show->movies_movie_id)->first()->name ?? 'No movie found';
            return $show;
        });
        
        return view('movies.shows',compact('shows','movies','showtimes','fullyBookedDates'), ['title' => 'Manage Upcoming Shows']);
    
    }

    //Screened Shows************************************************************************************************
    public function screened(){

        $shows = Shows::whereRaw("STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i:%s') < ?", [now()])
                ->get();
        $movies = Movies::all();

        $showtimes = Showtimes::all();

        $shows = $shows->map(function ($show) use ($movies) {
            $show->movie_name = $movies->where('movie_id', $show->movies_movie_id)->first()->name ?? 'No movie found';
            return $show;
        });
        
        return view('movies.screenedShows',compact('shows','movies','showtimes'), ['title' => 'Screened Shows']);
    }

    //Save Shows For A Date Range*******************************************************************************
    public function storeShows(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'movie1' => 'nullable|exists:movies,movie_id',
            'movie2' => 'nullable|exists:movies,movie_id',
            'movie3' => 'nullable|exists:movies,movie_id',
            'movie4' => 'nullable|exists:movies,movie_id',
            'movie5' => 'nullable|exists:movies,movie_id',
        ], [
            'start_date.required' => 'Please select a start date.',
            'end_date.after_or_equal' => 'End date cannot be before start date.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : $startDate->copy();

        if ($endDate->gt(Carbon::today()->addMonths(2))) {
            return response()->json(['errors' => 'Shows cannot be created more than 2 months in advance.']);
        }

        // Collect the movies the user actually picked, in order, skipping empty inputs
        $selectedMovies = [];
        for ($i = 1; $i <= 5; $i++) {
            $movieId = $request->input('movie' . $i);
            if (!empty($movieId)) {
                $movie = Movies::find($movieId);
                if ($movie) {
                    $selectedMovies[] = $movie;
                }
            }
        }

        if (count($selectedMovies) == 0) {
            return response()->json(['errors' => 'Please select at least one movie.']);
        }

        $businessStart = 9 * 60 + 30;  // 9:30 AM in minutes from midnight
        $businessEnd = 24 * 60 + 30;   // 12:30 AM next day, in minutes
        $minimumGap = 15;

        $totalDuration = 0;
        foreach ($selectedMovies as $movie) {
            $totalDuration += $movie->duration;
        }

        $movieCount = count($selectedMovies);
        $gapCount = $movieCount - 1; // last show needs no gap after it

        $gap = $minimumGap;
        if ($gapCount > 0) {
            $gap = intdiv(($businessEnd - $businessStart) - $totalDuration, $gapCount);
            if ($gap < $minimumGap) {
                $gap = $minimumGap;
            }
        }

        if ($totalDuration + ($gap * $gapCount) > ($businessEnd - $businessStart)) {
            return response()->json(['errors' => 'Selected movies are too long to fit into one day (9:30 AM - 12:30 AM). Please select fewer or shorter movies.']);
        }

        // Build the one shared schedule (same start times applied to every date in the range)
        $scheduleTimes = [];
        $cursor = $businessStart;
        foreach ($selectedMovies as $index => $movie) {
            $roundedStart = (int) (ceil($cursor / 15) * 15); // round up to nearest 15 min, gap never shrinks
            $scheduleTimes[] = $roundedStart;
            $cursor = $roundedStart + $movie->duration + $gap;
        }

        $showsToCreate = [];
        $skippedDates = [];
        $skippedSlots = [];

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {

            $dateString = $currentDate->toDateString();

            $existingShows = DB::table('shows')
                ->join('movies', 'movies.movie_id', '=', 'shows.movies_movie_id')
                ->where('shows.date', $dateString)
                ->select('shows.time', 'movies.duration')
                ->get();

            $bookedRanges = [];
            foreach ($existingShows as $existingShow) {
                $timeParts = explode(':', $existingShow->time);
                $existingStart = ((int)$timeParts[0]) * 60 + (int)$timeParts[1];
                $bookedRanges[] = ['start' => $existingStart, 'end' => $existingStart + $existingShow->duration];
            }

            $placedCountForDate = 0;

            foreach ($selectedMovies as $index => $movie) {

                $slotStart = $scheduleTimes[$index];
                $slotEnd = $slotStart + $movie->duration;

                $conflict = false;
                foreach ($bookedRanges as $range) {
                    if ($slotStart < ($range['end'] + $minimumGap) && $slotEnd + $minimumGap > $range['start']) {
                        $conflict = true;
                        break;
                    }
                }

                if ($conflict) {
                    $skippedSlots[] = $dateString . ' - ' . $movie->name . ' (' . sprintf('%02d:%02d', intdiv($slotStart,60), $slotStart % 60) . ' already occupied)';
                    continue;
                }

                $showsToCreate[] = [
                    'movies_movie_id' => $movie->movie_id,
                    'movie_name' => $movie->name,
                    'date' => $dateString,
                    'time' => sprintf('%02d:%02d:00', intdiv($slotStart, 60) % 24, $slotStart % 60),
                ];

                $bookedRanges[] = ['start' => $slotStart, 'end' => $slotEnd];
                $placedCountForDate++;
            }

            if ($placedCountForDate == 0) {
                $skippedDates[] = $dateString . ' (fully booked)';
            }

            $currentDate->addDay();
        }

        if (count($showsToCreate) == 0) {
            return response()->json(['errors' => 'No shows could be created for the selected dates. They may already be fully booked.']);
        }

        // TESTING ONLY - remove this dd() once the schedule looks right, then the save loop below will run.
        dd([
            'shows_to_create' => $showsToCreate,
            'skipped_dates' => $skippedDates,
            'skipped_slots' => $skippedSlots,
        ]);

        foreach ($showsToCreate as $showData) {
            $show = new Shows();
            $show->movies_movie_id = $showData['movies_movie_id'];
            $show->date = $showData['date'];
            $show->time = $showData['time'];
            $show->save();
        }

        return response()->json(['success' => count($showsToCreate) . ' show(s) created successfully.']);
    }

    public function update(Request $request){ 
        
    
        $validator = Validator::make($request->all(), [
            'movie' => 'required',
            'date' => 'required|date',
            'time' => 'required',
        ], [
            'movie.required' => 'Please select a movie.',
            'date.required' => 'Please select a date.',
            'date.date' => 'Please enter a valid date.',
            'time.required' => 'Please select a time.',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
    
        // Find the show using the correct ID field
        $show = Shows::find($request->hiddenMovieID);
        
        if (!$show) {
            return response()->json(['errors' => 'Show not found']);
        }

        // Find if any show has same date and time as request
        $existingShow = Shows::where('date', $request->date)
                                ->where('time', $request->time) 
                                ->where('show_id','!=',$request->hiddenMovieID)
                                ->first();

        if ($existingShow) {
             return response()->json(['errors' => 'Show already exists for this date and time']);
        }  
    
        $show->movies_movie_id = $request->movie;
        $show->date = $request->date;
        $show->time = $request->time;
        $show->save();
        
        return response()->json(['success' => 'Show Updated Successfully.']);
    }

    public function destroy(Request $request){
        try {
            $show = Shows::find($request->show_id);

            if (!$show) {
                return response()->json(['errors' => 'Show not found.']);
            }
 
            $bookings = Bookings::where('shows_show_id', $show->show_id)->get();
            
            if ($bookings->count() > 0) {
                return response()->json(['errors' => 'This show has bookings and cannot be deleted.']);
            }

            $show->delete();
            
            return response()->json(['success' => 'Show deleted successfully.']);
            
        } catch (\Exception $e) {
            return response()->json(['errors' => 'An error occurred while deleting the show.']);
        } 
    }



    public function getAvailableShowtimes(Request $request)
    {
    
    $validated = $request->validate([
        'date' => 'required|date_format:Y-m-d',
    ]);

    $date = $validated['date'];
    
    // Query to get booked show times for this date
    $bookedTimes = DB::table('shows')
        ->where('date', $date)
        ->pluck('time')
        ->toArray();
    
    // Get all available showtimes by excluding booked ones
    $availableShowtimes = DB::table('showtimes')
        ->whereNotIn('time', $bookedTimes)
        ->get();
    
    // Format the time for display
    foreach ($availableShowtimes as $showtime) {
        $showtime->formatted_time = Carbon::createFromFormat('H:i:s', $showtime->time)->format('h:i A');
    }
    
    return response()->json([
        'success' => true,
        'showtimes' => $availableShowtimes
    ]);
}
}
