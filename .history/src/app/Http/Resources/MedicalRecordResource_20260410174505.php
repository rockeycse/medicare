<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'file_name'   => $this->file_name,
            'file_type'   => $this->file_type,
            'file_size'   => $this->file_size_formatted,
            'description' => $this->description,
            'patient'     => new PatientResource($this->whenLoaded('patient')),
            'appointment' => new AppointmentResource($this->whenLoaded('appointment')),
            'uploaded_by' => new UserResource($this->whenLoaded('uploadedBy')),
            'created_at'  => $this->created_at->toDateTimeString(),
        ];
    }
}
