<?php

use Database\Seeders\CreateUsersWithRolesSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed([
        RolesAndPermissionsSeeder::class,
        CreateUsersWithRolesSeeder::class,
    ]);
});

test('an admin can login successfully', function () {
    $this->withHeader('Referer', 'http://localhost:3000')
        ->get('/sanctum/csrf-cookie');

    $response = $this->withHeader('Referer', 'http://localhost:3000')
        ->postJson('/api/v1/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['user']);

    expect($response->json('user.email'))->toBe('admin@test.com');
});

test('a doctor can login successfully', function () {
    $this->withHeader('Referer', 'http://localhost:3000')
        ->get('/sanctum/csrf-cookie');

    $response = $this->withHeader('Referer', 'http://localhost:3000')
        ->postJson('/api/v1/login', [
            'email' => 'doctor@test.com',
            'password' => 'password',
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['user']);

    expect($response->json('user.email'))->toBe('doctor@test.com');
});

test('a patient can login successfully', function () {
    $this->withHeader('Referer', 'http://localhost:3000')
        ->get('/sanctum/csrf-cookie');

    $response = $this->withHeader('Referer', 'http://localhost:3000')
        ->postJson('/api/v1/login', [
            'email' => 'patient@test.com',
            'password' => 'password',
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['user']);

    expect($response->json('user.email'))->toBe('patient@test.com');
});

test('login fails with wrong credentials', function () {
    $response = $this->postJson('/api/v1/login', [
        'email' => 'admin@test.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401);
});
