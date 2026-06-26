<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Snack;
use App\SnackVariant;
use App\BookingSnack;

class SnackController extends Controller
{
    //Admin Snack Management ****************************************************************************************************
    public function index(){
        $snacks = Snack::with('variants')->get();
        return view('snacks.snacks', compact('snacks'), ['title' => 'Manage Snacks']);
    }
}
