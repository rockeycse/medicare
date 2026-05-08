<?php

namespace App\Http\Requests\MedicalRecord;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreMedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user->isAdmin() && !$user->isDoctor() && !$user->isPatient()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403));
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id'     => ['required', 'exists:patients,id'],
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'file'           => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
            'description'    => ['nullable', 'string', 'max:500'],
        ];
    }
}
