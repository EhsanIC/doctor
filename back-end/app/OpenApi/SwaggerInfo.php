<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Doctor Management & Appointment Booking API',
    description: "REST API for the Doctor Management and Simple Appointment Booking System.\n\nRoles: Management (admin), Doctor, Regular user (patient).\n\nAuthentication uses Laravel Sanctum bearer tokens. Obtain a token from POST /api/v1/login, then send it as `Authorization: Bearer <token>`.",
    contact: new OA\Contact(name: 'Support', email: 'mebrahimi405@yahoo.com'),
)]

#[OA\Server(url: 'http://127.0.0.1:8000', description: 'Local development')]

#[OA\Tag(name: 'Auth', description: 'Authentication and registration')]
#[OA\Tag(name: 'Admin', description: 'Management (admin) operations')]
#[OA\Tag(name: 'Doctor', description: 'Doctor operations')]
#[OA\Tag(name: 'Patient', description: 'Regular user (patient) operations')]
#[OA\Tag(name: 'General', description: 'Utility and health-check endpoints')]

#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Laravel Sanctum token. Enter the token returned by /api/v1/login in the format "Bearer <token>".'
)]

// Reusable responses (defined once, referenced everywhere)
#[OA\Response(response: 'Unauthorized', description: 'Unauthenticated - missing or invalid token.', content: new OA\JsonContent(
    properties: [new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.')]
))]
#[OA\Response(response: 'Forbidden', description: 'Forbidden - the authenticated user does not have the required role/permission.', content: new OA\JsonContent(
    properties: [new OA\Property(property: 'message', type: 'string', example: 'Unauthorized action.')]
))]
#[OA\Response(response: 'NotFound', description: 'Resource not found.', content: new OA\JsonContent(
    properties: [new OA\Property(property: 'message', type: 'string', example: 'No query results for model.')]
))]
#[OA\Response(response: 'ValidationError', description: 'Validation failed.', content: new OA\JsonContent(
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
        new OA\Property(property: 'errors', type: 'object', example: ['email' => ['The email field is required.']]),
    ]
))]

class SwaggerInfo {}
