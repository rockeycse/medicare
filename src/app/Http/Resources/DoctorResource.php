<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'license_number'    => $this->license_number,
            'bio'               => $this->bio,
            'experience_years'  => $this->experience_years,
            'consultation_fee'  => $this->consultation_fee,
            'is_available'      => $this->is_available,
            'user'              => new UserResource($this->whenLoaded('user')),
            'specialization'    => new SpecializationResource($this->whenLoaded('specialization')),
            'schedules'         => DoctorScheduleResource::collection($this->whenLoaded('schedules')),
        ];
    }
}
