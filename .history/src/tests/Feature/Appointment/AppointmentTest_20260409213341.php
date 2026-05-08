<?php

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Specialization;
use App\Models\User;
use Laravel\Passport\Passport;

test('patient can book appointment', function () {
    $spec       = Specialization::factory()->create();
    $doctorUser = User::factory()->doctor()->create();
    $doctor     = Doctor::factory()->create([
        'user_id'           => $doctorUser->id,
        'specialization_id' => $spec->id,
    ]);
    $schedule = DoctorSchedule::factory()->create(['doctor_id' => $doctor->id]);

    $patientUser = User::factory()->create(['role' => 'patient']);
    Patient::factory()->create(['user_id' => $patientUser->id]);

    Passport::actingAs($patientUser);

    $this->postJson('/api/v1/appointments', [
        'doctor_schedule_id' => $schedule->id,
        'appointment_date'   => now()->addDay()->toDateString(),
        'appointment_time'   => '10:00',
        'type'               => 'in-person',
    ])
    ->assertStatus(201)
    ->assertJsonPath('success', true);
});

test('only patients can book appointments', function () {
    $admin = User::factory()->admin()->create();

    Passport::actingAs($admin);

    // Admin কে valid data দিয়েও 403 আসবে Policy থেকে
    $this->postJson('/api/v1/appointments', [
        'doctor_schedule_id' => 1,
        'appointment_date'   => now()->addDay()->toDateString(),
        'appointment_time'   => '10:00',
        'type'               => 'in-person',
    ])
    ->assertStatus(403);
});
