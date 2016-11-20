<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class userMgt extends Controller
{
    //
    public function submitAddUser(){

    }

    public function confirmAddUser(Request $request){
        $validator = Validator::make($request->all(),[
            'firstname'=>'required|Between:3,64',
            'lastname'=>'required|Between:3,64',
            'email'=>'required|Email|Between:3,64|Unique:Staff,email',
            'password'=>'required|min:8',
            'gender'=>'required',
            'tel'=>'required',
            'role'=>'required',
            'department_id'=>'required|exists:Department,id'
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        DB::table('Staff')
            ->insert($input);

        return response('Hello World', 200)
            ->header('Content-Type', 'text/plain');
    }//success
    public function showScheduleForMed(Request $request){
        $validator = Validator::make($request->all(),[
            'start'=>'required',
            'end'=>'required',
            'doctor_id'=>'required|exists:staff,id',
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $input = $request->all();
        $result = DB::select( DB::raw('SELECT start,syntom,id,firstname,lastname 
          FROM Appointment,Patient 
          WHERE start>=:start and start<=:end'),
            $input
        );

        return $result;
    }
}
