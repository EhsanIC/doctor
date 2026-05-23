<?php

namespace App\Http\Controllers\Doctors;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDoctorProfileRequest;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;

class DoctorProfileController extends Controller
{

    public function show(DoctorProfile $profile)
    {
        $profile->load('user');
        return response()->json($profile->load('user'));
    }

    public function update(UpdateDoctorProfileRequest $request , DoctorProfile $profile) 
    {
        $profile->update($request->validated());
        return response()->json($profile->load('user'));
    }

}
