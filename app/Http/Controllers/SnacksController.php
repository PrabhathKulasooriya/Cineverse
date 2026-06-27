<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Snack;
use App\SnackVariant;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class SnacksController extends Controller
{
    // Get all snacks
    public function index()
    {
        $snacks = Snack::with('variants')->get();
        return view('management.snacks', compact('snacks'), ['title' => 'Manage Snacks']);
    }

    // Save snack
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:100',
            'image'   => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'sizes'   => 'required|array|min:1',
            'sizes.*.size_label' => 'required|string|max:20',
            'sizes.*.price'      => 'required|numeric|min:0',
        ], [
            'name.required'             => 'Snack name is required.',
            'image.required'            => 'Image is required.',
            'image.mimes'               => 'Image must be jpeg, png, jpg, gif or svg.',
            'image.max'                 => 'Image must not exceed 4MB.',
            'sizes.required'            => 'At least one size/price is required.',
            'sizes.*.size_label.required' => 'Size label is required.',
            'sizes.*.price.required'    => 'Price is required.',
            'sizes.*.price.numeric'     => 'Price must be a number.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', $validator->errors()->first());
        }

        // Handle image upload
        $filename = null;
        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->file('image')->extension();
            $request->image->move(public_path('snackImages'), $filename);
        }

        // Save snack
        $snack = new Snack();
        $snack->name      = strtoupper($request->name);
        $snack->image     = $filename;
        $snack->available = 1;
        $snack->save();

        // Save variants
        foreach ($request->sizes as $size) {
            $variant = new SnackVariant();
            $variant->snacks_idsnacks = $snack->idsnacks;
            $variant->size            = strtoupper($size['size_label']);
            $variant->price           = $size['price'];
            $variant->available       = 1;
            $variant->save();
        }

        return redirect()->route('snacks')->with('success', 'Snack saved successfully!');
    }

    // Update snack
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'snack_id' => 'required|integer',
            'name'     => 'required|string|max:100',
            'image'    => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'sizes'    => 'required|array|min:1',
            'sizes.*.size_label' => 'required|string|max:20',
            'sizes.*.price'      => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', $validator->errors()->first());
        }

        $snack = Snack::find($request->snack_id);

        // Handle image
        if ($request->hasFile('image')) {
            $oldImage = public_path('snackImages/' . $snack->image);
            if (File::exists($oldImage)) {
                File::delete($oldImage);
            }
            $filename = time() . '.' . $request->file('image')->extension();
            $request->image->move(public_path('snackImages'), $filename);
            $snack->image = $filename;
        }

        $snack->name = strtoupper($request->name);
        $snack->save();

        $submittedSizes = array_map(function($size) {
            return strtoupper($size['size_label']);
        }, $request->sizes);

        SnackVariant::where('snacks_idsnacks', $snack->idsnacks)
            ->whereNotIn('size', $submittedSizes)
            ->update(['available' => 0]); 

        foreach ($request->sizes as $size) {
            $sizeLabel = strtoupper($size['size_label']);
            
           
            $variant = SnackVariant::where('snacks_idsnacks', $snack->idsnacks)
                                   ->where('size', $sizeLabel)
                                   ->first();

            if ($variant) {
                $variant->price = $size['price'];
                $variant->available = 1;
                $variant->save();
            } else {
                $newVariant = new SnackVariant();
                $newVariant->snacks_idsnacks = $snack->idsnacks;
                $newVariant->size = $sizeLabel;
                $newVariant->price = $size['price'];
                $newVariant->available = 1;
                $newVariant->save();
            }
        }

        return redirect()->route('snacks')->with('success', 'Snack updated successfully!');
    }
    // Delete snack
    public function destroy(Request $request)
    {
        $snack = Snack::find($request->snack_id);

        if (!$snack) {
            return redirect()->route('snacks')->with('error', 'Snack not found.');
        }

        // Delete image file
        $imagePath = public_path('snackImages/' . $snack->image);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        // Delete variants then snack
        SnackVariant::where('snacks_idsnacks', $snack->idsnacks)->delete();
        $snack->delete();

        return redirect()->route('snacks')->with('success', 'Snack deleted successfully!');
    }

    // Toggle available status (reuses your existing activateDeactivate pattern)
    public function toggleAvailable(Request $request)
    {
        $snack = Snack::find($request->id);
        if ($snack) {
            $snack->available = !$snack->available;
            $snack->save();
        }
    }
}