<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Snack;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class SnacksController extends Controller
{

    // GET ALL SNACKS (Grouped for Display)
    public function index()
    {
        $snacks = Snack::where('is_deleted', 0)->orderBy('name')->get()->groupBy('name');   
        return view('management.snacks', compact('snacks'), ['title' => 'Manage Snacks']);
    }


    // SAVE SNACK
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'image'    => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'sizes'    => 'required|array|min:1',
            'sizes.*'  => 'required|string|max:20',
            'prices'   => 'required|array|min:1',
            'prices.*' => 'required|numeric|min:0',
        ]);

        $filename = null;
        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->file('image')->extension();
            $request->image->move(public_path('snackImages'), $filename);
        }

        $name = strtoupper($request->name);

        // Loop through the arrays and create a row for each size
        for ($i = 0; $i < count($request->sizes); $i++) {
            $snack = new Snack();
            $snack->name      = $name;
            $snack->image     = $filename;
            $snack->size      = strtoupper($request->sizes[$i]);
            $snack->price     = $request->prices[$i];
            $snack->available = 1;
            $snack->save();
        }

        return redirect()->route('snacks')->with('success', 'Snack saved successfully!');
    }

    // UPDATE SNACK
    public function update(Request $request)
    {
        $request->validate([
            'old_name' => 'required|string',
            'name'     => 'required|string|max:100',
            'image'    => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'ids'      => 'nullable|array',
            'sizes'    => 'required|array|min:1',
            'sizes.*'  => 'required|string|max:20',
            'prices'   => 'required|array|min:1',
            'prices.*' => 'required|numeric|min:0',
        ]);

        $oldName = strtoupper($request->old_name);
        $newName = strtoupper($request->name);
        
        $existingSnacks = Snack::where('name', $oldName)->get();
        if ($existingSnacks->isEmpty()) return redirect()->back()->with('error', 'Snack not found.');

        
        $filename = $existingSnacks->first()->image;
        if ($request->hasFile('image')) {
            $oldImagePath = public_path('snackImages/' . $filename);
            if (File::exists($oldImagePath)) File::delete($oldImagePath);
            
            $filename = time() . '.' . $request->file('image')->extension();
            $request->image->move(public_path('snackImages'), $filename);
        }

        //Delete removed sizes (Filter removes empty values from new added rows)
        $submittedIds = array_filter($request->ids ?? []);
        Snack::where('name', $oldName)->whereNotIn('idsnacks', $submittedIds)->delete();

        for ($i = 0; $i < count($request->sizes); $i++) {
            $id = $request->ids[$i] ?? null;

            if ($id) {
                $snack = Snack::find($id);
                if ($snack) {
                    $snack->name  = $newName;
                    $snack->size  = strtoupper($request->sizes[$i]);
                    $snack->price = $request->prices[$i];
                    $snack->image = $filename;
                    $snack->save();
                }
            } else {
                $newSnack = new Snack();
                $newSnack->name      = $newName;
                $newSnack->size      = strtoupper($request->sizes[$i]);
                $newSnack->price     = $request->prices[$i];
                $newSnack->image     = $filename;
                $newSnack->available = 1;
                $newSnack->save();
            }
        }

        return redirect()->route('snacks')->with('success', 'Snack updated successfully!');
    }

    // DELETE SNACK GROUP
    public function destroy(Request $request)
    {
        $snacks = Snack::where('name', $request->snack_name)->get();
        if ($snacks->isEmpty()) return redirect()->route('snacks')->with('error', 'Snack not found.');

        Snack::where('name', $request->snack_name)->update(['is_deleted' => 1]);

        return redirect()->route('snacks')->with('success', 'Snack deleted successfully!');
    }


    // TOGGLE AVAILABLE (Individual Size)
    public function toggleAvailable(Request $request)
    {
        $snack = Snack::find($request->id);
        if ($snack) {
            $snack->available = !$snack->available;
            $snack->save();
        }
    }
}