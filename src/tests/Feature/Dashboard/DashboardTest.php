<?php

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialization;
use App\Models\User;
use Laravel\Passport\Passport;

test('admin can view dashboard', function () {
    $admin = User::factory()->admin()->create();

    Passport::actingAs($admin);

    $this->getJson('/api/v1/dashboard')
         ->assertOk()
         ->assertJsonPath('success', true)
         ->assertJsonStructure([
             'data' => [
                 'overview',
                 'appointments',
                 'revenue',
             ]
         ]);
});

test('doctor can view own dashboard', function () {
    $spec       = Specialization::factory()->create();
    $doctorUser = User::factory()->doctor()->create();
    Doctor::factory()->create([
        'user_id'           => $doctorUser->id,
        'specialization_id' => $spec->id,
    ]);

    Passport::actingAs($doctorUser);

    $this->getJson('/api/v1/dashboard')
         ->assertOk()
         ->assertJsonPath('success', true)
         ->assertJsonStructure([
             'data' => [
                 'appointments',
                 'prescriptions',
                 'revenue',
             ]
         ]);
});

test('patient can view own dashboard', function () {
    $patientUser = User::factory()->create(['role' => 'patient']);
    Patient::factory()->create(['user_id' => $patientUser->id]);

    Passport::actingAs($patientUser);

    $this->getJson('/api/v1/dashboard')
         ->assertOk()
         ->assertJsonPath('success', true)
         ->assertJsonStructure([
             'data' => [
                 'appointments',
                 'prescriptions',
                 'invoices',
             ]
         ]);
});
