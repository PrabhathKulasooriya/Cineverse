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
            'movie_id' => 'nullable|exists:movies,movie_id',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:8192',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpeg,png,jpg,gif,svg,webp|max:8192',
        ], [
            'movie_id.exists' => 'Selected movie is invalid.',
            'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, svg, webp.',
            'image.max' => 'Image size must not exceed 8MB.',
            'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg, gif, svg, webp.',
            'images.*.max' => 'Each image size must not exceed 8MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (!$request->hasFile('image') && !$request->hasFile('images')) {
            return redirect()->back()->withErrors(['image' => 'Please select at least one image file.'])->withInput();
        }

        $movieId = $request->filled('movie_id') ? $request->movie_id : null;
        $movieName = null;
        if ($movieId) {
            $movie = Movies::find($movieId);
            if ($movie) {
                $movieName = $movie->name;
            } else {
                $movieId = null;
            }
        }

        $files = [];
        if ($request->hasFile('image')) {
            $files[] = $request->file('image');
        }
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file && $file->isValid()) {
                    $files[] = $file;
                }
            }
        }

        foreach ($files as $index => $file) {
            $filename = time() . '_' . $index . '_' . uniqid() . '.' . $file->extension();
            $file->move(public_path('sliderImages'), $filename);

            $saveSlider = new ImageSlider();
            $saveSlider->movies_movie_id = $movieId;
            $saveSlider->movie_name = $movieName;
            $saveSlider->image = $filename;
            $saveSlider->status = 1;
            $saveSlider->save();
        }

        return redirect()->route('movieSlider')->with('success', 'Slider image(s) saved successfully!');
    }
    // Save Slider Image End******************************************************************************************




    // Delete Slider Images*******************************************************************************************
    public function destroy(Request $request, $id)
    {
        $sliderImage = ImageSlider::find($id);

        if ($sliderImage) {
            $imagePath = public_path('sliderImages/' . $sliderImage->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }

            $sliderImage->delete();
        }

        return redirect()->route('movieSlider')->with('success', 'Poster deleted successfully!');
    }

    // Delete Slider Image End****************************************************************************************


    // Update Slider Images*******************************************************************************************
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'movie_id' => 'nullable|exists:movies,movie_id',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:8192',
        ], [
            'movie_id.exists' => 'Selected movie is invalid.',
            'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, svg, webp.',
            'image.max' => 'Image size must not exceed 8MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $sliderImage = ImageSlider::find($id);
        if (!$sliderImage) {
            return redirect()->route('movieSlider')->with('error', 'Slider image not found!');
        }

        $movieId = $request->filled('movie_id') ? $request->movie_id : null;
        $movieName = null;
        if ($movieId) {
            $movie = Movies::find($movieId);
            if ($movie) {
                $movieName = $movie->name;
            } else {
                $movieId = null;
            }
        }

        $filename = $sliderImage->image;

        if ($request->hasFile('image')) {
            $oldImagePath = public_path('sliderImages/' . $sliderImage->image);
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            $filename = time() . '_' . uniqid() . '.' . $request->file('image')->extension();
            $request->image->move(public_path('sliderImages'), $filename);
        }

        $sliderImage->movies_movie_id = $movieId;
        $sliderImage->movie_name = $movieName;
        $sliderImage->image = $filename;
        $sliderImage->save();

        return redirect()->route('movieSlider')->with('success', 'Slider image updated successfully!');
    }
    // Update Image End****************************************************************************************
}

