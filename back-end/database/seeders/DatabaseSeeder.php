<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Specialty::insert([
            ['name' => 'Cardiology'],
            ['name' => 'Dermatology'],
            ['name' => 'Neurology'],
            ['name' => 'Orthopedics'],
            ['name' => 'Ophthalmology'],
        ]);

        Appointment::factory(10)->create();

        $this->call([
            RolesAndPermissionsSeeder::class,
            CreateUsersWithRolesSeeder::class,
        ]);
    }
}
