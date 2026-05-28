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



        $shows = Shows::whereRaw("STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i:%s') >= ?", [now()->subHours(3)])
                ->get();
        $movies = Movies::where('status', 1)->where('screening_status', 1)->get();
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

    //Save Shows**********************************************************************************************************
    public function store(Request $request){

     
        $validator = \Validator::make($request->all(), [

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
            return response()->json(['errors' =>$validator->errors()]);
        }

        
        
        $existingShow = Shows::where(function($query) use ($request){
                                        $query->where('date', $request->date)
                                        ->where('time', $request->time);
                                    })
                                ->orWhere('show_id', $request->hiddenMovieID)->first();
        

        if ($existingShow) {
             return response()->json(['errors' => 'Show already exists for this date and time']);
        }

        $show = new Shows();
        $show->movies_movie_id = $request->movie;
        $show->date = $request->date;
        $show->time = $request->time;
        $show->save();
        return response()->json(['success' => 'Show Added Successfully.']);
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
