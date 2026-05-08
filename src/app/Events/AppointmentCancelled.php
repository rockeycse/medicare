<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentCancelled implements ShouldBroadcast
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
        return 'appointment.cancelled';
    }

    public function broadcastWith(): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'message'        => 'Appointment has been cancelled.',
        ];
    }
}
