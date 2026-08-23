<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [

            // specialty
            'specialty.view',
            'specialty.create',
            'specialty.update',
            'specialty.delete',

            // doctor management
            'doctor.view',
            'doctor.pending',
            'doctor.disable',

            // doctor profile
            'doctor.update',
            'profile.view',
            'profile.update',

            // appointment
            'appointment.view',
            'appointment.pending',
            'appointment.cancel',
            'appointment.create',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                [
                    'name' => $permission,
                ],
                [
                    'guard_name' => 'web',
                ],
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $admin = Role::updateOrCreate(
            [
                'name' => 'admin',
            ],
            [
                'guard_name' => 'web',
            ],
        );

        $doctor = Role::updateOrCreate(
            [
                'name' => 'doctor',
            ],
            [
                'guard_name' => 'web',
            ],
        );

        $patient = Role::updateOrCreate(
            [
                'name' => 'patient',
            ],
            [
                'guard_name' => 'web',
            ],
        );

        /*
        |--------------------------------------------------------------------------
        | Assign Permissions
        |--------------------------------------------------------------------------
        */

        // admin gets all permissions
        $admin->givePermissionTo(Permission::all());

        // doctor permissions
        $doctor->givePermissionTo([
            'profile.view',
            'profile.update',
            'appointment.view',
            'appointment.pending',
            'appointment.cancel',
        ]);

        // patient permissions
        $patient->givePermissionTo([
            'doctor.view',
            'appointment.create',
        ]);
    }
}
