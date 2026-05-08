<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return match($user->role) {
            'admin'       => true,
            'doctor'      => $appointment->doctor_id === $user->doctor?->id,
            'patient'     => $appointment->patient_id === $user->patient?->id,
            'receptionist'=> true,
            default       => false,
        };
    }

    public function create(User $user): bool
    {
        return $user->isPatient();
    }

    public function cancel(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin() ||
               ($user->isPatient() && $appointment->patient_id === $user->patient?->id);
    }
}
