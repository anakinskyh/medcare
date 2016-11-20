<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class healthRecordMgt extends Controller
{
    //view
    public function reqViewHealthRecord(Request $request){

        $validator = Validator::make($request->all(),[
            'hr_id'=>'required|exists:HealthRecord,id',
            'req_id'=>'required|exists:Staff,id'
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $input = $request->all();
        DB::table('HRAccessPermission')
            ->insert($input);
    }
    public function acceptToAccessHR(Request $request){

        $validator = Validator::make($request->all(),[
            'hr_id'=>'required|exists:HealthRecord,id',
            'req_id'=>'required|exists:Staff,id',
            'grantor_id'=>'required'
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $input = $request->all();
        $input['granted_date']=Carbon::now();
        $input['valid']=1;

        DB::table('HRAccessPermission')
            ->where('hr_id',$input['hr_id'])
            ->where('req_id',$input['req_id'])
            ->update($input);
    }

    public function editHealthRecord(Request $request){
        $validator = Validator::make($request->all(),[
            'appointment_id'=>'required|exists:Appointment,id'
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $validator = Validator::make($request->all(),[
            'appointment_id'=>'exists:HealthRecord'
        ]);

        $input = $request->all();

        if($validator->fails())
            DB::table('HealthRecord')
                ->insert($input);

        $patient = DB::select(DB::raw('
            SELECT patient_id,firstname,lastname
            FROM Appointment
            INNER JOIN Patient
            ON Appointment.patient_id == Patient.id
            WHERE Appointment.id == :appointment_id')
            ,$input);

        $data = DB::select(DB::raw('SELECT *
            FROM HealthRecord
            WHERE appointment_id = :appointment_id
            ')
            ,$input);

        $result = array(
            'patient'=>$patient,
            'data'=>$data,
        );
    }
    public function submitEditHealthRecord(Request $request){
        $validator = Validator::make($request->all(),[
            'appointment_id'=>'required|exists:HealthRecord',
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        return response('Hello World', 200)
            ->header('Content-Type', 'text/plain');
    }
    public function confirmEditHealthRecord(Request $request){
        $validator = Validator::make($request->all(),[
            'appointment_id'=>'required|exists:HealthRecord',
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $input = $request->all();
        DB::table('HealthRecord')
            ->where('appointment_id',$input['appointment_id'])
            ->update($input);

        return response('Hello World', 200)
            ->header('Content-Type', 'text/plain');
    }
}
