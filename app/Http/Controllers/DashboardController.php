<?php


namespace App\Http\Controllers;


use App\Appointment;
use App\Client;
use App\User;
use App\Movies;
use App\Shows;
use App\Showtimes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function dashboardIndex()
    {
        
        $shows = Shows::whereDate('date', '=', now()->toDateString())->get();
        
        $upcomingShows = Shows::whereDate('date', '>=', now()->toDateString())->get();
        $movieIdsWithShows = $upcomingShows->pluck('movies_movie_id')->unique();
        $movies = Movies::whereIn('movie_id', $movieIdsWithShows)
                ->where('status', 1)
                ->where('screening_status', 1)
                ->get();


        $showtimes = Showtimes::where('status',1)->get();
        $today = now()->format('Y-m-d');

        $allMovies = Movies::all();
        $shows = $shows->map(function ($show) use ($allMovies) {
            $show->movie_name = $allMovies->where('movie_id', $show->movies_movie_id)->first()->name ?? 'No movie found';
            return $show;
        });
        
        $showCount = count($shows);
       
        // $orderedShows = $shows->orderBy('time', 'asc');
        
        if(Auth::user()->user_role_iduser_role==1 || Auth::user()->user_role_iduser_role==2 || Auth::user()->user_role_iduser_role==3){
        return view('management.dashboard', 
                    ['title' => 'Dashboard',
                            'shows'=>$shows,
                            'showCount'=>$showCount,
                            'movies'=>$movies,
                            'showtimes'=>$showtimes,
                            'today'=>$today,
                            ]);
        }
        
    }


}