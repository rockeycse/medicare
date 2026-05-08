<?php

namespace App\Services;

use App\Events\AppointmentBooked;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    public function book(array $data, int $patientId): Appointment
    {
        $schedule = DoctorSchedule::findOrFail($data['doctor_schedule_id']);

        // Check max patient limit
        $existingCount = Appointment::where('doctor_schedule_id', $schedule->id)
            ->whereDate('appointment_date', $data['appointment_date'])
            ->whereNotIn('status', ['cancelled'])
            ->count();

        if ($existingCount >= $schedule->max_patients) {
            throw ValidationException::withMessages([
                'appointment_date' => ['No available slots for this date.'],
            ]);
        }

        $appointment = Appointment::create([
            ...$data,
            'patient_id'    => $patientId,
            'doctor_id'     => $schedule->doctor_id,
            'serial_number' => $existingCount + 1,
            'status'        => 'pending',
        ]);

        event(new AppointmentBooked($appointment));

        return $appointment->load(['doctor.user', 'patient.user', 'schedule']);
    }

    public function cancel(Appointment $appointment): Appointment
    {
        $appointment->update(['status' => 'cancelled']);

        event(new AppointmentCancelled($appointment));

        return $appointment;
    }

    public function complete(Appointment $appointment): Appointment
    {
        $appointment->update(['status' => 'completed']);
        return $appointment;
    }
}
