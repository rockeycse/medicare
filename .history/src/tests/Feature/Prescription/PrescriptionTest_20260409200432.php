<?php

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use function Pest\test;

test('doctor can create prescription', function () {
    $doctorUser = User::factory()->doctor()->create();
    $doctor     = Doctor::factory()->create(['user_id' => $doctorUser->id]);

    $patientUser = User::factory()->create(['role' => 'patient']);
    $patient     = Patient::factory()->create(['user_id' => $patientUser->id]);

    $appointment = Appointment::factory()->create([
        'doctor_id'  => $doctor->id,
        'patient_id' => $patient->id,
        'status'     => 'confirmed',
    ]);

    $this->actingAs($doctorUser, 'api')
         ->postJson('/api/v1/prescriptions', [
             'appointment_id'  => $appointment->id,
             'diagnosis'       => 'Common cold',
             'advice'          => 'Rest and drink water',
             'medicines'       => [
                 [
                     'medicine_name' => 'Paracetamol',
                     'dosage'        => '500mg',
                     'frequency'     => '1+1+1',
                     'duration'      => '5 days',
                 ]
             ],
         ])
         ->assertStatus(201)
         ->assertJsonPath('success', true);
});

test('patient cannot create prescription', function () {
    $patient = User::factory()->create(['role' => 'patient']);

    $this->actingAs($patient, 'api')
         ->postJson('/api/v1/prescriptions', [])
         ->assertStatus(403);
});
