<?php

use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

function make_user_with_role(string $role): User
{
    return tap(User::factory()->create(), fn (User $user) => $user->assignRole($role));
}

test('seeder assigns the expected permissions to each role', function () {
    $admin = Role::findByName('admin');
    $doctor = Role::findByName('doctor');
    $patient = Role::findByName('patient');

    // admin has every permission
    expect($admin->hasAllPermissions(Permission::pluck('name')->all()))->toBeTrue();

    // doctor permissions
    expect($doctor->hasAllPermissions(
        'profile.view',
        'profile.update',
        'appointment.view',
        'appointment.pending',
        'appointment.cancel',
    ))->toBeTrue();
    expect($doctor->hasPermissionTo('doctor.view'))->toBeFalse();

    // patient permissions
    expect($patient->hasAllPermissions('doctor.view', 'appointment.create'))->toBeTrue();
    expect($patient->hasPermissionTo('profile.view'))->toBeFalse();
    expect($patient->hasPermissionTo('appointment.cancel'))->toBeFalse();
});

test('unauthenticated users cannot access protected routes', function () {
    $this->getJson('/api/v1/admin/doctors')->assertUnauthorized();
    $this->getJson('/api/v1/doctor/appointment')->assertUnauthorized();
    $this->getJson('/api/v1/patient/appointment')->assertUnauthorized();
});

test('only admins can access admin doctor routes', function () {
    $admin = make_user_with_role('admin');
    $doctor = make_user_with_role('doctor');
    $patient = make_user_with_role('patient');

    Sanctum::actingAs($admin);
    $this->getJson('/api/v1/admin/doctors')->assertOk();
    $this->getJson('/api/v1/admin/doctors/profile')->assertOk();

    Sanctum::actingAs($doctor);
    $this->getJson('/api/v1/admin/doctors')->assertForbidden();

    Sanctum::actingAs($patient);
    $this->getJson('/api/v1/admin/doctors')->assertForbidden();
});

test('doctor can view and update their own profile but not another doctors', function () {
    $doctorA = make_user_with_role('doctor');
    $doctorB = make_user_with_role('doctor');

    $profileA = DoctorProfile::factory()->create(['user_id' => $doctorA->id, 'status' => 'active']);
    $profileB = DoctorProfile::factory()->create(['user_id' => $doctorB->id, 'status' => 'active']);

    Sanctum::actingAs($doctorA);

    // own profile is accessible and editable
    $this->getJson("/api/v1/doctor/profile/{$profileA->id}")->assertOk();
    $this->patchJson("/api/v1/doctor/profile/{$profileA->id}", [
        'bio' => 'Updated bio',
    ])->assertOk();

    expect($profileA->fresh()->bio)->toBe('Updated bio');

    // another doctor's profile is forbidden (ownership policy)
    $this->getJson("/api/v1/doctor/profile/{$profileB->id}")->assertForbidden();
    $this->patchJson("/api/v1/doctor/profile/{$profileB->id}", [
        'bio' => 'Hacked',
    ])->assertForbidden();

    expect($profileB->fresh()->bio)->not->toBe('Hacked');
});

test('patient cannot access doctor-only routes', function () {
    $patient = make_user_with_role('patient');
    $doctor = make_user_with_role('doctor');
    $profile = DoctorProfile::factory()->create(['user_id' => $doctor->id, 'status' => 'active']);
    $appointment = Appointment::factory()->create([
        'user_id' => $doctor->id,
        'doctor_id' => $profile->id,
        'status' => 'pending',
    ]);

    Sanctum::actingAs($patient);

    $this->getJson('/api/v1/doctor/appointment')->assertForbidden();
    $this->patchJson("/api/v1/doctor/appointment/{$appointment->id}", [
        'status' => 'approved',
    ])->assertForbidden();
});

test('patient can view active doctors and book an appointment', function () {
    $patient = make_user_with_role('patient');
    $doctor = make_user_with_role('doctor');
    $profile = DoctorProfile::factory()->create(['user_id' => $doctor->id, 'status' => 'active']);

    Sanctum::actingAs($patient);

    $this->getJson('/api/v1/patient/appointment')
        ->assertOk()
        ->assertJsonCount(1);

    $this->postJson('/api/v1/patient/appointment', [
        'doctor_id' => $profile->id,
        'appointment_date' => now()->addDay()->toDateString(),
        'appointment_time' => '14:30',
        'description' => 'Sore throat',
    ])->assertCreated();

    expect(Appointment::where('user_id', $patient->id)->count())->toBe(1);
});

test('doctor cannot book an appointment', function () {
    $doctor = make_user_with_role('doctor');
    $profile = DoctorProfile::factory()->create(['user_id' => $doctor->id, 'status' => 'active']);

    Sanctum::actingAs($doctor);

    $this->postJson('/api/v1/patient/appointment', [
        'doctor_id' => $profile->id,
        'appointment_date' => now()->addDay()->toDateString(),
        'appointment_time' => '14:30',
    ])->assertForbidden();
});

test('doctor can approve or cancel their own appointment', function () {
    $doctor = make_user_with_role('doctor');
    $profile = DoctorProfile::factory()->create(['user_id' => $doctor->id, 'status' => 'active']);

    $appointment = Appointment::factory()->create([
        'user_id' => $doctor->id,
        'doctor_id' => $profile->id,
        'status' => 'pending',
    ]);

    Sanctum::actingAs($doctor);

    $this->patchJson("/api/v1/doctor/appointment/{$appointment->id}", [
        'status' => 'approved',
    ])->assertOk();

    expect($appointment->fresh()->status->value)->toBe('approved');
});

test('doctor only sees their own appointments', function () {
    $doctor = make_user_with_role('doctor');
    $otherDoctor = make_user_with_role('doctor');
    $profile = DoctorProfile::factory()->create(['user_id' => $doctor->id, 'status' => 'active']);
    $otherProfile = DoctorProfile::factory()->create(['user_id' => $otherDoctor->id, 'status' => 'active']);

    $own = Appointment::factory()->create([
        'user_id' => $doctor->id,
        'doctor_id' => $profile->id,
        'status' => 'pending',
    ]);

    $other = Appointment::factory()->create([
        'user_id' => $doctor->id,
        'doctor_id' => $otherProfile->id,
        'status' => 'pending',
    ]);

    Sanctum::actingAs($doctor);

    $this->getJson('/api/v1/doctor/appointment')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['id' => $own->id])
        ->assertJsonMissing(['id' => $other->id]);
});

test('doctor cannot update another doctors appointment', function () {
    $doctor = make_user_with_role('doctor');
    $otherDoctor = make_user_with_role('doctor');
    $profile = DoctorProfile::factory()->create(['user_id' => $doctor->id, 'status' => 'active']);
    $otherProfile = DoctorProfile::factory()->create(['user_id' => $otherDoctor->id, 'status' => 'active']);

    $appointment = Appointment::factory()->create([
        'user_id' => $doctor->id,
        'doctor_id' => $otherProfile->id,
        'status' => 'pending',
    ]);

    Sanctum::actingAs($doctor);

    $this->patchJson("/api/v1/doctor/appointment/{$appointment->id}", ['status' => 'approved'])
        ->assertForbidden();

    expect($appointment->fresh()->status->value)->toBe('pending');
});

test('doctor with pending or disabled status cannot access the panel', function (string $status) {
    $doctor = make_user_with_role('doctor');
    DoctorProfile::factory()->create(['user_id' => $doctor->id, 'status' => $status]);

    Sanctum::actingAs($doctor);

    $this->getJson('/api/v1/doctor/appointment')->assertForbidden();
})->with(['pending', 'disabled']);
