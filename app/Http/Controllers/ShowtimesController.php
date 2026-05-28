<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Showtimes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ShowtimesController extends Controller
{
    public function index(){

        $showtimes = Showtimes::all();   

        return view('movies.showtimes',compact('showtimes'),['title'=>'Showtimes']);
    }

    //Save Showtime
    public function saveShowtime(Request $request){
        $validator = Validator::make($request->all(), [
            'showtimeId' => ['required', 'integer', Rule::unique('showtimes', 'idshowtimes')],
            'time' => 'required|date_format:H:i'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        
        if($request->showtimeId<=5){
            $showtime = new Showtimes();
            $showtime->idshowtimes = $request->showtimeId;
            $showtime->time = $request->time;
            $showtime->status = 1;
            $showtime->save();
        }
        
        return redirect()->route('showtimes')->with('success', 'Showtime added successfully.');;
    }

    //Update Showtime
    public function updateShowtime(Request $request)
    {
        // Normalize the input time
        $requestTime = date('H:i:s', strtotime($request->time));

        $validator = Validator::make($request->all(), [
                    'time' => [
                    'required',
                    'date_format:H:i',
                    Rule::unique('showtimes', 'time')->ignore($request->hiddenShowtimeId, 'idshowtimes')
                ],
            ]);
    
        if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }
    
        // Update the time
        $showtime = Showtimes::find($request->hiddenShowtimeId);
        $showtime->time = $requestTime;
        $showtime->save();
    
        return redirect()->route('showtimes')->with('success', 'Showtime updated successfully.');
    }
    

    //Delete Showtime
    public function destroyShowtime($id){
        $showtime = Showtimes::find($id);
        $showtime->delete();
        return redirect()->route('showtimes');
    }
}
