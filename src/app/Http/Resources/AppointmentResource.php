<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'appointment_date' => $this->appointment_date->toDateString(),
            'appointment_time' => $this->appointment_time,
            'serial_number'    => $this->serial_number,
            'status'           => $this->status,
            'type'             => $this->type,
            'symptoms'         => $this->symptoms,
            'doctor'           => new DoctorResource($this->whenLoaded('doctor')),
            'patient'          => new PatientResource($this->whenLoaded('patient')),
            'prescription'     => new PrescriptionResource($this->whenLoaded('prescription')),
            'invoice'          => new InvoiceResource($this->whenLoaded('invoice')),
            'created_at'       => $this->created_at->toDateTimeString(),
        ];
    }
}
