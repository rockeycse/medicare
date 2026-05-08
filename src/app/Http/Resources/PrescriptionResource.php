<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'diagnosis'       => $this->diagnosis,
            'advice'          => $this->advice,
            'next_visit_date' => $this->next_visit_date?->toDateString(),
            'doctor'          => new DoctorResource($this->whenLoaded('doctor')),
            'patient'         => new PatientResource($this->whenLoaded('patient')),
            'appointment'     => new AppointmentResource($this->whenLoaded('appointment')),
            'medicines'       => PrescriptionMedicineResource::collection(
                                    $this->whenLoaded('medicines')
                                 ),
            'created_at'      => $this->created_at->toDateTimeString(),
        ];
    }
}
