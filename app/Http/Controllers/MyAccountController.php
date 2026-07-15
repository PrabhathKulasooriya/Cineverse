<?php


namespace App\Http\Controllers;


use App\User;
use App\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        $oldEmail = $updateUser->email;
        $newEmail = strtolower($request['email']);
        $isEmailChanged = $oldEmail != $newEmail;

        $updateUser->email = $newEmail;
        $updateUser->first_name = strtoupper($request['fName']);
        $updateUser->last_name = strtoupper($request['lName']);
        $updateUser->contact_number = $request['contactNo'];

        if($isEmailChanged){
            $updateUser->email_verified_at = null;
        }

        $updateUser->save();

        if($isEmailChanged){
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json(['success'=>'Account Details Updated Successfully! Please verify your email!','isEmailChanged'=>true]);
        }
        

        return response()->json(['success'=>'Account Details Updated Successfully!','isEmailChanged'=>false]);
    }








//Change Password Start

   public function changePassword(Request $request) 
{
    $validator = \Validator::make($request->all(), [
        'currentPassword' => 'required',
        'newPassword' => 'required|min:6|same:confirmPassword', // Added validation
        'confirmPassword' => 'required',
    ], [
        'currentPassword.required' => 'Current Password should be provided!',
        'newPassword.required' => 'New Password should be provided!',
        'newPassword.same' => 'New Password and Confirm Password must match!',
        'confirmPassword.required' => 'Confirm Password should be provided!',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()]);
    }

    $user = Auth::user();

    // 1. Verify the current password using Hash::check
    if (!Hash::check($request['currentPassword'], $user->password)) {
        return response()->json(['errors' => ['currentPassword' => ['Current Password is not correct!']]]);
    }

    // 2. Hash and save the new password using Hash::make
    $user->password = Hash::make($request['newPassword']);
    $user->save();

    return response()->json(['success' => 'Password updated successfully!']);
}
//Change Password End



}