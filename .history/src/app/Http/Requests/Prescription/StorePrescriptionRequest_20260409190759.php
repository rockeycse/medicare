<?php

namespace App\Http\Requests\Prescription;

use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'appointment_id'             => ['required', 'exists:appointments,id'],
            'diagnosis'                  => ['required', 'string'],
            'advice'                     => ['nullable', 'string'],
            'next_visit_date'            => ['nullable', 'date', 'after:today'],
            'medicines'                  => ['required', 'array', 'min:1'],
            'medicines.*.medicine_name'  => ['required', 'string', 'max:255'],
            'medicines.*.dosage'         => ['required', 'string', 'max:100'],
            'medicines.*.frequency'      => ['required', 'string', 'max:100'],
            'medicines.*.duration'       => ['required', 'string', 'max:100'],
            'medicines.*.instructions'   => ['nullable', 'string'],
        ];
    }
}
