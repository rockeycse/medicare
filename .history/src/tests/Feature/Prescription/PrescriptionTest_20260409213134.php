<?php

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Specialization;
use App\Models\User;
use Laravel\Passport\Passport;

test('doctor can create prescription', function () {
    $spec       = Specialization::factory()->create();
    $doctorUser = User::factory()->doctor()->create();
    $doctor     = Doctor::factory()->create([
        'user_id'           => $doctorUser->id,
        'specialization_id' => $spec->id,
    ]);

    $patientUser = User::factory()->create(['role' => 'patient']);
    $patient     = Patient::factory()->create(['user_id' => $patientUser->id]);

    $schedule = DoctorSchedule::factory()->create(['doctor_id' => $doctor->id]);

    $appointment = Appointment::factory()->create([
        'doctor_id'          => $doctor->id,
        'patient_id'         => $patient->id,
        'doctor_schedule_id' => $schedule->id,
        'status'             => 'confirmed',
    ]);

    Passport::actingAs($doctorUser);

    $this->postJson('/api/v1/prescriptions', [
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

    Passport::actingAs($patient);

    $this->postJson('/api/v1/prescriptions', [])
         ->assertStatus(403);
});
