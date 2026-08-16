<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('user can have admin role', function () {

    Permission::create([
        'name' => 'doctor.approve',
    ]);

    $admin = Role::create([
        'name' => 'admin',
    ]);

    $admin->givePermissionTo('doctor.approve');

    $user = User::factory()->create();

    $user->assignRole('admin');

    expect($user->hasRole('admin'))->toBeTrue();

    expect($user->can('doctor.approve'))->toBeTrue();
});
