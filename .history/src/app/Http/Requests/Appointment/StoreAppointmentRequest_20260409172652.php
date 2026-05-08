<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'doctor_schedule_id' => ['required', 'exists:doctor_schedules,id'],
            'appointment_date'   => ['required', 'date', 'after_or_equal:today'],
            'appointment_time'   => ['required', 'date_format:H:i'],
            'type'               => ['required', 'in:in-person,online'],
            'symptoms'           => ['nullable', 'string', 'max:1000'],
        ];
    }
}
