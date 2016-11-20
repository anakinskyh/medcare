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

Route::get('/v1/sayHiDB',function(){

    $results = DB::select( DB::raw("SELECT * FROM Appointment") );
    return $results;
});
Route::get('/v1/demoEmail','demo@demo');

Route::post('/v1/showEditAppt','apptMgt@showEditAppt');

//scheduleMgt
Route::post('/v1/getAvailableDatetimeList','scheduleMgt@getAvailableDateTime');

//userMgt
Route::post('/v1/confirmAddUser','userMgt@confirmAddUser');