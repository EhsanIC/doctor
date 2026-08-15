<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSpecialtyRequest;
use App\Http\Requests\UpdateSpecialtyRequest;
use App\Http\Resources\SpecialtyResource;
use App\Models\Specialty;
use OpenApi\Attributes as OA;

class SpecialtyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: '/api/v1/admin/specialties',
        operationId: 'adminListSpecialties',
        summary: 'List all specialties (paginated)',
        tags: ['Admin'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Paginated list of specialties', content: new OA\JsonContent(ref: '#/components/schemas/PaginatedSpecialty')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
        ]
    )]
    public function index()
    {
        return SpecialtyResource::collection(Specialty::paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    #[OA\Post(
        path: '/api/v1/admin/specialties',
        operationId: 'adminStoreSpecialty',
        summary: 'Add a specialty',
        tags: ['Admin'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/StoreSpecialtyRequest')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Created specialty', content: new OA\JsonContent(ref: '#/components/schemas/Specialty')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
        ]
    )]
    public function store(StoreSpecialtyRequest $request)
    {
        $specialty = Specialty::create($request->validated());

        return (new SpecialtyResource($specialty))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    #[OA\Get(
        path: '/api/v1/admin/specialties/{specialty}',
        operationId: 'adminShowSpecialty',
        summary: 'Show a specialty',
        tags: ['Admin'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\PathParameter(name: 'specialty', description: 'Specialty ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Specialty', content: new OA\JsonContent(ref: '#/components/schemas/Specialty')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
        ]
    )]
    public function show(Specialty $specialty)
    {
        return new SpecialtyResource($specialty);
    }

    /**
     * Update the specified resource in storage.
     */
    #[OA\Patch(
        path: '/api/v1/admin/specialties/{specialty}',
        operationId: 'adminUpdateSpecialty',
        summary: 'Edit a specialty',
        tags: ['Admin'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\PathParameter(name: 'specialty', description: 'Specialty ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateSpecialtyRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Updated specialty', content: new OA\JsonContent(ref: '#/components/schemas/Specialty')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
        ]
    )]
    #[OA\Put(
        path: '/api/v1/admin/specialties/{specialty}',
        operationId: 'adminUpdateSpecialtyPut',
        summary: 'Edit a specialty (PUT)',
        tags: ['Admin'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\PathParameter(name: 'specialty', description: 'Specialty ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateSpecialtyRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Updated specialty', content: new OA\JsonContent(ref: '#/components/schemas/Specialty')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
        ]
    )]
    public function update(UpdateSpecialtyRequest $request, Specialty $specialty)
    {
        $specialty->update($request->validated());

        return new SpecialtyResource($specialty);
    }

    /**
     * Remove the specified resource from storage.
     */
    #[OA\Delete(
        path: '/api/v1/admin/specialties/{specialty}',
        operationId: 'adminDeleteSpecialty',
        summary: 'Delete a specialty (soft delete)',
        tags: ['Admin'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\PathParameter(name: 'specialty', description: 'Specialty ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Specialty deleted'),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
        ]
    )]
    public function destroy(Specialty $specialty)
    {
        $specialty->delete();

        return response()->noContent();
    }
}
