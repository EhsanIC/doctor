<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can log out and the logout call succeeds', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $this->withHeader('Referer', 'http://localhost:3000')
        ->get('/sanctum/csrf-cookie');

    $this->withHeader('Referer', 'http://localhost:3000')
        ->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk();

    // Logout returns 204 — the session is destroyed server-side
    $this->withHeader('Referer', 'http://localhost:3000')
        ->postJson('/api/v1/logout')->assertNoContent();
});

test('login creates a valid session that can access protected routes', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $this->withHeader('Referer', 'http://localhost:3000')
        ->get('/sanctum/csrf-cookie');

    $this->withHeader('Referer', 'http://localhost:3000')
        ->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk();

    // After login, /me returns the user
    $this->withHeader('Referer', 'http://localhost:3000')
        ->getJson('/api/v1/me')->assertOk()->assertJsonPath('id', $user->id);
});

test('unauthenticated logout is rejected', function () {
    $this->postJson('/api/v1/logout')->assertUnauthorized();
});
