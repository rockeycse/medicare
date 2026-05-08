<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePaidNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Invoice $invoice) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Confirmed - Invoice #' . $this->invoice->invoice_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your payment has been received successfully.')
            ->line('**Invoice:** ' . $this->invoice->invoice_number)
            ->line('**Amount:** BDT ' . $this->invoice->total_amount)
            ->line('**Status:** Paid')
            ->action('View Invoice', url('/api/v1/invoices/' . $this->invoice->id))
            ->line('Thank you for choosing MediCare!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'           => 'invoice_paid',
            'invoice_id'     => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'amount'         => $this->invoice->total_amount,
            'message'        => 'Payment confirmed for invoice ' . $this->invoice->invoice_number,
        ];
    }
}
