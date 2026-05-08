<?php

namespace App\Events;

use App\Models\Invoice;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoicePaid implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Invoice $invoice) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('patient.' . $this->invoice->patient_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'invoice.paid';
    }

    public function broadcastWith(): array
    {
        return [
            'invoice_id'     => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'total_amount'   => $this->invoice->total_amount,
            'message'        => 'Payment confirmed!',
        ];
    }
}
