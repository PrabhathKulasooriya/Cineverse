<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class EmployeeController extends Controller
{
    public function index()
    {
        $users = User::whereIn('user_role_iduser_role', [1, 2, 3])->get();
        return view('employee_management.employeeManagement', [
            'title' => 'Employee Management',
            'users' => $users
        ]);
    }


    // Save User
    public function saveUser(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'userType'  => 'required',
            'fName'     => 'required|max:115',
            'lName'     => 'required|max:115',
            'contactNo' => 'required|max:10|min:10|regex:/^07\d{8}$/',
            'email'     => 'required|email',
            'password'  => 'required|min:6',
        ], [
            'userType.required'  => 'User Type should be provided!',
            'fName.required'     => 'First Name should be provided!',
            'fName.max'          => 'First Name must be less than 115 characters.',
            'lName.required'     => 'Last Name should be provided!',
            'lName.max'          => 'Last Name must be less than 115 characters.',
            'contactNo.required' => 'Contact No should be provided!',
            'contactNo.max'      => 'Contact No must include 10 numbers.',
            'contactNo.min'      => 'Contact No must include 10 numbers.',
            'contactNo.regex'    => 'Enter a valid phone number (e.g. 07XXXXXXXX).',
            'email.required'     => 'Email should be provided!',
            'email.email'        => 'Please provide a valid email address!',
            'password.required'  => 'Password should be provided.',
            'password.min'       => 'Password must include minimum 6 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $email = strtolower($request['email']);
        $existingUser = User::where('email', $email)->orWhere('pending_email', $email)->first();
        if ($existingUser) {
            return response()->json(['errors' => ['email' => ['This email address is already registered!']]]);
        }

        $saveUser = new User();
        $saveUser->first_name            = strtoupper($request['fName']);
        $saveUser->last_name             = strtoupper($request['lName']);
        $saveUser->contact_number        = $request['contactNo'];
        $saveUser->email                 = $email;
        $saveUser->password              = Hash::make($request['password']);
        $saveUser->status                = 1;
        $saveUser->user_role_iduser_role = $request['userType'];
        $saveUser->save();

        return response()->json(['success' => 'User Saved Successfully.']);
    }


    // Update User
    public function updateUser(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'firstName' => 'required|max:115',
            'lastName'  => 'required|max:115',
            'contactNo' => 'required|max:10|min:10|regex:/^07\d{8}$/',
            'email'     => 'required|email',
            'userType'  => 'required',
        ], [
            'firstName.required' => 'First Name should be provided!',
            'firstName.max'      => 'First Name must be less than 115 characters.',
            'lastName.required'  => 'Last Name should be provided!',
            'lastName.max'       => 'Last Name must be less than 115 characters.',
            'contactNo.required' => 'Contact No should be provided!',
            'contactNo.max'      => 'Contact No must include 10 numbers.',
            'contactNo.min'      => 'Contact No must include 10 numbers.',
            'contactNo.regex'    => 'Enter a valid phone number (e.g. 07XXXXXXXX).',
            'email.required'     => 'Email should be provided!',
            'email.email'        => 'Please provide a valid email address!',
            'userType.required'  => 'User Role should be provided!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $update = User::find($request['hiddenUserId']);

        if(!$update){
            return response()->json(['errors' => ['general' => ['User Not Found!']]]);
        }

        $oldEmail = strtolower($update->email);
        $newEmail = strtolower($request['email']);

        if($oldEmail != $newEmail){
            $existingUser = User::where('idmaster_user', '!=', $update->idmaster_user)
                ->where(function ($query) use ($newEmail) {
                    $query->where('email', $newEmail)
                          ->orWhere('pending_email', $newEmail);
                })->exists();

            if ($existingUser) {
                return response()->json(['errors' => ['email' => ['This email address is already registered!']]]);
            }

            $update->email_verified_at = null;
        }

        $update->first_name            = strtoupper($request['firstName']);
        $update->last_name             = strtoupper($request['lastName']);
        $update->contact_number        = $request['contactNo'];
        $update->email                 = $newEmail;
        $update->user_role_iduser_role = $request['userType'];
        $update->save();

        return response()->json(['success' => 'User Updated Successfully !']);
    }
}