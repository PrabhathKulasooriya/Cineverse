<?php


namespace App\Http\Controllers;


use App\User;
use App\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MyAccountController extends Controller
{
    public function index(){

        $users = Auth::user();

        return view('my_account.myAccount', ['title'=>'My Account', 'users' => $users]);
    }



    public function getUserDetails(Request $request){

        return User::find($request['profile']);

    }





    public function updateUserDetails(Request $request) {


        $validator = \Validator::make($request->all(), [

            'fName' => 'required|max:115',
            'lName' => 'required|max:115',
            'contactNo' => 'required|min:10|max:10',
            'email' => 'required|email',


        ], [
            'fName.required' => 'First Name should be provided!',
            'fName.max' => 'First Name must be less than 115 characters.',

            'lName.required' => 'Last Name should be provided!',
            'lName.max' => 'Last Name must be less than 115 characters.',

            'contactNo.required' => 'Contact No should be provided!',
            'contactNo.max' => 'Contact No must be at most 10 numbers.',
            'contactNo.min' => 'Contact No must be at least 10 numbers.',

            'email.required' => 'Email should be provided!',
            'email.email' => 'Please provide a valid email address!',


        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        // Update User table
        $updateUser = User::find(Auth::user()->idmaster_user);
        
        if(!$updateUser){
            return response()->json(['errors' => 'User not found!']);
        }

        $updateUser->first_name = strtoupper($request['fName']);
        $updateUser->last_name = strtoupper($request['lName']);
        $updateUser->contact_number = $request['contactNo'];
        $updateUser->email = strtolower($request['email']); 
        $updateUser->save();

        
        $updateClient = Client::where('master_user_idmaster_user', Auth::user()->idmaster_user)->first();
        
        if($updateClient) {
            $updateClient->first_name = strtoupper($request['fName']);
            $updateClient->last_name = strtoupper($request['lName']);
            $updateClient->contact_number = $request['contactNo'];
            $updateClient->save();
        }

        return response()->json(['success'=>'']);
    }








//Change Password Start

    public function changePassword(Request $request) {


            $validator = \Validator::make($request->all(), [

                'currentPassword' => 'required',
                'newPassword' => 'required',
                'confirmPassword' => 'required',

            ], [

                'currentPassword.required' => 'Current Password should be provided!',

                'newPassword.required' => 'New Password should be provided!',

                'confirmPassword.required' => 'Confirm Password should be provided!',


            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }


            $advanceEncryption=(new  \App\MyResources\AdvanceEncryption($request->get('newPassword'),"Nova6566",256));

        $changePassword=User::find(Auth::user()->idmaster_user);

        $currentPassword = (new  \App\MyResources\AdvanceEncryption($request->get('currentPassword'),"Nova6566",256));


        if($changePassword->password != $currentPassword->encrypt()){
            return response()->json(['errors' => ['currentPassword' => ['Current Password is not correct!']]]);
        }else{

            $changePassword->password= $advanceEncryption->encrypt();

            $changePassword->save();

            return response()->json(['success'=>'Saved']);
    }
    }
//Change Password End



}