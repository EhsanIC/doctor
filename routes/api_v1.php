<?php

use App\Http\Controllers\Admin\AdminPanelDoctorManagement;
use App\Http\Controllers\Admin\AdminPanelDoctorProfileController;
use App\Http\Controllers\Auth\Doctors\DoctorRegisterContoller;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Doctors\DoctorProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/hello', function () {
    return response()->json([
        'message' => 'Hello API V1'
    ]);
});

Route::apiResource('/admin/doctors', AdminPanelDoctorManagement::class)->only(['index' , 'update']);
Route::apiResource('/admin/doctors/profile' , AdminPanelDoctorProfileController::class)->only(['index' , 'update']);

// register for doctor
Route::post('/doctor/register' , [DoctorRegisterContoller::class , 'registerDoctor']);

// doctor profile
    // TODO : add middleware for docs
Route::middleware('auth')->group(function() {
    Route::get('/doctor/profile/{profile}' , [DoctorProfileController::class , 'show']);
    Route::patch('/doctor/profile/{profile}' , [DoctorProfileController::class , 'update']);
});






// login for every user (admin doctor user)
Route::post('login', [LoginController::class, 'login']);

// middleware test  
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin' , function() {
        return 'hello';
    });
});