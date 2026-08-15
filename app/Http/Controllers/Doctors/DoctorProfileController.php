<?php

namespace App\Http\Controllers\Doctors;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDoctorProfileRequest;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DoctorProfileController extends Controller
{

    #[OA\Get(
        path: '/api/v1/doctor/profile/{profile}',
        operationId: 'doctorShowProfile',
        summary: 'View own doctor profile',
        tags: ['Doctor'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\PathParameter(name: 'profile', description: 'Doctor profile ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Doctor profile with user', content: new OA\JsonContent(ref: '#/components/schemas/DoctorProfileDetail')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
        ]
    )]
    public function show(DoctorProfile $profile)
    {
        $profile->load('user');
        return response()->json($profile->load('user'));
    }

    #[OA\Patch(
        path: '/api/v1/doctor/profile/{profile}',
        operationId: 'doctorUpdateProfile',
        summary: 'Complete / update own profile',
        tags: ['Doctor'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\PathParameter(name: 'profile', description: 'Doctor profile ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateDoctorProfileRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Updated doctor profile', content: new OA\JsonContent(ref: '#/components/schemas/DoctorProfileDetail')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
        ]
    )]
    public function update(UpdateDoctorProfileRequest $request , DoctorProfile $profile) 
    {
        $profile->update($request->validated());
        return response()->json($profile->load('user'));
    }

}
