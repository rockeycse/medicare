<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'date_of_birth'           => $this->date_of_birth?->toDateString(),
            'gender'                  => $this->gender,
            'blood_group'             => $this->blood_group,
            'address'                 => $this->address,
            'emergency_contact_name'  => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'user'                    => new UserResource($this->whenLoaded('user')),
        ];
    }
}
