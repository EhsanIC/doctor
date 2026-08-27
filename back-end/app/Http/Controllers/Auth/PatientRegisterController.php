<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\PatientRegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class PatientRegisterController extends Controller
{
    #[OA\Post(
        path: '/api/v1/register',
        operationId: 'registerPatient',
        summary: 'Register a new patient (regular user)',
        description: "Create a regular user account with the 'patient' role. No approval is required - the user is logged in immediately.",
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PatientRegisterRequest')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Patient registered and logged in', content: new OA\JsonContent(ref: '#/components/schemas/PatientRegisterResponse')),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
        ]
    )]
    public function register(PatientRegisterRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $user->assignRole('patient');

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Registration successful.',
            'user' => $user->load('roles'),
        ], 201);
    }
}
