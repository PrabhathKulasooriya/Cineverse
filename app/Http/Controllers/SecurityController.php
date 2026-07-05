<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class SecurityController extends Controller
{

    public function showLoginPage(){
        if (!session()->has('url.intended')) {
        session(['url.intended' => URL::previous()]);
        }
        
        return view('signin',['title'=>'Sign In']);
    }


    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email', 
            'password' => 'required|min:6' 
        ]);

        
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'status' => 1])) {
            
            $user = Auth::user();

            // Check if verified
            if (!$user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
                return redirect()->route('verification.notice');
            }

            return redirect()->intended('/')->with('success', 'Login Successful!');
        }

        
        $user = User::where('email', $request->email)->first();
        if ($user && $user->status == 0) {
            return back()->with('warning', 'User has been suspended! Contact your System Administrator.');
        }

       
        return back()->with('error', 'Incorrect login details! Check email and Password');
    }



    public function logoutNow(Request $request){
        //Auth::logout();
        $request->session()->invalidate();
        return redirect('/');
    }


    public function signup(){

        return view('clientSignup',['title'=>'Sign Up']);
    }


}
