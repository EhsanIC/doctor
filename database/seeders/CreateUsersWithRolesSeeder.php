<?php

namespace Database\Seeders;

use App\Models\DoctorProfile;
use App\Models\Specialty;
use App\Models\User;
use Database\Factories\DoctorProfileFactory;
use Database\Factories\specialtyFactory;
use Illuminate\Database\Seeder;

class CreateUsersWithRolesSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
        ]);

        $admin->assignRole('admin');

        /*
        |--------------------------------------------------------------------------
        | Doctors
        |--------------------------------------------------------------------------
        */

        $doctors = User::factory(3)
        ->has(DoctorProfile::factory())
        ->create();

        foreach ($doctors as $doctor) {
            $doctor->assignRole('doctor');
        }

        /*
        |--------------------------------------------------------------------------
        | Patients
        |--------------------------------------------------------------------------
        */

        $patients = User::factory(10)->create();

        foreach ($patients as $patient) {
            $patient->assignRole('patient');
        }
    }
}