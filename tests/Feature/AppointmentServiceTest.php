<?php

use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Models\User;
use App\Services\AppointmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new AppointmentService;
});

test('listForDoctor returns only appointments for the given doctor, paginated and eager loaded', function () {
    $patient = User::factory()->create();
    $profile = DoctorProfile::factory()->create();
    $otherProfile = DoctorProfile::factory()->create();

    $own = Appointment::factory()->create([
        'user_id' => $patient->id,
        'doctor_id' => $profile->id,
    ]);
    Appointment::factory()->create([
        'user_id' => $patient->id,
        'doctor_id' => $otherProfile->id,
    ]);

    $result = $this->service->listForDoctor($profile);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($result->total())->toBe(1);
    expect($result->pluck('id')->all())->toBe([$own->id]);
    expect($result->first()->relationLoaded('user'))->toBeTrue();
    expect($result->first()->relationLoaded('doctorProfile'))->toBeTrue();
});

test('updateStatus updates the appointment status', function () {
    $patient = User::factory()->create();
    $profile = DoctorProfile::factory()->create();
    $appointment = Appointment::factory()->create([
        'user_id' => $patient->id,
        'doctor_id' => $profile->id,
        'status' => 'pending',
    ]);

    $this->service->updateStatus($appointment, ['status' => 'approved']);

    expect($appointment->fresh()->status->value)->toBe('approved');
});

test('book creates a pending appointment for the given user', function () {
    $patient = User::factory()->create();
    $profile = DoctorProfile::factory()->create();

    $appointment = $this->service->book([
        'doctor_id' => $profile->id,
        'appointment_date' => '2026-08-20',
        'appointment_time' => '14:30',
        'description' => 'Sore throat',
    ], $patient);

    expect($appointment)->toBeInstanceOf(Appointment::class);
    expect($appointment->user_id)->toBe($patient->id);
    expect($appointment->doctor_id)->toBe($profile->id);
    expect($appointment->status->value)->toBe('pending');
    expect($appointment->appointment_date)->toBe('2026-08-20');
    expect($appointment->description)->toBe('Sore throat');
});
