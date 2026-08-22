<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('a patient can register and is logged in immediately', function () {
    $this->withHeader('Referer', 'http://localhost:3000')
        ->get('/sanctum/csrf-cookie');

    $response = $this->withHeader('Referer', 'http://localhost:3000')
        ->postJson('/api/v1/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['message', 'user']);

    $user = User::where('email', 'jane@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->hasRole('patient'))->toBeTrue();
    expect($user->hasRole('doctor'))->toBeFalse();
});

test('patient registration validates required fields', function () {
    $response = $this->postJson('/api/v1/register', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});
