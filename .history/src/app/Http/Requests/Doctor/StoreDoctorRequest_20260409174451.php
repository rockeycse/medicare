<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', 'unique:users,email'],
            'phone'              => ['required', 'string', 'unique:users,phone'],
            'password'           => ['required', 'string', 'min:8', 'confirmed'],
            'specialization_id'  => ['required', 'exists:specializations,id'],
            'bio'                => ['nullable', 'string'],
            'experience_years'   => ['required', 'integer', 'min:0'],
            'consultation_fee'   => ['required', 'numeric', 'min:0'],
            'license_number'     => ['required', 'string', 'unique:doctors,license_number'],
        ];
    }
}
