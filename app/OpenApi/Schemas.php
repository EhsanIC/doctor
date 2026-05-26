<?php
namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LoginRequest',
    required: ['email', 'password'],
    properties: [
        new OA\Property(property: 'email', type: 'string', example: 'doctor@example.com'),
        new OA\Property(property: 'password', type: 'string', example: 'secret123'),
    ]
)]

#[OA\Schema(
    schema: 'AppointmentRequest',
    required: ['doctor_id', 'date'],
    properties: [
        new OA\Property(property: 'doctor_id', type: 'integer', example: 1),
        new OA\Property(property: 'date', type: 'string', format: 'date', example: '2026-06-01'),
    ]
)]

class Schemas {}