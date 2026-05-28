<?php

namespace App\Http\Controllers;

use App\Movies;
use App\User;
use App\Showtimes;

use Illuminate\Http\Request;

class StatusController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function activateDeactivate(Request $request)
    {

        $id = $request['id'];
        $table = $request['table'];



        if ($table == "movies") {

            $table = Movies::find($id);
            if ($table->status == 1) {
                $table->status = 0;
            }

            else {
                $table->status = 1;
            }
            $table->update();
        }

        if ($table == "master_user") {

            $table = User::find($id);
            if ($table->status == 1) {
                $table->status = 0;
            }

            else {
                $table->status = 1;
            }
            $table->update();
        }

        if ($table == "showtimes") {

            $table = Showtimes::find($id);
            if ($table->status == 1) {
                $table->status = 0;
            }

            else {
                $table->status = 1;
            }
            $table->update();
        }

    }
}
