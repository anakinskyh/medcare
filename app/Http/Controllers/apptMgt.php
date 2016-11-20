<?php

namespace App\Http\Controllers;

use App\Http\Controllers\scheduleMgt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use scheduleMgt;
use Carbon\Carbon;

class apptMgt extends Controller
{
    //
    public function demo(){
        $results = DB::select( DB::raw("SELECT * FROM Appointment") );
        return $results;
    }

    //
    public function showAppt(Request $request){

    }
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

        return $result;
    }//success?
    public function submitEditAppt(Request $request){
        $validator = Validator::make($request->all(),[
            'apptID'=>'required|exists:Appointment,id',
            'syntom'=>'required',
            'start'=>'required|before:'.date_timestamp_get(),
            'patient_id'=>'required|exists:Patient,id',
            'doctor_id'=>'required|exists:Staff,id',
        ]);

        if($validator->fails())
            return $validator->errors()->all();
        return $validator->getData();

            /*
        $var = $request->all();
        $result = DB::table('Appointment')
            ->where('id',$var['apptID'])
            ->update($var);

        return $result;
            */
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

        return $result;
    }//success

    public function submitAppt(Request $request){
        $validator = Validator::make($request->all(),[
            'syntom'=>'required|max:255',
            'start'=>'required|after:'.date_timestamp_get(),
            'patient_id'=>'required|exists:Patient,id',
            'doctor_id'=>'required|exists:Staff,id'
        ]);

        if($validator->fails())
            return $validator->errors()->all();

        $var = $request->all();
        $result = DB::select(DB::raw("SELECT * FROM Appointment 
          WHERE id = :apptID"),$var );

        return $result;
    }//
    public function confirmAppt(Request $request){}

    public function showApptList(Request $request){}
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
    }

    // reschedule & update
    public function reschedule($start,$doctor_id){

        $available = scheduleMgt::getAvailableDateTimeFromDoctor($doctor_id,$start,);
    }
    public function confirmApptUpdate(Request $request){}
}
