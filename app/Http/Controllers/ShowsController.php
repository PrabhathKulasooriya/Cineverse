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
    $availableSlotCount = $availableTimeSlots->count();
    if ($availableSlotCount > 0) {
        foreach ($bookedTimesByDate as $date => $bookedTimes) {
            $uniqueBookedTimes = array_unique($bookedTimes);
            if (count($uniqueBookedTimes) >= $availableSlotCount) {
                $fullyBookedDates[] = $date;
            }
        }
    }

    $shows = Shows::whereRaw("STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i:%s') >= ?", [now()->subHours(2)])
            ->get();
    $movies = Movies::where('status', 1)
        ->where('screening_status', 1)
        ->where('release_date', '<=', Carbon::today()->toDateString())
        ->get();
    $showtimes = Showtimes::orderBy('time', 'asc')->get();
    $enabledShowtimes = $showtimes->where('status', 1)->values();

    // Get every show_id that has at least one booking, so we know which shows to lock from editing
    $bookedShowIds = DB::table('bookings')->pluck('shows_show_id')->unique()->toArray();

    $shows = $shows->map(function ($show) use ($movies, $bookedShowIds) {
        $show->movie_name = $movies->where('movie_id', $show->movies_movie_id)->first()->name ?? 'No movie found';
        $show->has_bookings = in_array($show->show_id, $bookedShowIds);
        return $show;
    });
    
    return view('movies.shows',compact('shows','movies','showtimes','enabledShowtimes','fullyBookedDates'), ['title' => 'Manage Upcoming Shows']);

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
        $enabledShowtimes = Showtimes::where('status', 1)
            ->orderBy('time', 'asc')
            ->get();

        if ($enabledShowtimes->isEmpty()) {
            return response()->json(['errors' => 'No enabled showtime anchors are available for scheduling.']);
        }

        $rules = [
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ];

        foreach ($enabledShowtimes as $index => $showtime) {
            $rules['movie' . ($index + 1)] = 'nullable|exists:movies,movie_id';
        }

        $validator = Validator::make($request->all(), $rules, [
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

        // Build the list of slots the user actually picked movies for
        $selectedSlots = [];
        $selectedMoviesCount = 0;
        foreach ($enabledShowtimes as $index => $showtime) {
            $movieId = $request->input('movie' . ($index + 1));
            $movie = $movieId ? Movies::find($movieId) : null;

            if ($movie !== null) {
                $selectedMoviesCount++;
            }

            $selectedSlots[] = [
                'slotIndex' => $index + 1,
                'showtime' => $showtime,
                'anchor_minutes' => $this->convertTimeToMinutes($showtime->time),
                'movie' => $movie,
            ];
        }

        if ($selectedMoviesCount === 0) {
            return response()->json(['errors' => 'Please select at least one movie.']);
        }

        $businessEnd = 24 * 60 + 30; // 12:30 AM next day
        $minimumGap = 15;

        $showsToCreate = [];
        $issues = []; // every problem we run into gets added here, so nothing is skipped silently

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateString = $currentDate->toDateString();
            $bookedRangesByDate = [
                $dateString => $this->loadBookedRanges($dateString),
            ];

            $createdThisDate = 0;
            $carryOver = null;

            foreach ($selectedSlots as $slot) {
                $movie = $slot['movie'];
                $anchorMinutes = $slot['anchor_minutes'];
                $showTimeLabel = Carbon::parse($slot['showtime']->time)->format('h:i A');

                if ($movie === null) {
                    // No movie chosen for this slot, just leave it open - nothing to report here
                    continue;
                }

                $startTime = $anchorMinutes;
                if ($carryOver !== null) {
                    $startTime = max($startTime, $carryOver);
                }
                $startTime = $this->roundUpToQuarterHour($startTime);
                $endTime = $startTime + $movie->duration;

                if ($endTime > $businessEnd) {
                    return response()->json([
                        'errors' => 'Schedule overflow on ' . $dateString . ' for slot ' . $slot['slotIndex'] .
                            ' (' . $showTimeLabel . ') with movie "' . $movie->name . '". It exceeds 12:30 AM.',
                    ]);
                }

                $targetDate = $dateString;
                if ($startTime >= 24 * 60) {
                    $targetDate = Carbon::parse($dateString)->addDay()->toDateString();
                }

                if (!isset($bookedRangesByDate[$targetDate])) {
                    $bookedRangesByDate[$targetDate] = $this->loadBookedRanges($targetDate);
                }

                $conflictStart = $startTime;
                $conflictEnd = $endTime;
                if ($targetDate !== $dateString) {
                    $conflictStart -= 24 * 60;
                    $conflictEnd -= 24 * 60;
                }

                if ($this->hasConflict($conflictStart, $conflictEnd, $bookedRangesByDate[$targetDate], $minimumGap)) {
                    $issues[] = $dateString . ': Slot ' . $slot['slotIndex'] . ' (' . $showTimeLabel . ') for "' . $movie->name . '" was skipped - conflicts with an existing show.';
                    continue;
                }

                $showTimeMinutes = $startTime;
                if ($showTimeMinutes >= 24 * 60) {
                    $showTimeMinutes -= 24 * 60;
                }

                $showsToCreate[] = [
                    'movies_movie_id' => $movie->movie_id,
                    'date' => $targetDate,
                    'time' => sprintf('%02d:%02d:00', intdiv($showTimeMinutes, 60), $showTimeMinutes % 60),
                ];

                $bookedRangesByDate[$targetDate][] = ['start' => $conflictStart, 'end' => $conflictEnd];
                $createdThisDate++;
                $carryOver = $this->roundUpToQuarterHour($endTime + $minimumGap);
            }

            if ($createdThisDate === 0) {
                $issues[] = $dateString . ': No shows were created - this date is fully booked or has no available slot.';
            }

            $currentDate->addDay();
        }

        if (count($showsToCreate) === 0) {
            return response()->json([
                'errors' => 'No shows could be created for the selected dates.',
                'issues' => $issues,
            ]);
        }

        foreach ($showsToCreate as $showData) {
            $show = new Shows();
            $show->movies_movie_id = $showData['movies_movie_id'];
            $show->date = $showData['date'];
            $show->time = $showData['time'];
            $show->save();
        }

        $response = ['success' => count($showsToCreate) . ' show(s) created successfully.'];
        if (!empty($issues)) {
            $response['issues'] = $issues;
        }

        return response()->json($response);
    }

    private function loadBookedRanges($dateString)
    {
        $existingShows = DB::table('shows')
            ->join('movies', 'movies.movie_id', '=', 'shows.movies_movie_id')
            ->where('shows.date', $dateString)
            ->select('shows.time', 'movies.duration')
            ->get();

        $ranges = [];
        foreach ($existingShows as $existingShow) {
            $timeParts = explode(':', $existingShow->time);
            $existingStart = ((int)$timeParts[0]) * 60 + (int)$timeParts[1];
            $ranges[] = ['start' => $existingStart, 'end' => $existingStart + $existingShow->duration];
        }

        return $ranges;
    }

    private function convertTimeToMinutes($time)
    {
        $parts = explode(':', $time);
        return ((int)$parts[0] * 60) + (int)$parts[1];
    }

    private function roundUpToQuarterHour($minutes)
    {
        return (int) (ceil($minutes / 15) * 15);
    }

    private function hasConflict($startTime, $endTime, $bookedRanges, $minimumGap)
    {
        foreach ($bookedRanges as $range) {
            if ($startTime < ($range['end'] + $minimumGap) && ($endTime + $minimumGap) > $range['start']) {
                return true;
            }
        }

        return false;
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

        // Block editing if this show already has bookings - the movie/date/time can no longer be changed
        $bookingCount = Bookings::where('shows_show_id', $show->show_id)->count();
        if ($bookingCount > 0) {
            return response()->json(['errors' => 'This show already has bookings and cannot be edited.']);
        }

        $movie = Movies::find($request->movie);
        if (!$movie) {
            return response()->json(['errors' => 'Selected movie not found.']);
        }

        $timeParts = explode(':', $request->time);
        $startMinutes = ((int)$timeParts[0]) * 60 + (int)$timeParts[1];
        $endMinutes = $startMinutes + $movie->duration;

        $bookedShows = DB::table('shows')
            ->join('movies', 'movies.movie_id', '=', 'shows.movies_movie_id')
            ->where('shows.date', $request->date)
            ->where('shows.show_id', '!=', $show->show_id)
            ->select('shows.time', 'movies.duration')
            ->get();

        $bookedRanges = [];
        foreach ($bookedShows as $bookedShow) {
            $bookedParts = explode(':', $bookedShow->time);
            $bookedStart = ((int)$bookedParts[0]) * 60 + (int)$bookedParts[1];
            $bookedRanges[] = ['start' => $bookedStart, 'end' => $bookedStart + $bookedShow->duration];
        }

        if ($this->hasConflict($startMinutes, $endMinutes, $bookedRanges, 15)) {
            return response()->json(['errors' => 'Updated showtime conflicts with another existing show on ' . $request->date . '.']);
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
            'movie_id' => 'nullable|exists:movies,movie_id',
            'exclude_show_id' => 'nullable|exists:shows,show_id',
        ]);

        $date = $validated['date'];
        $movieId = $validated['movie_id'] ?? null;
        $excludeShowId = $validated['exclude_show_id'] ?? null;

        $bookedShows = DB::table('shows')
            ->join('movies', 'movies.movie_id', '=', 'shows.movies_movie_id')
            ->when($excludeShowId, function ($query) use ($excludeShowId) {
                return $query->where('shows.show_id', '!=', $excludeShowId);
            })
            ->where('shows.date', $date)
            ->select('shows.time', 'movies.duration')
            ->get();

        $bookedRanges = [];
        foreach ($bookedShows as $bookedShow) {
            $timeParts = explode(':', $bookedShow->time);
            $start = ((int)$timeParts[0]) * 60 + (int)$timeParts[1];
            $bookedRanges[] = ['start' => $start, 'end' => $start + $bookedShow->duration];
        }

        $movieDuration = null;
        if ($movieId) {
            $movie = Movies::find($movieId);
            $movieDuration = $movie ? $movie->duration : null;
        }

        $showtimes = Showtimes::where('status', 1)
            ->orderBy('time', 'asc')
            ->get();

        $availableShowtimes = [];
        foreach ($showtimes as $showtime) {
            $start = $this->convertTimeToMinutes($showtime->time);
            $end = $movieDuration !== null ? $start + $movieDuration : $start + 15;

            if ($movieDuration !== null) {
                if ($this->hasConflict($start, $end, $bookedRanges, 15)) {
                    continue;
                }
            } else {
                $bookedTimes = array_map(function ($range) {
                    return $range['start'];
                }, $bookedRanges);

                if (in_array($start, $bookedTimes, true)) {
                    continue;
                }
            }

            $showtime->formatted_time = Carbon::createFromFormat('H:i:s', $showtime->time)->format('h:i A');
            $availableShowtimes[] = $showtime;
        }

        return response()->json([
            'success' => true,
            'showtimes' => $availableShowtimes
        ]);
    }
}
