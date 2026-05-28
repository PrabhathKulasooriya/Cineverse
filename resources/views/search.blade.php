@extends('moviePage_include.main')

@section('pageSpecificStyles')
<link rel="stylesheet" href="{{ asset('css/searchPage.css') }}">
@endsection



@section('pageSpecificContent')

    <div id="preloader">
        <div class="preloader">
            <div class="preloader-bounce">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>


    <div  class=" searchpage-container">


            <div class="section animated-row" data-section="slide02">
            <div class="section-inner movies-section">
                <div class="row">
                    <div >

                        <div class="title-block animate" data-animate="fadeInUp">
                            <span>Search Results for : '{{$query}}'</span>
                        </div>

                        <div class="services-section">
                            <div class="movie-list">

                                @if($movies->isEmpty())

                                    <p class="animate" data-animate="fadeInUp">No movies found for the search term : '{{$query}}</p>
                                    
                                @else

                                    @foreach ($movies as $movie )
                                     

                                        <a href="{{ route('bookmovie', ['movie_id' => $movie->movie_id]) }}" class="animate movie-card" data-animate="fadeInUp">
                                         <div class="movie-card-image">
                                           <img src="{{URL::asset('movieImages/'.$movie->image) }}" alt="" >
                                         </div>
                                         <div class="movie-card-details">
                                            <span class="movie-card-title">{{$movie->name}}</span>
                                            <span class="dot">&bull;</span>
                                            <span>{{$movie->language}}</span>
                                            <span class="dot">&bull;</span>
                                             <span>{{$movie->category}}  </span>
                                         </div>
                                        </a>
                    
                                     
                                    @endforeach

                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
    </div>
    


@endsection



@section('pageSpecificScript')



@endsection