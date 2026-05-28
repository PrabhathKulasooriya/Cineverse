<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Movies; 
use App\ImageSlider;
use App\Shows;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File; 

class MoviesController extends Controller 
{
    // Get movies***********************************************************************************************
    public function index(){

        $movies = Movies::where('screening_status', 1)->get()->map(function ($movie) {
            $movie->formatted_duration = $this->formatDuration($movie->duration);
            return $movie;
        });
        

        return view('movies.movies', compact('movies'), ['title' => 'Manage Screening Movies']);
    }

    // Save movie***********************************************************************************************
    public function store(Request $request)
    {
        // Validator with correct facade usage
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'category' => 'required|string',
            'duration' => 'required',
            'language' => 'required',
            'trailer' => 'required',
            'rating' => 'required',
            'date' => 'required|date',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ], [
            'name.required' => 'Name should be provided!',
            'name.string' => 'Name must be a valid string.',
            'category.required' => 'Category should be provided!',
            'category.string' => 'Category must be a valid string.',
            'duration.required' => 'Duration should be provided!',
            'language.required' => 'Language should be provided!',
            'trailer.required' => 'Trailer should be provided!',
            'rating.required' => 'Rating should be provided!',
            'date.required' => 'Please select a date.',
            'date.date' => 'Please enter a valid date.',
            'image.required' => 'Image should be provided!',
            'image.image' => 'Uploaded file must be an image!',
            'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, svg.',
            'image.max' => 'Image size must not exceed 4MB.',
        ]);

        // Check validation
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        

        // Duration conversion method
        $duration = $this->convertDurationToMinutes($request->input('duration'));

        // Handle file upload
        $filename = null;
        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->file('image')->extension();
            $request->image->move(public_path('movieImages'), $filename);
        }

        // Save movie
        $saveMovie = new Movies();
        $saveMovie->name = strtoupper($request->name);
        $saveMovie->category = strtoupper($request->category);
        $saveMovie->duration = $duration;
        $saveMovie->language = strtoupper($request->language);
        $saveMovie->trailer = $request->trailer;
        $saveMovie->rating = $request->rating;
        $saveMovie->release_date = $request->date;
        $saveMovie->image = $filename;
        $saveMovie->status = 1;
        $saveMovie->screening_status = 1;
        $saveMovie->save();

        return redirect()->route('movies')->with('success', 'Movie saved successfully!');
    }

    

    // Update Movie*******************************************************************************************
        public function update(Request $request){

            $movie_id = $request['hiddenMovieId'];

            $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'category' => 'required|string',
            'duration' => 'required',
            'language' => 'required',
            'rating' => 'required|numeric|min:0|max:10',
            'trailer' => 'required',
            'date' => 'required|date',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ], [
            'name.required' => 'Name should be provided!',
            'name.string' => 'Name must be a valid string.',
            'category.required' => 'Category should be provided!',
            'category.string' => 'Category must be a valid string.',
            'duration.required' => 'Duration should be provided!',
            'language.required' => 'Language should be provided!',
            'trailer.required' => 'Trailer should be provided!',
            'rating.required' => 'Rating should be provided!',
            'rating.numeric' => 'Rating must be a number.',
            'rating.min' => 'Rating must be between 0 and 10.',
            'rating.max' => 'Rating must be between 0 and 10.',
            'date.required' => 'Please select a date.',
            'date.date' => 'Please enter a valid date.',
            'image.required' => 'Image should be provided!',
            'image.image' => 'Uploaded file must be an image!',
            'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, svg.',
            'image.max' => 'Image size must not exceed 4MB.',
        ]);

        // Check validation
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $movie = Movies::find($movie_id);

        $filename = $movie->image;
        if ($request->hasFile('image')){
            $oldImage = public_path('movieImages/' . $movie->image);
            if (File::exists($oldImage)) {
                File::delete($oldImage);
            }
            $filename = time() . '.' . $request->file('image')->extension();
            $request->image->move(public_path('movieImages'), $filename);
            }
        
        $duration = $this->convertDurationToMinutes($request->input('duration'));


        // update movie
       
        $movie->name = strtoupper($request->name);
        $movie->category = strtoupper($request->category);
        $movie->duration = $duration;
        $movie->language = strtoupper($request->language);
        $movie->rating = $request->rating;
        $movie->release_date = $request->date;
        $movie->trailer = $request->trailer;
        $movie->image = $filename;

        $movie->save();

        return redirect()->route('movies')->with('success', 'Movie Updated successfully!');


 
        }
    // Update Movie End****************************************************************************************


    // Delete Movies*******************************************************************************************
    public function destroy(Request $request){
        
        $movie = Movies::find($request->movie_id);
        $shows = Shows::whereRaw("STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i:%s') >= ?", [now()])
                        ->where('movies_movie_id', $movie->movie_id)->get();
                    
        
        if($shows->isNotEmpty()){
            return redirect()->route('movies')->with('error', 'Upcomig Shows available for this movie!');
        }else{
        
        // $sliderImage = ImageSlider::where('movies_movie_id', $movie->movie_id)->first();

        // if($sliderImage){
        //     $imagePathSlider = public_path('sliderImages/' . $sliderImage->image);
        //     if (File::exists($imagePathSlider)) {
        //         File::delete($imagePathSlider);
        //     }

        // $sliderImage->delete();
        // }

        $movie->status = 0;
        $movie->screening_status = 0;
        $movie->save();

        return redirect()->route('movies')->with('success', 'Movie Deleted Successfully!');
        }
    }

    // Delete Movies End****************************************************************************************

    // Screened Movies Start****************************************************************************************
        public function screenedMovies(){
        
         $movies = Movies::where('screening_status', 0)->get()->map(function ($movie) {
         $movie->formatted_duration = $this->formatDuration($movie->duration);
                                        return $movie;
                                    });
    

        return view('movies.screenedMovies',['movies' => $movies], ['title' => 'Manage Screened Movies']);
    }


    public function activateScreenMovie(Request $request){

        $movie = Movies::find($request->movie_id);
        $movie->screening_status = 1;
        $movie->save();
        return redirect()->route('screenedMovies')->with('success', 'Movie Screened Back Successfully!');

    }
     // Screened Movies End******************************************************************************************



    // Duration conversion methods
    private function convertDurationToMinutes($duration) 
    {
        preg_match('/(\d+)h/', $duration, $hours);
        preg_match('/(\d+)min/', $duration, $minutes);

        $hours = isset($hours[1]) ? (int)$hours[1] * 60 : 0;
        $minutes = isset($minutes[1]) ? (int)$minutes[1] : 0;

        return $hours + $minutes;
    }

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

    // Duration conversion methods end

    //Search function*******************************************************************************************
    public function search(Request $request){
        $query = $request->input('query');
    
        if (!$query) {
            return redirect()->back();
        }
    
        $searchTerm = "%" . strtoupper($query) . "%";
        
        $movies = Movies::where('status', 1)
                          ->where(function ($query) use ($searchTerm) {
                            $query->where('name', 'like', $searchTerm)
                                  ->orWhere('category', 'like', $searchTerm)
                                  ->orWhere('language', 'like', $searchTerm);
                        })
                        ->get();
    
        return view('search', compact('movies', 'query'));
    }
    
    //Search function end*******************************************************************************************

    
}