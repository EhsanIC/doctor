<?php

use App\Models\Appointment;
use App\Models\DoctorProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('doctor profile uses soft deletes', function () {
    $profile = DoctorProfile::factory()->create();

    $profile->delete();

    expect(DoctorProfile::find($profile->id))->toBeNull();
    expect(DoctorProfile::withTrashed()->find($profile->id))->not->toBeNull();
});

test('appointment uses soft deletes', function () {
    $appointment = Appointment::factory()->create();

    $appointment->delete();

    expect(Appointment::find($appointment->id))->toBeNull();
    expect(Appointment::withTrashed()->find($appointment->id))->not->toBeNull();
});
