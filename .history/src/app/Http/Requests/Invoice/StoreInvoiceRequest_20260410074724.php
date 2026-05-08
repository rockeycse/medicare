<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!$this->user()->isAdmin() && !$this->user()->role === 'receptionist') {
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
            'appointment_id' => ['required', 'exists:appointments,id'],
            'discount'       => ['nullable', 'numeric', 'min:0'],
            'tax'            => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
