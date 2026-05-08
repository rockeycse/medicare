<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $doctorId = $this->route('doctor')->id;
        $userId   = $this->route('doctor')->user_id;

        return [
            'name'              => ['sometimes', 'string', 'max:255'],
            'phone'             => ['sometimes', 'string', Rule::unique('users', 'phone')->ignore($userId)],
            'specialization_id' => ['sometimes', 'exists:specializations,id'],
            'bio'               => ['nullable', 'string'],
            'experience_years'  => ['sometimes', 'integer', 'min:0'],
            'consultation_fee'  => ['sometimes', 'numeric', 'min:0'],
            'license_number'    => ['sometimes', 'string', Rule::unique('doctors', 'license_number')->ignore($doctorId)],
            'is_available'      => ['sometimes', 'boolean'],
        ];
    }
}
