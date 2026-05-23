<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDoctorProfileAdminRequest;
use App\Http\Resources\DoctorProfileResource;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use PhpParser\Comment\Doc;

class AdminPanelDoctorProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctorProfile = DoctorProfile::paginate();

        return DoctorProfileResource::collection($doctorProfile);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoctorProfileAdminRequest $request, DoctorProfile $profile)
    {
        // dd($doctor->update($request->validated()));
        $data = $request->validated();
        $profile->update($data);
        
        return new DoctorProfileResource($profile);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}


