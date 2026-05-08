<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorScheduleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'schedules'                  => ['required', 'array', 'min:1'],
            'schedules.*.day_of_week'    => ['required', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'schedules.*.start_time'     => ['required', 'date_format:H:i'],
            'schedules.*.end_time'       => ['required', 'date_format:H:i', 'after:schedules.*.start_time'],
            'schedules.*.max_patients'   => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }
}
