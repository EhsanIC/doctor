<?php

use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('doctor profile belongs to a user', function () {
    $user = User::factory()->create();

    $profile = DoctorProfile::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($profile->user)->toBeInstanceOf(User::class);
    expect($profile->user->id)->toBe($user->id);
});
