<?php

namespace Database\Factories;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\DoctorProfile;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['pending', 'approved', 'canceled']; 

        return [
            // تولید یک تاریخ تصادفی برای 30 روز آینده
            'appointment_date' => $this->faker->date(), 
            
            // تولید یک زمان تصادفی
            'appointment_time' => $this->faker->time('H:i'), 
            
            // تولید یک متن تصادفی برای توضیحات
            'description' => $this->faker->sentence(), 
            
            // اختصاص یک کاربر به صورت تصادفی از دیتابیس
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            
            // اختصاص یک پزشک به صورت تصادفی از دیتابیس
            'doctor_id' => DoctorProfile::inRandomOrder()->first()?->id ?? DoctorProfile::factory(),

            'status' => $this->faker->randomElement($statuses), 
        ];
    }
}
