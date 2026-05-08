<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Notifications\AppointmentConfirmationNotification;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    protected $signature   = 'appointments:send-reminders';
    protected $description = 'Send reminder notifications for tomorrow\'s appointments';

    public function handle(): void
    {
        $appointments = Appointment::with(['patient.user', 'doctor.user'])
            ->confirmed()
            ->whereDate('appointment_date', today()->addDay())
            ->get();

        $appointments->each(function ($appointment) {
            $appointment->patient->user->notify(
                new AppointmentConfirmationNotification($appointment)
            );
        });

        $this->info("Sent reminders for {$appointments->count()} appointments.");
    }
}
