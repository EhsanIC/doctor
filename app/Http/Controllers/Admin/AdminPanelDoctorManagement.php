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
use OpenApi\Attributes as OA;

class AdminPanelDoctorManagement extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: '/api/v1/admin/doctors',
        operationId: 'adminListDoctors',
        summary: 'List all doctors',
        tags: ['Admin'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'List of doctors', content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/DoctorManagementItem')
            )),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
        ]
    )]
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
    #[OA\Patch(
        path: '/api/v1/admin/doctors/{doctor}',
        operationId: 'adminUpdateDoctorStatus',
        summary: 'Approve / reject / deactivate a doctor',
        description: "Change a doctor's status. Use 'active' to approve, 'suspended' to deactivate.",
        tags: ['Admin'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\PathParameter(name: 'doctor', description: 'Doctor profile ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateDoctorStatusRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Updated doctor profile', content: new OA\JsonContent(ref: '#/components/schemas/DoctorProfile')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
        ]
    )]
    #[OA\Put(
        path: '/api/v1/admin/doctors/{doctor}',
        operationId: 'adminUpdateDoctorStatusPut',
        summary: 'Approve / reject / deactivate a doctor (PUT)',
        tags: ['Admin'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\PathParameter(name: 'doctor', description: 'Doctor profile ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateDoctorStatusRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Updated doctor profile', content: new OA\JsonContent(ref: '#/components/schemas/DoctorProfile')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
        ]
    )]
    public function update(UpdateDoctorStatusRequest $request, DoctorProfile $doctor)
    {
        $newStatus = $request->validated('status');

        $doctor->update([
            'status' => $newStatus
        ]);

        return new DoctorProfileResource($doctor);
    }
}
