<?php

use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Models\User;
use App\Policies\AppointmentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('updateStatus allows the doctor who owns the appointment', function () {
    $doctor = User::factory()->create();
    $profile = DoctorProfile::factory()->create(['user_id' => $doctor->id]);
    $appointment = Appointment::factory()->create([
        'user_id' => User::factory()->create()->id,
        'doctor_id' => $profile->id,
    ]);

    expect((new AppointmentPolicy)->updateStatus($doctor, $appointment))->toBeTrue();
});

test('updateStatus denies a doctor who does not own the appointment', function () {
    $doctor = User::factory()->create();
    $otherDoctor = User::factory()->create();
    DoctorProfile::factory()->create(['user_id' => $doctor->id]);
    $otherProfile = DoctorProfile::factory()->create(['user_id' => $otherDoctor->id]);
    $appointment = Appointment::factory()->create([
        'user_id' => User::factory()->create()->id,
        'doctor_id' => $otherProfile->id,
    ]);

    expect((new AppointmentPolicy)->updateStatus($doctor, $appointment))->toBeFalse();
});

test('updateStatus denies a user without a doctor profile', function () {
    $patient = User::factory()->create();
    $profile = DoctorProfile::factory()->create();
    $appointment = Appointment::factory()->create([
        'user_id' => $patient->id,
        'doctor_id' => $profile->id,
    ]);

    expect((new AppointmentPolicy)->updateStatus($patient, $appointment))->toBeFalse();
});
