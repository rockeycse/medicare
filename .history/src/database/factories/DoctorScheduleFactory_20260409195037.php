<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'doctor_id'    => Doctor::factory(),
            'day_of_week'  => fake()->randomElement([
                'monday','tuesday','wednesday','thursday','friday'
            ]),
            'start_time'   => '09:00',
            'end_time'     => '17:00',
            'max_patients' => fake()->numberBetween(10, 30),
            'is_available' => true,
        ];
    }
}
