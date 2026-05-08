<?php

use App\Events\AppointmentBooked;
use App\Events\AppointmentCancelled;
use App\Events\InvoicePaid;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;

test('appointment booked event is fired', function () {
    Event::fake();

    $spec       = Specialization::factory()->create();
    $doctorUser = User::factory()->doctor()->create();
    $doctor     = Doctor::factory()->create([
        'user_id'           => $doctorUser->id,
        'specialization_id' => $spec->id,
    ]);
    $schedule    = DoctorSchedule::factory()->create(['doctor_id' => $doctor->id]);
    $patientUser = User::factory()->create(['role' => 'patient']);
    Patient::factory()->create(['user_id' => $patientUser->id]);

    Passport::actingAs($patientUser);

    $this->postJson('/api/v1/appointments', [
        'doctor_schedule_id' => $schedule->id,
        'appointment_date'   => now()->addDay()->toDateString(),
        'appointment_time'   => '10:00',
        'type'               => 'in-person',
    ])->assertStatus(201);

    Event::assertDispatched(AppointmentBooked::class);
});

test('appointment cancelled event is fired', function () {
    Event::fake();

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
        'status'             => 'pending',
    ]);

    Passport::actingAs($patientUser);

    $this->patchJson("/api/v1/appointments/{$appointment->id}/cancel")
         ->assertOk();

    Event::assertDispatched(AppointmentCancelled::class);
});

test('invoice paid event is fired', function () {
    Event::fake();

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
    ])->assertOk();

    Event::assertDispatched(InvoicePaid::class);
});
