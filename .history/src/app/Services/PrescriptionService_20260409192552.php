<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Prescription;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PrescriptionService
{
    public function create(array $data, int $doctorId): Prescription
    {
        $appointment = Appointment::findOrFail($data['appointment_id']);

        // Validate appointment belongs to this doctor
        if ($appointment->doctor_id !== $doctorId) {
            throw ValidationException::withMessages([
                'appointment_id' => ['This appointment does not belong to you.'],
            ]);
        }

        // Validate appointment is confirmed/completed
        if (!in_array($appointment->status, ['confirmed', 'completed'])) {
            throw ValidationException::withMessages([
                'appointment_id' => ['Prescription can only be added to confirmed appointments.'],
            ]);
        }

        // Check if prescription already exists
        if ($appointment->prescription) {
            throw ValidationException::withMessages([
                'appointment_id' => ['Prescription already exists for this appointment.'],
            ]);
        }

        return DB::transaction(function () use ($data, $doctorId, $appointment) {
            $prescription = Prescription::create([
                'appointment_id'  => $appointment->id,
                'doctor_id'       => $doctorId,
                'patient_id'      => $appointment->patient_id,
                'diagnosis'       => $data['diagnosis'],
                'advice'          => $data['advice']          ?? null,
                'next_visit_date' => $data['next_visit_date'] ?? null,
            ]);

            $prescription->medicines()->createMany($data['medicines']);

            // Mark appointment as completed
            $appointment->update(['status' => 'completed']);

            return $prescription->load(['doctor.user', 'patient.user', 'medicines']);
        });
    }
}
