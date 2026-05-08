<?php

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Specialization;
use App\Models\User;
use App\Notifications\InvoicePaidNotification;
use Illuminate\Support\Facades\Notification;
use Laravel\Passport\Passport;

test('admin can generate invoice', function () {
    Notification::fake();

    $admin      = User::factory()->admin()->create();
    $spec       = Specialization::factory()->create();
    $doctorUser = User::factory()->doctor()->create();
    $doctor     = Doctor::factory()->create([
        'user_id'           => $doctorUser->id,
        'specialization_id' => $spec->id,
    ]);
    $schedule    = DoctorSchedule::factory()->create(['doctor_id' => $doctor->id]);
    $patientUser = User::factory()->create(['role' => 'patient']);
    $patient     = Patient::factory()->create(['user_id' => $patientUser->id]);
    $appointment = Appointment::factory()->create([
        'doctor_id'          => $doctor->id,
        'patient_id'         => $patient->id,
        'doctor_schedule_id' => $schedule->id,
        'status'             => 'completed',
    ]);

    Passport::actingAs($admin);

    $this->postJson('/api/v1/invoices', [
        'appointment_id' => $appointment->id,
        'discount'       => 0,
        'tax'            => 0,
    ])
    ->assertStatus(201)
    ->assertJsonPath('success', true)
    ->assertJsonStructure(['data' => ['invoice_number', 'total_amount', 'status']]);
});

test('patient can pay invoice', function () {
    Notification::fake();

    $spec       = Specialization::factory()->create();
    $doctorUser = User::factory()->doctor()->create();
    $doctor     = Doctor::factory()->create([
        'user_id'           => $doctorUser->id,
        'specialization_id' => $spec->id,
    ]);
    $schedule    = DoctorSchedule::factory()->create(['doctor_id' => $doctor->id]);
    $patientUser = User::factory()->create(['role' => 'patient']);
    $patient     = Patient::factory()->create(['user_id' => $patientUser->id]);
    $appointment = Appointment::factory()->create([
        'doctor_id'          => $doctor->id,
        'patient_id'         => $patient->id,
        'doctor_schedule_id' => $schedule->id,
    ]);
    $invoice = Invoice::factory()->create([
        'appointment_id' => $appointment->id,
        'patient_id'     => $patient->id,
        'status'         => 'unpaid',
    ]);

    Passport::actingAs($patientUser);

    $this->postJson("/api/v1/invoices/{$invoice->id}/pay", [
        'method' => 'cash',
    ])
    ->assertOk()
    ->assertJsonPath('success', true)
    ->assertJsonPath('data.status', 'paid');

    Notification::assertSentTo($patientUser, InvoicePaidNotification::class);
});
