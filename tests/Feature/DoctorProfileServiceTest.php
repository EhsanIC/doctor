<?php

use App\Models\DoctorProfile;
use App\Models\User;
use App\Services\DoctorProfileService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    Storage::fake('local');

    $this->service = new DoctorProfileService;
});

test('listDoctors returns only users with the doctor role', function () {
    $doctor = User::factory()->create();
    $doctor->assignRole('doctor');
    DoctorProfile::factory()->create(['user_id' => $doctor->id]);

    User::factory()->create()->assignRole('patient');

    $result = $this->service->listDoctors();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result)->toHaveCount(1);
    expect($result->first()->id)->toBe($doctor->id);
});

test('listProfiles returns a paginator', function () {
    DoctorProfile::factory()->count(3)->create();

    $result = $this->service->listProfiles();

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($result->total())->toBe(3);
});

test('listActive returns only active doctor profiles', function () {
    $active = DoctorProfile::factory()->create(['status' => 'active']);
    DoctorProfile::factory()->create(['status' => 'pending']);
    DoctorProfile::factory()->create(['status' => 'disabled']);

    $result = $this->service->listActive();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($result->pluck('id')->all())->toBe([$active->id]);
});

test('show loads the user relation', function () {
    $user = User::factory()->create();
    $profile = DoctorProfile::factory()->create(['user_id' => $user->id]);

    $result = $this->service->show($profile);

    expect($result)->toBeInstanceOf(DoctorProfile::class);
    expect($result->relationLoaded('user'))->toBeTrue();
    expect($result->user->id)->toBe($user->id);
});

test('update applies the given data', function () {
    $profile = DoctorProfile::factory()->create(['bio' => 'old bio']);

    $this->service->update($profile, ['bio' => 'new bio']);

    expect($profile->fresh()->bio)->toBe('new bio');
});

test('updateStatus changes the doctor status', function () {
    $profile = DoctorProfile::factory()->create(['status' => 'pending']);

    $this->service->updateStatus($profile, 'active');

    expect($profile->fresh()->status->value)->toBe('active');
});

test('updateOwn stores a new image and deletes the previous one', function () {
    $profile = DoctorProfile::factory()->create(['image' => 'doctors/old.jpg']);
    Storage::disk('local')->put('doctors/old.jpg', 'content');

    $image = UploadedFile::fake()->create('new.jpg', 100, 'image/jpeg');

    $this->service->updateOwn($profile, [], $image);

    $profile->refresh();

    expect($profile->image)->toContain('doctors/');
    expect($profile->image)->not->toBe('doctors/old.jpg');
    Storage::disk('local')->assertExists($profile->image);
    Storage::disk('local')->assertMissing('doctors/old.jpg');
});

test('updateOwn updates fields without changing the image', function () {
    $profile = DoctorProfile::factory()->create(['image' => 'doctors/keep.jpg', 'bio' => 'old']);

    $this->service->updateOwn($profile, ['bio' => 'updated bio']);

    expect($profile->fresh()->bio)->toBe('updated bio');
    expect($profile->fresh()->image)->toBe('doctors/keep.jpg');
});
