<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDoctorStatusRequest;
use App\Http\Resources\AdminPanelDoctorsManagementResource;
use App\Http\Resources\DoctorProfileResource;
use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Http\Request;
use PhpParser\Comment\Doc;

class AdminPanelDoctorManagement extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = User::role('doctor')
            ->with('doctorProfile')
            ->get();

        return AdminPanelDoctorsManagementResource::collection($doctors);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoctorStatusRequest $request, DoctorProfile $doctor)
    {
        $newStatus = $request->validated('status');

        $doctor->update([
            'status' => $newStatus
        ]);

        return new DoctorProfileResource($doctor);
    }
}
