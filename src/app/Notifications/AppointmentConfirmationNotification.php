<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Appointment $appointment) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Appointment Confirmation - MediCare')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your appointment has been confirmed.')
            ->line('**Doctor:** ' . $this->appointment->doctor->user->name)
            ->line('**Date:** ' . $this->appointment->appointment_date->format('d M Y'))
            ->line('**Time:** ' . $this->appointment->appointment_time)
            ->line('**Serial No:** ' . $this->appointment->serial_number)
            ->action('View Appointment', url('/api/v1/appointments/' . $this->appointment->id))
            ->line('Thank you for choosing MediCare!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'           => 'appointment_confirmation',
            'appointment_id' => $this->appointment->id,
            'doctor_name'    => $this->appointment->doctor->user->name,
            'date'           => $this->appointment->appointment_date->format('d M Y'),
            'time'           => $this->appointment->appointment_time,
            'serial_number'  => $this->appointment->serial_number,
            'message'        => 'Your appointment has been confirmed.',
        ];
    }
}
