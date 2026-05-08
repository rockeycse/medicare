<?php

use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\DoctorSchedule;

test('patient can book appointment', function () {
    $user = User::factory()->create(['role' => 'patient']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    $doctor = Doctor::factory()->create();
    $schedule = DoctorSchedule::factory()->create([
        'doctor_id' => $doctor->id
    ]);

    $response = $this->actingAs($user, 'api')
        ->postJson('/api/v1/appointments', [
            'doctor_schedule_id' => $schedule->id,
            'appointment_date'   => now()->addDay()->toDateString(),
            'appointment_time'   => '10:00',
            'type'               => 'in-person',
        ]);

    $response->assertStatus(201)
             ->assertJsonPath('success', true);
});

test('only patients can book appointments', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin, 'api')
        ->postJson('/api/v1/appointments', [])
        ->assertStatus(403);
});
