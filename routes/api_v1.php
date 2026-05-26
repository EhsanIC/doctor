<?php

use App\Http\Controllers\Admin\AdminPanelDoctorManagement;
use App\Http\Controllers\Admin\AdminPanelDoctorProfileController;
use App\Http\Controllers\Auth\Doctors\DoctorRegisterContoller;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Doctors\DoctorAppointmentController;
use App\Http\Controllers\Doctors\DoctorProfileController;
use App\Http\Controllers\Patient\PatientController;
use Illuminate\Support\Facades\Route;

Route::get('/hello', function () {
    return response()->json([
        'message' => 'Hello API V1'
    ]);
});

// INFO: route for admin access
// TODO: add middlwear
Route::prefix('/admin/doctors')->middleware('auth:sanctum' , 'role:admin')->group(function () {
    Route::apiResource('', AdminPanelDoctorManagement::class)->only(['index' , 'update']);
    Route::apiResource('/profile' , AdminPanelDoctorProfileController::class)->only(['index' , 'update']);
});


// INFO: route for doctor role
Route::prefix('/doctor')->group(function() {

    // INFO: doctor register
    Route::post('/doctor/register' , [DoctorRegisterContoller::class , 'registerDoctor']);

    Route::prefix('/doctor')->middleware('auth:sanctum')->group(function() {

        // INFO: doctor profile
        Route::get('/profile/{profile}' , [DoctorProfileController::class , 'show'])->can('view' , 'profile');
        Route::patch('/profile/{profile}' , [DoctorProfileController::class , 'update'])->can('update' , 'profile');

        // INFO: doctor appointment 
        Route::get('/appointment' , [DoctorAppointmentController::class , 'index']);
        Route::patch('/appointment/{appointment}' , [DoctorAppointmentController::class , 'update']);
    });

});



// BUG: not getting redirect to login route 
//  instead getting redirect to home page
Route::prefix('/patient/appointment')->group(function () {
    Route::get('' , [PatientController::class , 'index'])->middleware('auth');
    Route::post('' , [PatientController::class , 'getAppointment']);
});



// INFO: login for every user (admin doctor user)
// INFO: doc is done
Route::post('login', [LoginController::class, 'login']);

// TEST: middleware test  
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/test' , function() {
        return 'hello';
    });
});

// TODO: add swagger DOCs for all routes 
// TODO: add resource for all endpoints
// TODO: complite resteraction
// FIX: role can have access to there routes

