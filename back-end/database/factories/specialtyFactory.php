<?php

namespace Database\Factories;

use App\Models\specialty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<specialty>
 */
class specialtyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Cardiology',
                'Dermatology',
                'Neurology',
                'Orthopedics',
                'Ophthalmology',
            ]),
        ];
    }
}
