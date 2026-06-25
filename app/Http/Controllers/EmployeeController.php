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
            'contactNo' => 'required|max:10|min:10',
            'email'     => 'required',
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
            'email.required'     => 'Email should be provided!',
            'password.required'  => 'Password should be provided.',
            'password.min'       => 'Password must include minimum 6 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }


        $saveUser = new User();
        $saveUser->first_name            = strtoupper($request['fName']);
        $saveUser->last_name             = strtoupper($request['lName']);
        $saveUser->contact_number        = $request['contactNo'];
        $saveUser->email                 = strtolower($request['email']);
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
            'contactNo' => 'required|max:10|min:10',
            'email'     => 'required',
            'userType'  => 'required',
        ], [
            'firstName.required' => 'First Name should be provided!',
            'firstName.max'      => 'First Name must be less than 115 characters.',
            'lastName.required'  => 'Last Name should be provided!',
            'lastName.max'       => 'Last Name must be less than 115 characters.',
            'contactNo.required' => 'Contact No should be provided!',
            'contactNo.max'      => 'Contact No must include 10 numbers.',
            'contactNo.min'      => 'Contact No must include 10 numbers.',
            'email.required'     => 'Email should be provided!',
            'userType.required'  => 'User Role should be provided!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $update = User::find($request['hiddenUserId']);
        $update->first_name            = strtoupper($request['firstName']);
        $update->last_name             = strtoupper($request['lastName']);
        $update->contact_number        = $request['contactNo'];
        $update->email                 = strtolower($request['email']);
        $update->user_role_iduser_role = $request['userType'];
        $update->save();

        return response()->json(['success' => 'User Updated']);
    }
}