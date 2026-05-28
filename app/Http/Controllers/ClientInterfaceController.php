<?php


namespace App\Http\Controllers;
use App\Movies; 
use App\ImageSlider;


class ClientInterfaceController extends controller
{

public function index(){

    $movies = Movies::where('status', 1)
            ->where('screening_status', 1)
            ->whereHas('shows', function ($query) {
            $query->whereNotNull('date')
              ->whereNotNull('time')
              ->whereRaw(
                  "STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i:%s') >= ?",
                  [now()]
              );
    })
    ->get()
    ->map(function ($movie) {
        $movie->formatted_duration = $this->formatDuration($movie->duration);
        return $movie;
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