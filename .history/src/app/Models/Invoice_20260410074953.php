<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'invoice_number',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'     => 'decimal:2',
            'discount'     => 'decimal:2',
            'tax'          => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_at'      => 'datetime',
        ];
    }

    public function appointment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
