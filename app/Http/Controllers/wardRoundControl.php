<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class wardRoundMgt extends Controller
{
    //Add
    public function submitAddWR(Request $request){
        $validator = Validator::make($request->all(),[
            'start'=>'required|after:'.Carbon::now(),
            'editor'=>'required',
            'doctor_id'=>'required|exists:Staff,id'
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        return response('Hello World', 200)
            ->header('Content-Type', 'text/plain');
    }
    public function confirmAddWR(Request $request){

        $validator = Validator::make($request->all(),[
            'start'=>'required|after:'.Carbon::now(),
            'end'=>'required',
            'editor'=>'required',
            'doctor_id'=>'required|exists:Staff,id'
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $input = $request->all();
        DB::table('RoundTime')
            ->insert($input);

        return response('Hello World', 200)
            ->header('Content-Type', 'text/plain');
    }

    //Cancel
    public function submitCancelWR(Request $request){
        $validator = Validator::make($request->all(),[
            'start'=>'required|after:'.Carbon::now(),
            'editor'=>'required',
            'doctor_id'=>'required|exists:Staff,id'
        ]);

        if($validator->fails())
            return $validator->errors()->all();


        return response('Hello World', 200)
            ->header('Content-Type', 'text/plain');
    }
    public function confirmCancelWR(Request $request){
        $validator = Validator::make($request->all(),[
            'start'=>'required|after:'.Carbon::now(),
            'editor'=>'required',
            'doctor_id'=>'required|exists:Staff,id'
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $input = $request->all();

        DB::select( DB::raw("
            DELETE FROM RoundTime 
            WHERE doctor_id = :doctor_id
            AND start = :start"),
            $input);


        return response('Hello World', 200)
            ->header('Content-Type', 'text/plain');
    }
}
