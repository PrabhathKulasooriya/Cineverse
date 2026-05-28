<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecurityController extends Controller
{

    public function login(Request $request)
    {
        

        $this->validate($request, ['email' => 'required|email', 'password' => 'required|min:3']);

        $advanceEncryption=(new  \App\MyResources\AdvanceEncryption($request->get('password'),"Nova6566",256));


        $user = User::where('email', $request->get('email'))->where('password',$advanceEncryption->encrypt())->exists();
        if ($user==true){
            $userData=User::where('email', $request->get('email'))->where('password',$advanceEncryption->encrypt())->first();

            if ($userData->status==1){
                Auth::login($userData);
                return redirect()->intended('/');
            }

            else if($userData->status==0){
                return back()->with('warning', 'User has been suspended! Contact your System Administrator.');
            }

        }

        else{
            return back()->with('error', 'Incorrect login details! Check email and Password');
        }

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
