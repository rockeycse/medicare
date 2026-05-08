<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'method'         => ['required', 'in:cash,card,mobile_banking'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}
