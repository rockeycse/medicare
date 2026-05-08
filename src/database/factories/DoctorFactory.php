<?php

namespace Database\Factories;

use App\Models\Specialization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'           => User::factory()->doctor(),
            'specialization_id' => Specialization::factory(),
            'bio'               => fake()->paragraph(),
            'experience_years'  => fake()->numberBetween(1, 30),
            'consultation_fee'  => fake()->numberBetween(500, 3000),
            'license_number'    => 'LIC-' . fake()->unique()->numerify('####'),
            'is_available'      => true,
        ];
    }
}
