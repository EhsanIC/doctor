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
            Permission::firstOrCreate([
                'name' => $permission,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $admin = Role::firstOrCreate([
            'name' => 'admin',
        ]);

        $doctor = Role::firstOrCreate([
            'name' => 'doctor',
        ]);

        $patient = Role::firstOrCreate([
            'name' => 'patient',
        ]);

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
