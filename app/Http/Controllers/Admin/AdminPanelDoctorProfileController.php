<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDoctorProfileAdminRequest;
use App\Http\Resources\DoctorProfileResource;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use PhpParser\Comment\Doc;
use OpenApi\Attributes as OA;

class AdminPanelDoctorProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: '/api/v1/admin/doctors/profile',
        operationId: 'adminListDoctorProfiles',
        summary: 'List all doctor profiles (paginated)',
        tags: ['Admin'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Paginated list of doctor profiles', content: new OA\JsonContent(ref: '#/components/schemas/PaginatedDoctorProfile')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
        ]
    )]
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
    #[OA\Patch(
        path: '/api/v1/admin/doctors/profile/{profile}',
        operationId: 'adminUpdateDoctorProfile',
        summary: 'Edit a doctor profile',
        tags: ['Admin'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\PathParameter(name: 'profile', description: 'Doctor profile ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateDoctorProfileAdminRequest')
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
        path: '/api/v1/admin/doctors/profile/{profile}',
        operationId: 'adminUpdateDoctorProfilePut',
        summary: 'Edit a doctor profile (PUT)',
        tags: ['Admin'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\PathParameter(name: 'profile', description: 'Doctor profile ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateDoctorProfileAdminRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Updated doctor profile', content: new OA\JsonContent(ref: '#/components/schemas/DoctorProfile')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
        ]
    )]
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


