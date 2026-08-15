<?php

namespace Database\Factories;

use App\Models\DoctorProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Specialty;

/**
 * @extends Factory<DoctorProfile>
 */
class DoctorProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'user_id' => User::factory(),

            'specialty_id' => Specialty::inRandomOrder()->first()?->id,

            'status' => fake()->randomElement([
                'pending',
                'active',
                'disabled',
            ]),

            'image' => fake()->imageUrl(),

            'bio' => fake()->paragraph(),

            
            'mobile' => fake()->unique()->phoneNumber(), 
            'medical_code' => fake()->unique()->regexify('[A-Z]{2}[0-9]{5}'), 
            'address' => fake()->address(), 
            'working_hours' => fake()->text(100), 
        ];
    }
}
