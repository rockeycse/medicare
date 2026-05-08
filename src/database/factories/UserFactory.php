<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'     => fake()->name(),
            'email'    => fake()->unique()->safeEmail(),
            'phone'    => fake()->unique()->phoneNumber(),
            'role'     => 'patient',
            'password' => bcrypt('password'),
            'is_active'=> true,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn() => ['role' => 'admin']);
    }

    public function doctor(): static
    {
        return $this->state(fn() => ['role' => 'doctor']);
    }
}
