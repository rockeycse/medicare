<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'invoice_number' => $this->invoice_number,
            'subtotal'       => $this->subtotal,
            'discount'       => $this->discount,
            'tax'            => $this->tax,
            'total_amount'   => $this->total_amount,
            'status'         => $this->status,
            'paid_at'        => $this->paid_at?->toDateTimeString(),
        ];
    }
}
