<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'date_of_birth' => fake()->date(max: '-18 years'),
            'gender'        => fake()->randomElement(['male', 'female']),
            'blood_group'   => fake()->randomElement(['A+','A-','B+','B-','O+','O-','AB+','AB-']),
            'address'       => fake()->address(),
        ];
    }
}
