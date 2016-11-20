<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class scheduleMgt extends Controller
{
    //get doctor_id or dep_id
    public function getAvailableDateTime(Request $request){

        $validator = Validator::make($request->all(),[
            'start'=>'required|after:'.Carbon::now(),
            'end'=>'required',
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $input = $request->all();

        $result = array();

        if(array_key_exists('doctor_id',$input)){
            $result_ = $this->getAvailableDateTimeFromDoctor(
                $input['doctor_id'],$input['start'],$input['end']
            );
            $result[] = $result_;

        }else if(array_key_exists('dep_id',$input)){
            $all_doctor_id = DB::select(DB::raw(
                'SELECT id FROM Staff
                 WHERE department_id = :dep_id
                 AND role = doctor'
                ),$input);

            foreach ($all_doctor_id as $value) {
                $result_ = $this->getAvailableDateTimeFromDoctor(
                    $value, $input['start'], $input['end']
                );
                $result[] = $result_;
            }
        }else{
        }

        return $result;
    }

    public function getAvailableDateTimeFromDoctor($doctor_id,$start,$end){
        $input = array('doctor_id'=>$doctor_id,
            'start'=>$start,
            'end'=>$end,
        );

        $name = DB::select(DB::raw("SELECT firstname,lastname 
          FROM Staff 
          WHERE id = :doctor_id"),$input);

        //get unavailable
        $count = DB::select(DB::raw('SELECT start,Count(*) as Count
            FROM Appointment 
            WHERE doctor_id = :doctor_id
            AND start>=:start
            AND start<=:end
            GROUP BY start'),$input);

        $unavailable = array();
        foreach ($count as $key => $value){
            if($value == 15)
                $unavailable[] = $key;
        }

        //get all wr
        $allWR = DB::select(DB::raw('SELECT start
            FROM RoundTime
            WHERE doctor_id = :doctor_id
            AND start>=:start
            AND start<=:end'),$input);

        $available = array_diff($allWR,$unavailable);

        $result = array(
            'doctor_id'=>$doctor_id,
            'name'=>$name,
            'available'=>$available
        );

        return $result;
    }

    public function isAvailableDateTime($doctor_id,$start){
        $input = array(
            'doctor_id'=>$doctor_id,
            'start'=>$start
        );

        //get unavailable
        $count = DB::select(DB::raw('SELECT Count(*) as Count
            FROM Appointment 
            WHERE doctor_id = :doctor_id
            AND start==:start'),$input);

        /*
        $patient_num = $count[0]
        */
    }
}
