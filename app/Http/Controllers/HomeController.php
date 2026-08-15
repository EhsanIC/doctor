<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

class HomeController extends Controller
{
    #[OA\Get(
        path: '/api/v1/hello',
        operationId: 'hello',
        summary: 'API root / health check',
        description: 'Returns a simple greeting to confirm the API is up.',
        tags: ['General'],
        responses: [
            new OA\Response(response: 200, description: 'Hello message', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'message', type: 'string', example: 'Hello API V1')]
            )),
        ]
    )]
    public function hello()
    {
        return response()->json([
            'message' => 'Hello API V1'
        ]);
    }

    #[OA\Get(
        path: '/api/v1/test',
        operationId: 'test',
        summary: 'Authenticated test endpoint',
        description: 'Returns a plain "hello" string. Used to verify Sanctum authentication is working.',
        tags: ['General'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Test response', content: new OA\MediaType(
                mediaType: 'text/plain',
                schema: new OA\Schema(type: 'string', example: 'hello')
            )),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
        ]
    )]
    public function test()
    {
        return 'hello';
    }
}
