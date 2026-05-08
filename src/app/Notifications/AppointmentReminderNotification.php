<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminderNotification extends Notification implements ShouldQueue
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
            ->subject('Appointment Reminder - Tomorrow')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a reminder for your appointment tomorrow.')
            ->line('**Doctor:** ' . $this->appointment->doctor->user->name)
            ->line('**Date:** ' . $this->appointment->appointment_date->format('d M Y'))
            ->line('**Time:** ' . $this->appointment->appointment_time)
            ->action('View Details', url('/api/v1/appointments/' . $this->appointment->id))
            ->line('Please arrive 15 minutes early.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'           => 'appointment_reminder',
            'appointment_id' => $this->appointment->id,
            'doctor_name'    => $this->appointment->doctor->user->name,
            'date'           => $this->appointment->appointment_date->format('d M Y'),
            'time'           => $this->appointment->appointment_time,
            'message'        => 'Reminder: You have an appointment tomorrow.',
        ];
    }
}
