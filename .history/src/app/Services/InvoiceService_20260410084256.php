<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Notifications\InvoicePaidNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    public function generate(array $data): Invoice
    {
        $appointment = Appointment::with(['doctor', 'patient'])->findOrFail($data['appointment_id']);

        // Check if invoice already exists
        if ($appointment->invoice) {
            throw ValidationException::withMessages([
                'appointment_id' => ['Invoice already exists for this appointment.'],
            ]);
        }

        $subtotal = $appointment->doctor->consultation_fee;
        $discount = $data['discount'] ?? 0;
        $tax      = $data['tax'] ?? 0;
        $total    = $subtotal - $discount + ($subtotal * $tax / 100);

        return DB::transaction(function () use ($appointment, $subtotal, $discount, $tax, $total) {
            return Invoice::create([
                'appointment_id' => $appointment->id,
                'patient_id'     => $appointment->patient_id,
                'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'tax'            => $tax,
                'total_amount'   => $total,
                'status'         => 'unpaid',
            ]);
        });
    }

    public function pay(Invoice $invoice, array $data): Invoice
    {
        if ($invoice->status === 'paid') {
            throw ValidationException::withMessages([
                'invoice' => ['This invoice has already been paid.'],
            ]);
        }

        return DB::transaction(function () use ($invoice, $data) {
            Payment::create([
                'invoice_id'     => $invoice->id,
                'amount'         => $invoice->total_amount,
                'method'         => $data['method'],
                'transaction_id' => $data['transaction_id'] ?? null,
                'status'         => 'success',
            ]);

            $invoice->update([
                'status'  => 'paid',
                'paid_at' => now(),
            ]);

            $invoice->load(['payments', 'appointment', 'patient.user']);

            // Notification
            $invoice->patient->user->notify(new InvoicePaidNotification($invoice));

            // Broadcast
            event(new InvoicePaid($invoice));

            return $invoice;
        });
    }

    public function refund(Invoice $invoice): Invoice
    {
        if ($invoice->status !== 'paid') {
            throw ValidationException::withMessages([
                'invoice' => ['Only paid invoices can be refunded.'],
            ]);
        }

        $invoice->update(['status' => 'refunded']);

        return $invoice->fresh();
    }
}
