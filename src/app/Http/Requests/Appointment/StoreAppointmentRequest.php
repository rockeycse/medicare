<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!$this->user()->isPatient()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Only patients can book appointments.',
            ], 403));
        }

        return true;
    }

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
