<?php

use Illuminate\Support\Facades\Broadcast;

// Doctor channel
Broadcast::channel('doctor.{doctorId}', function ($user, $doctorId) {
    return $user->isAdmin() ||
           ($user->isDoctor() && $user->doctor?->id == $doctorId);
});

// Patient channel
Broadcast::channel('patient.{patientId}', function ($user, $patientId) {
    return $user->isAdmin() ||
           ($user->isPatient() && $user->patient?->id == $patientId);
});
