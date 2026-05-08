<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'appointment_id', 'doctor_id', 'patient_id',
        'diagnosis', 'advice', 'next_visit_date',
    ];

    protected function casts(): array
    {
        return ['next_visit_date' => 'date'];
    }

    public function appointment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicines(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PrescriptionMedicine::class);
    }
}
