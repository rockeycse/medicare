<?php

namespace App\Listeners;

use App\Events\AppointmentBooked;
use App\Notifications\AppointmentConfirmationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAppointmentConfirmation implements ShouldQueue
{
    public function handle(AppointmentBooked $event): void
    {
        $patient = $event->appointment->patient->user;
        $patient->notify(new AppointmentConfirmationNotification($event->appointment));
    }
}
