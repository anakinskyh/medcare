<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class userMgt extends Controller
{
    //
    public function submitAddUser(Request $request){
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

        return response()->json(['message' => 'Request completed']);

    }//success

    public function confirmAddUser(Request $request){
        //return 'what';
        //return response()->json($request->all());

        $validator = Validator::make($request->all(),[
            'firstname'=>'required|Between:3,64',
            'lastname'=>'required|Between:3,64',
            'email'=>'required|Email|Between:3,64|Unique:Staff,email',
            'password'=>'required|min:8',
            'tel'=>'required',
            'role'=>'required',
            'department_id'=>'required|exists:Department,id'
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $input = $request->all();
        //$input['password'] = bcrypt($input['password']);
        DB::table('Staff')
            ->insert($input);


        return response()->json(['status'=>'done']);
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
        $result = DB::select( DB::raw('
          SELECT start,syntom,id,firstname,lastname
          FROM Appointment,Patient 
          WHERE start>=:start and start<=:end'),
            $input
        );

        return $result;
    }//success

    public function confirmAddPatient(Request $request){
        $validator = Validator::make($request->all(),[
            'firstname'=>'required|Between:3,64',
            'lastname'=>'required|Between:3,64',
            'email'=>'required|Email|Between:3,64|Unique:Patient,email',
            'password'=>'required|min:8',
            'tel'=>'required',
            'ssn'=>'required|Between:13,13'
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $input = $request->all();
        //$input['password'] = bcrypt($input['password']);
        DB::table('Patient')
            ->insert($input);

        return response()->json(['status'=>'done']);
    }//success

    public function patientEasySignin(Request $request){
        $validator = Validator::make($request->all(),[
            'ssn'=>'required',
            'password'=>'required'
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $input = $request->all();
        //$input['password']=bcrypt($input['password']);
        $result = DB::select(DB::raw('
            SELECT *
            FROM Patient
            WHERE ssn = :ssn
            AND password = :password
        '),$input);

        if(sizeof($result) != 0)
            $result['status']='done';
        else $result['status']='bad';

        return response()->json($result);
    }

    public function staffEasySignin(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required',
            'password'=>'required'
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $input = $request->all();
        //$input['password']=bcrypt($input['password']);
        $result = DB::select(DB::raw('
            SELECT *
            FROM Staff
            WHERE email = :email
            AND password = :password
        '),$input);

        if(sizeof($result) != 0)
            $result['status']='done';
        else $result['status']='bad';

        return response()->json($result);
    }
}
