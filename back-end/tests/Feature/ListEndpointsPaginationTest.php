<?php

use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Models\Specialty;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('patient doctor list is paginated and eager loads user and specialty', function () {
    $patient = User::factory()->create();
    $patient->assignRole('patient');

    $doctor = User::factory()->create();
    $doctor->assignRole('doctor');

    $specialty = Specialty::create(['name' => 'Cardiology']);

    DoctorProfile::factory()->create([
        'user_id' => $doctor->id,
        'specialty_id' => $specialty->id,
        'status' => 'active',
    ]);

    Sanctum::actingAs($patient);

    $response = $this->getJson('/api/v1/patient/appointment')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'user' => ['name'], 'specialty' => ['name']],
            ],
            'meta',
        ]);

    expect($response->json('data.0.user.name'))->toBe($doctor->name);
    expect($response->json('data.0.specialty.name'))->toBe('Cardiology');
});

test('doctor appointment list is paginated and eager loads user and doctorProfile', function () {
    $doctor = User::factory()->create();
    $doctor->assignRole('doctor');

    $profile = DoctorProfile::factory()->create(['user_id' => $doctor->id, 'status' => 'active']);

    $patient = User::factory()->create();

    Appointment::factory()->create([
        'user_id' => $patient->id,
        'doctor_id' => $profile->id,
        'status' => 'pending',
    ]);

    Sanctum::actingAs($doctor);

    $response = $this->getJson('/api/v1/doctor/appointment')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'user' => ['name'], 'doctor_profile' => ['profile_id']],
            ],
            'meta',
        ]);

    expect($response->json('data.0.user.name'))->toBe($patient->name);
    expect($response->json('data.0.doctor_profile.profile_id'))->toBe($profile->id);
});
