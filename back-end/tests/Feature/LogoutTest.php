<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

test('authenticated user can log out and revoke their token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/v1/logout')
        ->assertNoContent();

    expect($user->tokens()->count())->toBe(0);
});

test('revoked token can no longer access protected routes', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $this->withToken($token)->postJson('/api/v1/logout')->assertNoContent();

    // Reset the cached auth guards so the revoked token is re-resolved on the next request.
    Auth::forgetGuards();

    $this->withToken($token)->getJson('/api/v1/test')->assertUnauthorized();
});

test('unauthenticated logout is rejected', function () {
    $this->postJson('/api/v1/logout')->assertUnauthorized();
});
