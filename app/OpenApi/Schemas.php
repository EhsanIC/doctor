<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

/*
|--------------------------------------------------------------------------
| Enums
|--------------------------------------------------------------------------
*/
#[OA\Schema(schema: 'DoctorStatus', type: 'string', enum: ['pending', 'active', 'disabled'], description: 'Doctor profile status')]
#[OA\Schema(schema: 'AppointmentStatus', type: 'string', enum: ['pending', 'approved', 'canceled'], description: 'Appointment status')]

/*
|--------------------------------------------------------------------------
| Request schemas
|--------------------------------------------------------------------------
*/
#[OA\Schema(schema: 'LoginRequest', required: ['email', 'password'], properties: [
    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@test.com'),
    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
])]

#[OA\Schema(schema: 'DoctorRegisterRequest', required: ['name', 'email', 'password', 'password_confirmation'], properties: [
    new OA\Property(property: 'name', type: 'string', example: 'Dr. John Doe'),
    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'doctor@example.com'),
    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret123'),
    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'secret123'),
    new OA\Property(property: 'specialty_id', type: 'integer', nullable: true, example: 1, description: 'Optional specialty ID'),
])]

#[OA\Schema(schema: 'PatientRegisterRequest', required: ['name', 'email', 'password', 'password_confirmation'], properties: [
    new OA\Property(property: 'name', type: 'string', example: 'Jane Doe'),
    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'patient@example.com'),
    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret123'),
    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'secret123'),
])]

#[OA\Schema(schema: 'PatientAppointmentRequest', required: ['doctor_id', 'appointment_date', 'appointment_time'], properties: [
    new OA\Property(property: 'doctor_id', type: 'integer', example: 1, description: 'Doctor profile ID'),
    new OA\Property(property: 'appointment_date', type: 'string', format: 'date', example: '2026-08-20'),
    new OA\Property(property: 'appointment_time', type: 'string', example: '14:30'),
    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Sore throat and fever'),
])]

#[OA\Schema(schema: 'UpdateDoctorStatusRequest', required: ['status'], properties: [
    new OA\Property(property: 'status', ref: '#/components/schemas/DoctorStatus', example: 'active'),
])]

#[OA\Schema(schema: 'UpdateAppointmentStatusRequest', required: ['status'], properties: [
    new OA\Property(property: 'status', ref: '#/components/schemas/AppointmentStatus', example: 'approved'),
])]

#[OA\Schema(schema: 'UpdateDoctorProfileRequest', properties: [
    new OA\Property(property: 'specialty_id', type: 'integer', example: 2),
    new OA\Property(property: 'image', type: 'string', format: 'binary', description: 'Image file (jpeg, png, jpg, webp; max 2MB)'),
    new OA\Property(property: 'bio', type: 'string', example: 'Experienced cardiologist with 10+ years.'),
    new OA\Property(property: 'mobile', type: 'string', example: '+989123456789'),
    new OA\Property(property: 'medical_code', type: 'string', example: 'MC-12345'),
    new OA\Property(property: 'address', type: 'string', example: 'Tehran, Valiasr St.'),
    new OA\Property(property: 'working_hours', type: 'string', example: 'Sat-Wed 9:00-17:00'),
])]

#[OA\Schema(schema: 'UpdateDoctorProfileAdminRequest', properties: [
    new OA\Property(property: 'specialty_id', type: 'integer', example: 2),
    new OA\Property(property: 'status', ref: '#/components/schemas/DoctorStatus', example: 'active'),
    new OA\Property(property: 'image', type: 'string', example: 'doctors/avatar.jpg'),
    new OA\Property(property: 'bio', type: 'string', example: 'Updated biography.'),
])]

#[OA\Schema(schema: 'StoreSpecialtyRequest', required: ['name'], properties: [
    new OA\Property(property: 'name', type: 'string', example: 'Cardiology'),
])]

#[OA\Schema(schema: 'UpdateSpecialtyRequest', required: ['name'], properties: [
    new OA\Property(property: 'name', type: 'string', example: 'Cardiology'),
])]

/*
|--------------------------------------------------------------------------
| Model / response schemas
|--------------------------------------------------------------------------
*/
#[OA\Schema(schema: 'User', properties: [
    new OA\Property(property: 'id', type: 'integer', example: 1),
    new OA\Property(property: 'name', type: 'string', example: 'Admin User'),
    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@test.com'),
])]

#[OA\Schema(schema: 'Specialty', properties: [
    new OA\Property(property: 'id', type: 'integer', example: 1),
    new OA\Property(property: 'name', type: 'string', example: 'Cardiology'),
])]

// Doctor profile as returned by the API resources (admin endpoints)
#[OA\Schema(schema: 'DoctorProfile', properties: [
    new OA\Property(property: 'profile_id', type: 'integer', example: 1),
    new OA\Property(property: 'user_id', type: 'integer', example: 2),
    new OA\Property(property: 'specialty_id', type: 'integer', nullable: true, example: 1),
    new OA\Property(property: 'status', ref: '#/components/schemas/DoctorStatus', example: 'active'),
    new OA\Property(property: 'image_url', type: 'string', nullable: true, example: 'doctors/avatar.jpg'),
    new OA\Property(property: 'bio', type: 'string', nullable: true, example: 'Experienced cardiologist.'),
])]

// Raw doctor profile (Eloquent model shape, patient + register endpoints)
#[OA\Schema(schema: 'DoctorProfileFull', properties: [
    new OA\Property(property: 'id', type: 'integer', example: 1),
    new OA\Property(property: 'user_id', type: 'integer', example: 2),
    new OA\Property(property: 'specialty_id', type: 'integer', nullable: true, example: 1),
    new OA\Property(property: 'status', ref: '#/components/schemas/DoctorStatus', example: 'active'),
    new OA\Property(property: 'image', type: 'string', nullable: true, example: 'doctors/avatar.jpg'),
    new OA\Property(property: 'bio', type: 'string', nullable: true, example: 'Experienced cardiologist.'),
    new OA\Property(property: 'mobile', type: 'string', nullable: true, example: '+989123456789'),
    new OA\Property(property: 'medical_code', type: 'string', nullable: true, example: 'MC-12345'),
    new OA\Property(property: 'address', type: 'string', nullable: true, example: 'Tehran, Valiasr St.'),
    new OA\Property(property: 'working_hours', type: 'string', nullable: true, example: 'Sat-Wed 9:00-17:00'),
    new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
])]

// Raw doctor profile with its user relation (doctor show/update endpoints)
#[OA\Schema(schema: 'DoctorProfileDetail', allOf: [
    new OA\Schema(ref: '#/components/schemas/DoctorProfileFull'),
    new OA\Schema(properties: [
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
    ]),
])]

#[OA\Schema(schema: 'Appointment', properties: [
    new OA\Property(property: 'id', type: 'integer', example: 1),
    new OA\Property(property: 'user_id', type: 'integer', nullable: true, example: 10, description: 'Patient user ID'),
    new OA\Property(property: 'doctor_id', type: 'integer', nullable: true, example: 1, description: 'Doctor profile ID'),
    new OA\Property(property: 'appointment_date', type: 'string', format: 'date', example: '2026-08-20'),
    new OA\Property(property: 'appointment_time', type: 'string', example: '14:30:00'),
    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Sore throat and fever'),
    new OA\Property(property: 'status', ref: '#/components/schemas/AppointmentStatus', example: 'pending'),
    new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
])]

#[OA\Schema(schema: 'DoctorManagementItem', properties: [
    new OA\Property(property: 'id', type: 'integer', example: 1),
    new OA\Property(property: 'name', type: 'string', example: 'Dr. John Doe'),
    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'doctor@example.com'),
    new OA\Property(property: 'profile', ref: '#/components/schemas/DoctorProfile', nullable: true),
])]

#[OA\Schema(schema: 'PaginatedDoctorProfile', properties: [
    new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/DoctorProfile')),
    new OA\Property(property: 'links', type: 'object', example: ['first' => null, 'last' => null, 'prev' => null, 'next' => null]),
    new OA\Property(property: 'meta', type: 'object', example: ['current_page' => 1, 'per_page' => 15, 'total' => 15]),
])]

#[OA\Schema(schema: 'PaginatedSpecialty', properties: [
    new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Specialty')),
    new OA\Property(property: 'links', type: 'object', example: ['first' => null, 'last' => null, 'prev' => null, 'next' => null]),
    new OA\Property(property: 'meta', type: 'object', example: ['current_page' => 1, 'per_page' => 15, 'total' => 15]),
])]

#[OA\Schema(schema: 'LoginResponse', properties: [
    new OA\Property(property: 'user', ref: '#/components/schemas/User'),
    new OA\Property(property: 'token', type: 'string', example: '1|abcdef123456'),
])]

#[OA\Schema(schema: 'PatientRegisterResponse', properties: [
    new OA\Property(property: 'message', type: 'string', example: 'Registration successful.'),
    new OA\Property(property: 'user', ref: '#/components/schemas/User'),
    new OA\Property(property: 'token', type: 'string', example: '1|abcdef123456'),
])]

#[OA\Schema(schema: 'RegisterResponse', properties: [
    new OA\Property(property: 'message', type: 'string', example: 'Doctor registered successfully. Account is pending admin approval.'),
    new OA\Property(property: 'user', ref: '#/components/schemas/User'),
    new OA\Property(property: 'doctor_profile', ref: '#/components/schemas/DoctorProfileFull'),
])]

#[OA\Schema(schema: 'BookAppointmentResponse', properties: [
    new OA\Property(property: 'message', type: 'string', example: 'Appointment booked successfully.'),
    new OA\Property(property: 'data', ref: '#/components/schemas/Appointment'),
])]

class Schemas {}
