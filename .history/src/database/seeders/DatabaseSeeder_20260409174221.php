<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialization;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::factory()->admin()->create([
            'name'  => 'Admin User',
            'email' => 'admin@medicare.com',
        ]);

        // Specializations
        $specializations = Specialization::insert([
            ['name' => 'Cardiology',  'slug' => 'cardiology'],
            ['name' => 'Neurology',   'slug' => 'neurology'],
            ['name' => 'Orthopedics', 'slug' => 'orthopedics'],
        ]);

        // Doctors
        User::factory()->doctor()->count(5)->create()->each(function ($user) {
            Doctor::create([
                'user_id'            => $user->id,
                'specialization_id'  => rand(1, 3),
                'consultation_fee'   => rand(500, 2000),
                'license_number'     => 'LIC-' . fake()->unique()->numberBetween(1000, 9999),
                'experience_years'   => rand(2, 20),
            ]);
        });

        // Patients
        User::factory()->count(20)->create()->each(function ($user) {
            Patient::create(['user_id' => $user->id]);
        });
    }
}
