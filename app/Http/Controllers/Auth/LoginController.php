<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use OpenApi\Attributes as OA;

class LoginController extends Controller
{

    #[OA\Post(
        path: '/api/v1/login',
        summary: 'Login',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest',
            example: ['email' => 'admin@test.com', 'password' => 'password'] )  // reuse schema
        ),
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'), // reuse response
        ]
    )]
    
    public function login(Request $request) {

        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);


        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['message' => 'اطلاعات وارد شده اشتباه است'], 401);
        }

        


        $token = $user->createToken('myapptoken')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ], 201);
    }
}
