<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;

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

        $inputEmail = strtolower($request->email);

        // 1. Attempt login with primary email
        if (Auth::attempt(['email' => $inputEmail, 'password' => $request->password, 'status' => 1])) {
            $user = Auth::user();

            if (!$user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
                return redirect()->route('verification.notice');
            }
            
            if ($user->user_role_iduser_role == 4) {
                return redirect()->intended('/')->with('success', 'Login Successful!');
            } else {
                return redirect()->route('dashboard')->with('success', 'Login Successful!');
            }
        }

        // 2. Check if user is trying to log in using their pending_email
        $pendingUser = User::where('pending_email', $inputEmail)->where('status', 1)->first();
        if ($pendingUser && Hash::check($request->password, $pendingUser->password)) {
            Auth::login($pendingUser);
            $pendingUser->sendEmailVerificationNotification();
            return redirect()->route('verification.notice')->with('info', 'Your new email address (' . $pendingUser->pending_email . ') is pending verification.');
        }

        // 3. Check for suspended user
        $user = User::where('email', $inputEmail)->orWhere('pending_email', $inputEmail)->first();
        if ($user && Hash::check($request->password, $user->password) && $user->status == 0) {
            return back()->with('warning', 'User has been suspended! Contact Cineverse Support.');
        }
       
        return back()->with('error', 'Incorrect login details! Check email and Password');
    }



    public function logoutNow(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        return redirect('/');
    }


    public function signup(){

        return view('clientSignup',['title'=>'Sign Up']);
    }


}
