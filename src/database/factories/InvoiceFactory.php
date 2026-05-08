<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(500, 3000);

        return [
            'appointment_id' => Appointment::factory(),
            'patient_id'     => Patient::factory(),
            'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
            'subtotal'       => $subtotal,
            'discount'       => 0,
            'tax'            => 0,
            'total_amount'   => $subtotal,
            'status'         => 'unpaid',
        ];
    }
}
