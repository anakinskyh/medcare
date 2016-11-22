<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class emailMgt extends Controller
{
    public function demo(Request $request){
        $input = $request->all();

        $doctor_id = $input['doctor_id'];
        $patient_id = $input['patient_id'];
        $datetime = $input['datetime'];

        $this->sendtopatient($doctor_id,$patient_id,$datetime);
    }
    //
    public function sendtopatient($doctor_id,$patient_id,$datetime){


        $patient = DB::table('Patient')
            ->where('id','=',$patient_id)
            ->select('*')
            ->get();

        $email = $patient[0]->email;

        $doctor = DB::table('Staff')
            ->where('id','=',$doctor_id)
            ->select('*')
            ->get();

        $doctor_name = $doctor[0]->firstname.' '.$doctor[0]->lastname;

        $department = DB::table('Department')
            ->where('id','=',$doctor[0]->department_id)
            ->select('*')
            ->get();

        $department_name = $department[0]->name;

        $data = array(
            'email'=>$email,
        );
        Mail::send('emails.apptpatient', ['department_name'=>$department_name,'doctor_name'=>$doctor_name
            ,'data'=>$data,'datetime'=>$datetime], function ($message)
            use ($data)
        {
            $message->to($data['email']);
        });

        return response()->json(['status' => 'send mail','data'=>[$email,$doctor_name,$datetime] ]);
    }
}
