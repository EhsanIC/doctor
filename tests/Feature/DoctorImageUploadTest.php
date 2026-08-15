<?php

use App\Models\DoctorProfile;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    Storage::fake('local');
});

function image_test_doctor(): User
{
    $doctor = User::factory()->create();
    $doctor->assignRole('doctor');

    DoctorProfile::factory()->create(['user_id' => $doctor->id, 'status' => 'active']);

    return $doctor;
}

test('doctor can upload a profile image', function () {
    $doctor = image_test_doctor();
    $profile = $doctor->doctorProfile;

    Sanctum::actingAs($doctor);

    $this->patchJson("/api/v1/doctor/profile/{$profile->id}", [
        'image' => UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg'),
    ])->assertOk();

    $profile->refresh();

    expect($profile->image)->toContain('doctors/');
    Storage::disk('local')->assertExists($profile->image);
});

test('non-image file is rejected', function () {
    $doctor = image_test_doctor();
    $profile = $doctor->doctorProfile;

    Sanctum::actingAs($doctor);

    $this->patchJson("/api/v1/doctor/profile/{$profile->id}", [
        'image' => UploadedFile::fake()->create('document.jpg', 100, 'text/plain'),
    ])->assertStatus(422)->assertJsonValidationErrors('image');
});

test('image larger than 2mb is rejected', function () {
    $doctor = image_test_doctor();
    $profile = $doctor->doctorProfile;

    Sanctum::actingAs($doctor);

    $this->patchJson("/api/v1/doctor/profile/{$profile->id}", [
        'image' => UploadedFile::fake()->create('big.jpg', 3000, 'image/jpeg'),
    ])->assertStatus(422)->assertJsonValidationErrors('image');
});
