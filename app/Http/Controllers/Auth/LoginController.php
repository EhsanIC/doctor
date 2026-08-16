<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class LoginController extends Controller
{
    #[OA\Post(
        path: '/api/v1/login',
        operationId: 'login',
        summary: 'Login',
        description: 'Authenticate a user (admin, doctor or patient) and issue a Sanctum token.',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'User credentials',
            content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Login successful', content: new OA\JsonContent(ref: '#/components/schemas/LoginResponse')),
            new OA\Response(response: 401, description: 'Invalid credentials', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'message', type: 'string', example: 'Invalid credentials.')]
            )),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
        ]
    )]
    public function login(Request $request)
    {

        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (! $user || ! Hash::check($fields['password'], $user->password)) {
            return response(['message' => 'اطلاعات وارد شده اشتباه است'], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    #[OA\Post(
        path: '/api/v1/logout',
        operationId: 'logout',
        summary: 'Logout',
        description: 'Revoke the current access token and log the user out.',
        tags: ['Auth'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 204, description: 'Logged out successfully'),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
        ]
    )]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
