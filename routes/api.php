<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::get('/v1/sayhidb',function(){

    $results = DB::select( DB::raw("SELECT * FROM Appointment") );
    return $results;
});
Route::post('/v1/sayhi',function (){
    return response()->json("Hi, I'm api.");
});
Route::get('/v1/sayhi',function (){
    return "Hi, I'm api.";
});

Route::get('/v1/demoEmail','demo@demo');

Route::post('/v1/sendtopatient','emailMgt@demo');

Route::post('/v1/showEditAppt','apptMgt@showEditAppt');

//scheduleMgt
Route::post('/v1/getAvailableDatetimeList','scheduleMgt@getAvailableDateTime');

//userMgt
Route::post('/v1/confirmadduser','userMgt@confirmAddUser');

//signup
Route::post('/v1/signup','userMgt@confirmAddPatient');

//signin
Route::post('/v1/peasysignin','userMgt@patientEasySignin');
Route::post('/v1/seasysignin','userMgt@staffEasySignin');


//apptMgt
Route::post('/v1/getpatientattplist','apptMgt@showApptListByPatientID');
Route::post('/v1/getdoctorattplist','apptMgt@showApptListByDoctorID');
Route::post('/v1/confirmappt','apptMgt@confirmAppt');

//etc
Route::post('/v1/getdoctorname','etc@getDoctorNameByDepname');
Route::post('/v1/getdepartment','etc@getDepartment');