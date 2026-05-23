<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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

            // doctor
            'doctor.view',
            'doctor.pending',
            'doctor.suspended',
            'doctor.disable',

            // appointment
            'appointment.view',
            'appointment.create',
            'appointment.pending',
            'appointment.cancel',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $admin = Role::firstOrCreate([
            'name' => 'admin'
        ]);

        $doctor = Role::firstOrCreate([
            'name' => 'doctor'
        ]);

        $patient = Role::firstOrCreate([
            'name' => 'patient'
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
            'appointment.view',
            'appointment.pending',
            'appointment.cancel',
        ]);

        // patient permissions
        $patient->givePermissionTo([
            'appointment.create',
        ]);
    }
}