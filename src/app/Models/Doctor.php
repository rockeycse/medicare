<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'specialization_id', 'bio',
        'experience_years', 'consultation_fee',
        'license_number', 'is_available',
    ];

    protected function casts(): array
    {
        return [
            'is_available'     => 'boolean',
            'consultation_fee' => 'decimal:2',
        ];
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function specialization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    public function schedules(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function appointments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    // Scope
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
}
