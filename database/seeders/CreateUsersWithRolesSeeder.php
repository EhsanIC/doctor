<?php

namespace Database\Seeders;

use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateUsersWithRolesSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Default Admin
        |--------------------------------------------------------------------------
        */
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['name' => 'Admin User', 'password' => Hash::make('password')],
        );

        $admin->assignRole('admin');

        /*
        |--------------------------------------------------------------------------
        | Default Doctor
        |--------------------------------------------------------------------------
        */
        $doctor = User::firstOrCreate(
            ['email' => 'doctor@test.com'],
            ['name' => 'Doctor User', 'password' => Hash::make('password')],
        );

        $doctor->assignRole('doctor');

        DoctorProfile::firstOrCreate(
            ['user_id' => $doctor->id],
            ['status' => 'active'],
        );

        /*
        |--------------------------------------------------------------------------
        | Default Patient
        |--------------------------------------------------------------------------
        */
        $patient = User::firstOrCreate(
            ['email' => 'patient@test.com'],
            ['name' => 'Patient User', 'password' => Hash::make('password')],
        );

        $patient->assignRole('patient');

        /*
        |--------------------------------------------------------------------------
        | Sample data (random users)
        |--------------------------------------------------------------------------
        */
        $doctors = User::factory(3)
            ->has(DoctorProfile::factory())
            ->create();

        foreach ($doctors as $doctor) {
            $doctor->assignRole('doctor');
        }

        $patients = User::factory(10)->create();

        foreach ($patients as $patient) {
            $patient->assignRole('patient');
        }
    }
}
