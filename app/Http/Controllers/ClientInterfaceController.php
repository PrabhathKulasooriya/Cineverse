<?php


namespace App\Http\Controllers;
use App\Movies; 
use App\ImageSlider;


class ClientInterfaceController extends controller
{

public function index(){

    $bookingCutOff = now()->addMinutes(env('BOOKING_CUTOFF_MINUTES', 15));

    $movies = Movies::where('status', 1)
    ->where('screening_status', 1)
    ->whereHas('shows', function ($query) use ($bookingCutOff) {
        // Range query is faster and uses indexes
        $query->where('date', '>=', $bookingCutOff->format('Y-m-d'))
              ->orWhere(function ($q) use ($bookingCutOff) {
                  $q->where('date', '=', $bookingCutOff->format('Y-m-d'))
                    ->where('time', '>=', $bookingCutOff->format('H:i:s'));
              });
    })
    ->get()
    ->each(function ($movie) {
        $movie->formatted_duration = $this->formatDuration($movie->duration);
    });

    $movieIds = $movies->pluck('movie_id'); 

    $imageSlider = ImageSlider::whereIn('movies_movie_id', $movieIds)->get();

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