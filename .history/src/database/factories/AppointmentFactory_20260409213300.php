<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        $doctor   = Doctor::factory()->create();
        $schedule = DoctorSchedule::factory()->create(['doctor_id' => $doctor->id]);
        $patient  = Patient::factory()->create();

        return [
            'patient_id'         => $patient->id,
            'doctor_id'          => $doctor->id,
            'doctor_schedule_id' => $schedule->id,
            'appointment_date'   => now()->addDays(rand(1, 30)),
            'appointment_time'   => '10:00',
            'serial_number'      => 1,
            'status'             => 'pending',
            'type'               => 'in-person',
        ];
    }
}
