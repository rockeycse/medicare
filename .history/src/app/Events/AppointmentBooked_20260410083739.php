<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentBooked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Appointment $appointment) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('doctor.' . $this->appointment->doctor_id),
            new PrivateChannel('patient.' . $this->appointment->patient_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'appointment.booked';
    }

    public function broadcastWith(): array
    {
        return [
            'appointment_id'   => $this->appointment->id,
            'patient_name'     => $this->appointment->patient->user->name,
            'doctor_name'      => $this->appointment->doctor->user->name,
            'appointment_date' => $this->appointment->appointment_date->toDateString(),
            'appointment_time' => $this->appointment->appointment_time,
            'serial_number'    => $this->appointment->serial_number,
            'status'           => $this->appointment->status,
            'message'          => 'New appointment booked!',
        ];
    }
}
