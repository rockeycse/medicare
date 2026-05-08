<?php

use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\User;
use Laravel\Passport\Passport;

test('anyone can list doctors', function () {
    Doctor::factory()->count(3)->create();

    $this->getJson('/api/v1/doctors')
         ->assertOk()
         ->assertJsonPath('success', true)
         ->assertJsonStructure(['data', 'meta']);
});

test('admin can create doctor', function () {
    $admin = User::factory()->admin()->create();
    $spec  = Specialization::factory()->create();

    Passport::actingAs($admin);

    $this->postJson('/api/v1/doctors', [
        'name'                  => 'Dr. Smith',
        'email'                 => 'drsmith@test.com',
        'phone'                 => '01712345678',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'specialization_id'     => $spec->id,
        'experience_years'      => 5,
        'consultation_fee'      => 800,
        'license_number'        => 'LIC-9999',
    ])
    ->assertStatus(201)
    ->assertJsonPath('success', true);
});

test('non-admin cannot create doctor', function () {
    $patient = User::factory()->create(['role' => 'patient']);

    Passport::actingAs($patient);

    $this->postJson('/api/v1/doctors', [])
         ->assertStatus(403);
});
