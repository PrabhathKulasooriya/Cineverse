<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File; 
use App\ImageSlider;
use App\Movies;


class SliderController extends Controller
{
    public function index(){
        $imageSlider = ImageSlider::all();
        $movies = Movies::where('status', 1)->get();
        
        return view('movies.movieSlider', compact('imageSlider', 'movies'), ['title' => 'Movie Slider']);
    }

    // Save Slider Image***************************************************************************************************
    public function store(Request $request)
 {
    $validator = Validator::make($request->all(), [
        'movie_id' => 'required|exists:movies,movie_id',
        'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:8192',
    ], [
        'movie_id.required' => 'Movie should be selected!',
        'movie_id.exists' => 'Selected movie is invalid.',
        'image.required' => 'Image should be provided!',
        'image.image' => 'Uploaded file must be an image!',
        'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, svg.',
        'image.max' => 'Image size must not exceed 8MB.',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()]);
    }

  
    $movie = Movies::find($request->movie_id);

    // Handle file upload
    $filename = null;
    if ($request->hasFile('image')) {
        $filename = time() . '.' . $request->file('image')->extension();
        $request->image->move(public_path('sliderImages'), $filename);
    }

    $saveSlider = new ImageSlider();
    $saveSlider->movies_movie_id = $movie->movie_id;
    $saveSlider->movie_name = $movie->name;
    $saveSlider->image = $filename;
    $saveSlider->save();

    return redirect()->route('movieSlider')->with('success', 'Poster saved successfully!');
}

    // Save Slider Image End******************************************************************************************




    // Delete Slider Images*******************************************************************************************
    public function destroy(Request $request, $id){

        $sliderImage = ImageSlider::find($id);

        $imagePath = public_path('sliderImages/' . $sliderImage->image);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        $sliderImage->delete();

        return redirect()->route('movieSlider')->with('success', 'Poster deleted successfully!');
    }

    // Delete Slider Image End****************************************************************************************


    // Update Slider Images*******************************************************************************************
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'movie_id' => 'required|exists:movies,movie_id',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:8192',
        ], [
            'movie_id.required' => 'Movie should be selected!',
            'movie_id.exists' => 'Selected movie is invalid.',
            'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, svg.',
            'image.max' => 'Image size must not exceed 8MB.',
        ]);

    // Check validation
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()]);
    }

    $sliderImage = ImageSlider::find($id);

    // Get movie info
    $movie = Movies::find($request->movie_id);
    if (!$movie) {
        return back()->withErrors(['movie_id' => 'Invalid movie selected!']);
    }

    // Handle optional image upload
    $filename = $sliderImage->image;  // keep old image by default

    if ($request->hasFile('image')) {
        $oldImagePath = public_path('sliderImages/' . $sliderImage->image);
        if (File::exists($oldImagePath)) {
            File::delete($oldImagePath);
        }

        $filename = time() . '.' . $request->file('image')->extension();
        $request->image->move(public_path('sliderImages'), $filename);
    }

    // Update slider image record
    $sliderImage->movies_movie_id = $movie->movie_id;
    $sliderImage->movie_name = $movie->name;
    $sliderImage->image = $filename;
    $sliderImage->save();

    return redirect()->route('movieSlider')->with('success', 'Poster updated successfully!');
}

    // Update Image End****************************************************************************************

  
}
