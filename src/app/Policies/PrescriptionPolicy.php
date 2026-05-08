<?php

namespace App\Policies;

use App\Models\Prescription;
use App\Models\User;

class PrescriptionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Prescription $prescription): bool
    {
        return match($user->role) {
            'admin'   => true,
            'doctor'  => $prescription->doctor_id === $user->doctor?->id,
            'patient' => $prescription->patient_id === $user->patient?->id,
            default   => false,
        };
    }

    public function create(User $user): bool
    {
        return $user->isDoctor();
    }
}
