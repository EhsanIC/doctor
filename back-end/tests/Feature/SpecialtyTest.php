<?php

use App\Models\Specialty;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

function specialty_test_user(string $role): User
{
    return tap(User::factory()->create(), fn (User $u) => $u->assignRole($role));
}

test('admin can list specialties', function () {
    Specialty::create(['name' => 'Cardiology']);

    Sanctum::actingAs(specialty_test_user('admin'));

    $this->getJson('/api/v1/admin/specialties')
        ->assertOk()
        ->assertJsonStructure(['data', 'links', 'meta'])
        ->assertJsonCount(1, 'data');
});

test('admin can create a specialty', function () {
    Sanctum::actingAs(specialty_test_user('admin'));

    $this->postJson('/api/v1/admin/specialties', ['name' => 'Neurology'])
        ->assertCreated()
        ->assertJson(['data' => ['name' => 'Neurology']]);

    expect(Specialty::where('name', 'Neurology')->exists())->toBeTrue();
});

test('admin can update a specialty', function () {
    $specialty = Specialty::create(['name' => 'Cardiology']);

    Sanctum::actingAs(specialty_test_user('admin'));

    $this->putJson("/api/v1/admin/specialties/{$specialty->id}", ['name' => 'Cardio'])
        ->assertOk()
        ->assertJson(['data' => ['name' => 'Cardio']]);
});

test('admin can soft delete a specialty', function () {
    $specialty = Specialty::create(['name' => 'Cardiology']);

    Sanctum::actingAs(specialty_test_user('admin'));

    $this->deleteJson("/api/v1/admin/specialties/{$specialty->id}")
        ->assertNoContent();

    expect(Specialty::find($specialty->id))->toBeNull();
    expect(Specialty::withTrashed()->find($specialty->id))->not->toBeNull();
});

test('non-admin users cannot access specialty routes', function () {
    Sanctum::actingAs(specialty_test_user('doctor'));

    $this->getJson('/api/v1/admin/specialties')->assertForbidden();
    $this->postJson('/api/v1/admin/specialties', ['name' => 'X'])->assertForbidden();
});

test('specialty name must be unique', function () {
    Specialty::create(['name' => 'Cardiology']);

    Sanctum::actingAs(specialty_test_user('admin'));

    $this->postJson('/api/v1/admin/specialties', ['name' => 'Cardiology'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('name');
});
