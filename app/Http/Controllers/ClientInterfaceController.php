<?php


namespace App\Http\Controllers;
use App\Movies; 
use App\ImageSlider;


class ClientInterfaceController extends controller
{

public function index(){

    $bookingCutOff = now()->addMinutes(env('BOOKING_CUTOFF_MINUTES', 15));
    $currentDate = now()->format('Y-m-d');
    $currentTime = $bookingCutOff->format('H:i:s');

    $movies = Movies::where('status', 1)
    ->where('screening_status', 1)
    ->whereHas('shows', function ($query) use ($currentDate, $currentTime) {
        $query->where('date', '>', $currentDate)
              ->orWhere(function ($q) use ($currentDate, $currentTime) {
                  $q->where('date', '=', $currentDate)
                    ->where('time', '>=', $currentTime);
              });
    })
    ->get()
    ->each(function ($movie) {
        $movie->formatted_duration = $this->formatDuration($movie->duration);
    });

    $movieIds = $movies->pluck('movie_id'); 

    $imageSlider = ImageSlider::where('status', 1)
    ->where(function ($q) use ($movieIds) {
        $q->whereIn('movies_movie_id', $movieIds)
          ->orWhereNull('movies_movie_id');
    })
    ->get();

    $title = 'Cineverse';

    return view('index', compact('title', 'movies', 'imageSlider'));
}


    


    //duration conversion
    protected function formatDuration($minutes){
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        $formattedDuration = '';
    
        if ($hours > 0) {
         $formattedDuration .= $hours . 'h ';
     }
    
        if ($remainingMinutes > 0) {
         $formattedDuration .= $remainingMinutes . 'min';
     }

        return trim($formattedDuration);
    }

    
}