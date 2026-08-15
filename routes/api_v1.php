<?php

use App\Http\Controllers\Admin\AdminPanelDoctorManagement;
use App\Http\Controllers\Admin\AdminPanelDoctorProfileController;
use App\Http\Controllers\Admin\SpecialtyController;
use App\Http\Controllers\Auth\Doctors\DoctorRegisterContoller;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PatientRegisterController;
use App\Http\Controllers\Doctors\DoctorAppointmentController;
use App\Http\Controllers\Doctors\DoctorProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Patient\PatientController;
use Illuminate\Support\Facades\Route;

Route::get('/hello', [HomeController::class, 'hello']);

// INFO: route for admin access
// TODO: add middlwear
Route::prefix('/admin/doctors')->middleware('auth:sanctum' , 'role:admin')->group(function () {
    Route::apiResource('', AdminPanelDoctorManagement::class)->only(['index' , 'update']);
    Route::apiResource('/profile' , AdminPanelDoctorProfileController::class)->only(['index' , 'update']);
});

// INFO: specialty management (admin only)
Route::apiResource('admin/specialties', SpecialtyController::class)
    ->middleware(['auth:sanctum', 'role:admin']);


// INFO: routes for doctor role
Route::post('/doctor/register' , [DoctorRegisterContoller::class , 'registerDoctor']);

Route::prefix('/doctor')->middleware('auth:sanctum')->group(function() {

    // INFO: doctor profile
    Route::get('/profile/{profile}' , [DoctorProfileController::class , 'show'])
        ->middleware('permission:profile.view')
        ->can('view' , 'profile');
    Route::patch('/profile/{profile}' , [DoctorProfileController::class , 'update'])
        ->middleware('permission:profile.update')
        ->can('update' , 'profile');

    // INFO: doctor appointment 
    Route::get('/appointment' , [DoctorAppointmentController::class , 'index'])
        ->middleware('permission:appointment.view');
    Route::patch('/appointment/{appointment}' , [DoctorAppointmentController::class , 'update'])
        ->middleware('permission:appointment.pending|appointment.cancel');
});



// BUG: not getting redirect to login route 
//  instead getting redirect to home page
Route::prefix('/patient/appointment')->middleware('auth:sanctum')->group(function () {
    Route::get('' , [PatientController::class , 'index'])->middleware('permission:doctor.view');
    Route::post('' , [PatientController::class , 'getAppointment'])->middleware('permission:appointment.create');
});



// INFO: patient (regular user) register
Route::post('/register', [PatientRegisterController::class, 'register']);

// INFO: login for every user (admin doctor user)
// INFO: doc is done
Route::post('login', [LoginController::class, 'login']);

// TEST: middleware test  
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/test', [HomeController::class, 'test']);
});

// TODO: add swagger DOCs for all routes 
// TODO: add resource for all endpoints
// TODO: complite resteraction
// FIX: role can have access to there routes

