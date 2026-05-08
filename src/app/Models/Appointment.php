<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id', 'doctor_id', 'doctor_schedule_id',
        'appointment_date', 'appointment_time', 'serial_number',
        'status', 'type', 'symptoms', 'notes',
    ];

    protected function casts(): array
    {
        return ['appointment_date' => 'date'];
    }

    public function patient(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function schedule(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DoctorSchedule::class, 'doctor_schedule_id');
    }

    public function prescription(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Prescription::class);
    }

    public function invoice(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    // Scopes
    public function scopePending($query)   { return $query->where('status', 'pending'); }
    public function scopeConfirmed($query) { return $query->where('status', 'confirmed'); }
    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', today());
    }
}
