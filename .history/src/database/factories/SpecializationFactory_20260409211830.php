<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SpecializationFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Cardiology', 'Neurology', 'Orthopedics',
            'Dermatology', 'Pediatrics', 'Psychiatry',
        ]);

        return [
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => fake()->sentence(),
            'is_active'   => true,
        ];
    }
}
