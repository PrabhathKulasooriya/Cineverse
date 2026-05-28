<?php


namespace App\Http\Controllers;


use App\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(){

        $users = User::whereIn('user_role_iduser_role', [1,2,3])->get(); 

        return view('employee_management.employeeManagement',['title'=>'Employee Management', 'users'=>$users]);
    }



//Save User Start
    public function saveUser(Request $request){


        $validator = \Validator::make($request->all(), [

            'userType' => 'required',
            'fName' => 'required|max:115',
            'lName' => 'required|max:115',
            'contactNo' => 'required|max:10|min:10',
            'gender' => 'required',
            'dob' => 'required',
            'email' => 'required',
            'password' => 'required|min:6',

        ], [

            'userType.required' => 'User Type should be provided!',

            'fName.required' => 'First Name should be provided!',
            'fName.max' => 'First Name must be less than 115 characters.',

            'lName.required' => 'Last Name should be provided!',
            'lName.max' => 'Last Name must be less than 115 characters.',

            'contactNo.required' => 'Contact No should be provided!',
            'contactNo.max' => 'Contact No must be include 10 numbers.',
            'contactNo.min' => 'Contact No must be include 10 numbers.',

            'gender.required' => 'Gender should be provided!',

            'dob.required' => 'DOB should be provided!',

            'email.required' => 'Email should be provided!',

            'password.required' => 'Password should be provided.',
            'password.min' => 'Password must be include minimum 6 characters.',


        ]);

        if ($validator->fails()) {
            return response()->json(['errors' =>$validator->errors()]);
        }


        $advanceEncryption = (new  \App\MyResources\AdvanceEncryption($request['password'],"Nova6566", 256));

        $saveUser = new User();

        $saveUser->first_name = strtoupper($request['fName']);
        $saveUser->last_name = strtoupper($request['lName']);
        $saveUser->contact_number = $request['contactNo'];
        $saveUser->gender = $request['gender'];
        $saveUser->dob = $request['dob'];
        $saveUser->email=strtolower($request['email']);
        $saveUser->password = $advanceEncryption->encrypt();
        $saveUser->status = 1;
        $saveUser->user_role_iduser_role = $request['userType'];


        $saveUser->save();


        return response()->json(['success' => 'User Saved Successfully.']);

    }
    //Save User End






    //Update User Start
    public function updateUser(Request $request){

        $hiddenUserId = $request['hiddenUserId'];
        $firstName = $request['firstName'];
        $lastName = $request['lastName'];
        $contactNo = $request['contactNo'];
        $dob = $request['dob'];
        $email = $request['email'];
        $gender = $request['gender'];
        $userRole = $request['userRole'];


        //Validation
        $validator = \Validator::make($request->all(), [

            'firstName' => 'required|max:115',
            'lastName' => 'required|max:115',
            'contactNo' => 'required|max:10|min:10',
            'dob' => 'required',
            'email' => 'required',
            'gender' => 'required',
            'userType' => 'required',

        ], [
            'firstName.required' => 'First Name should be provided!',
            'firstName.max' => 'First Name must be less than 115 characters.',

            'lastName.required' => 'Last Name should be provided!',
            'lastName.max' => 'Last Name must be less than 115 characters.',

            'contactNo.required' => 'Contact No should be provided!',
            'contactNo.max' => 'Contact No must be include 10 numbers.',
            'contactNo.min' => 'Contact No must be include 10 numbers.',

            'dob.required' => 'DOB should be provided!',

            'email.required' => 'Email should be provided!',

            'gender.required' => 'Gender should be provided!',

            'userType.required' => 'User Role should be provided!',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }



        $update = User::find($hiddenUserId);

        $update->first_name=strtoupper($firstName);
        $update->last_name=strtoupper($lastName);
        $update->contact_number=$contactNo;
        $update->dob=$dob;
        $update->email=strtolower($email);
        $update->gender=$gender;
        $update->user_role_iduser_role = $request['userType'];

        $update->save();

        return response()->json(['success'=>'User Updated']);
    }
//Update User End







}