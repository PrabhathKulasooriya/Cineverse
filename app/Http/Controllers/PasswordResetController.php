<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\ResetPasswordMail;
use App\User;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot_password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'We could not find an account with that email.');
        }

        $token = Str::random(60);

        
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Insert the new token into the database
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::to($request->email)->send(new ResetPasswordMail($token, $request->email));

        return back()->with('success', 'Password reset link sent to your email!');
    }

    // Show the form where users type their new password
    public function showResetForm($token, Request $request)
    {
        return view('auth.reset_password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    // Validate the token and update the password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed' 
        ]);

        
        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset || Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            return back()->with('error', 'Invalid or expired password reset token. Please request a new one.');
        }


        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save(); 

        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect('/signin')->with('success', 'Your password has been reset successfully! You can now log in.');
    }
}