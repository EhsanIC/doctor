<?php
namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0.0', title: 'Doctor API')]
#[OA\Server(url: 'http://127.0.0.1:8000', description: 'Local')]

// Reusable responses — define once, use everywhere
#[OA\Response(response: 'Unauthorized', description: 'Unauthenticated', content: new OA\JsonContent(
    properties: [new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated')]
))]
#[OA\Response(response: 'NotFound', description: 'Not found', content: new OA\JsonContent(
    properties: [new OA\Property(property: 'message', type: 'string', example: 'Not found')]
))]
#[OA\Response(response: 'ValidationError', description: 'Validation failed', content: new OA\JsonContent(
    properties: [new OA\Property(property: 'errors', type: 'object')]
))]

// Reusable security scheme
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Enter token from /api/v1/login response'
)]

class SwaggerInfo {}