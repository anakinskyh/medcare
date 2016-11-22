<?php

namespace App\Http\Controllers;

use App\Http\Controllers\scheduleMgt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\File;
use League\Flysystem\Exception;

class apptMgt extends Controller
{
    //
    public function demo(){
        $results = DB::select( DB::raw("SELECT * FROM Appointment") );
        return $results;
    }

    //

    public function showApptListByDoctorID(Request $request){
        $validator = Validator::make($request->all(),[
            'doctor_id'=>'required|exists:Staff,id',
            'start'=>'required|'.Carbon::now(),
            'end'=>'required|'.Carbon::now(),
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $input = $request->all();
        $result = DB::table('Patient')
            ->join('Appointment','Patient.id','=','Appointment.patient_id')
            ->select('Appointment.*','Staff.*')
            ->where('Appointment.start','>=',$input['start'])
            ->where('Appointment.patient_id','=',$input['patient_id'])
            ->get();

        return responst()->json($result);
    }//success

    public function getAnAppt(Request $request){
        $validator = Validator::make($request->all(),[
            'appointment_id'=>'required|exists:Appointment,id'
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $input = $request->all();
        $result = DB::select(DB::raw(
            'SELECT Appointment.syntom,
              Appointment.start,
              CONCAT(Staff.firstname,Staff.lastname) as doctor
              CONCAT(Patient.firstname,Patient.lastname) as patient
            FROM Appoinrment
            INNER JOIN Staff
            ON Staff.id=Appointment.doctor_id
            INNER JOIN Patient
            ON Patient.id=Appointment.patient_id
            WHERE Appointment.id = :appointment.id'
        ),$input);

        return $result;
    }//

    public function showApptListByPatientID(Request $request){
        $validator = Validator::make($request->all(),[
            'patient_id'=>'required|exists:Patient,id',
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $input = $request->all();
        $input['start'] = new DateTime();//Carbon::now();
        //return response()->json($input);
        $result = DB::table('Staff')
            ->join('Appointment','Staff.id','=','Appointment.doctor_id')
            ->select('Appointment.*','Staff.*')
            ->where('Appointment.start','>=',$input['start'])
            ->where('Appointment.patient_id','=',$input['patient_id'])
            ->get();

        return response()->json($result);
    }//success

    public function deleteAppt(Request $request){

        $validator = Validator::make($request->all(),[
            'apptID'=>'required|exists:Appointment,id'
        ]);

        if($validator->fails())
            return $validator->errors();

        $var = $request->all();
        $result = DB::select(DB::raw("SELECT FROM Appointment WHERE id = :apptID"),$var );

        return $result;
    }//success

    //Edit
    public function showEditAppt(Request $request){

        $validator = Validator::make($request->all(),[
            'apptID'=>'required|exists:Appointment,id'
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $var = $request->all();
        $result = DB::select(DB::raw("SELECT * FROM Appointment WHERE id = :apptID"),$var );

        return response()->json($result);
    }//success
    public function submitEditAppt(Request $request){
        $validator = Validator::make($request->all(),[
            'apptID'=>'required|exists:Appointment,id',
            'syntom'=>'required',
            'start'=>'required|after:'.date_timestamp_get(),
            'patient_id'=>'required|exists:Patient,id',
            'doctor_id'=>'required|exists:Staff,id',
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        return response()->json(['message' => 'Request completed']);
    }//success
    public function confirmEditAppt(Request $request){

        $validator = Validator::make($request->all(),[
            'apptID'=>'required|exists:Appointment,id',
            'syntom'=>'required',
            'start'=>'required|after:'.date_timestamp_get(),
            'patient_id'=>'required|exists:Patient,id',
            'doctor_id'=>'required|exists:Staff,id',
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $var = $request->all();
        $result = DB::table('Appointment')
            ->where('id',$var['apptID'])
            ->update($var);

        return response()->json($result);
    }//success

    public function submitAppt(Request $request){
        $validator = Validator::make($request->all(),[
            'syntom'=>'required|max:255',
            'start'=>'required|'.Carbon::now(),
            'end'=>'required|'.Carbon::now(),
            'patient_id'=>'required|exists:Patient,id',
            'doctor_id'=>'required|exists:Staff,id'
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        return response()->json(['message' => 'Request completed']);
    }//success
    public function confirmAppt(Request $request){
        $now = new DateTime();

        //return response()->json($request->all());

        $validator = Validator::make($request->all(),[
            'syntom'=>'required|max:255',
            'start'=>'required',
            'end'=>'required',
            'patient_id'=>'required|exists:Patient,id',
            'name' => 'required'
        ]);



        if($validator->fails())
            return $validator->errors()->all();

        $name = explode(' ',$request->only('name')['name']);
        $input1 = array(
            'firstname'=>$name[0],
            'lastname'=>$name[1]
        );

        //return response()->json($input1);

        $to_mail = array(
            'patient_id'=>$request->all()['patient_id'],
            'datetime'=>$request->all()['start'],
        );

        try {
            $db = DB::select('
            SELECT *
            FROM Staff
            WHERE firstname = :firstname
            AND lastname = :lastname
            ', $input1);

            if(sizeof($db)==0)
                return response()->json(['status' => $db]);

            $to_mail['doctor_id']=$db[0]->id;
            $input = $request->all();
            unset($input['name']);

            if(!app('App\Http\Controllers\scheduleMgt')
                ->isAvailableDateTime($db[0]->id
                    ,$request->all()['start'])){
                return response()->json(['status' => 'bad',
                    'error' => 'full'
                ]);
            }

            $input['doctor_id'] = $db[0]->id;
            DB::table('Appointment')
                ->insert($input);
        }catch (Exception $exc){
            return response()->json(['status' => 'bad query','error'=>$exc->getMessage()]);
        }

        return app('App\Http\Controllers\emailMgt')->sendtopatient(
            $to_mail['doctor_id'],$to_mail['patient_id'],$to_mail['datetime']
        );

        return response()->json(['status' => 'done']);
    }//success



    // reschedule & update
    public function reschedule($start,$doctor_id){

    }//

    public function confirmApptUpdate(Request $request){

    }//
}
