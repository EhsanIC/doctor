<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Specialty;
use Database\Factories\AppointmentFactory;

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
            ['name' => 'قلب'],
            ['name' => 'پوست'],
            ['name' => 'مغز'],
            ['name' => 'ارتوپدی'],
        ]);

        Appointment::factory(10)->create();

        $this->call([
            RolesAndPermissionsSeeder::class,
            CreateUsersWithRolesSeeder::class
        ]);
    }
}
