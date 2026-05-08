<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'date_of_birth', 'gender', 'blood_group',
        'address', 'emergency_contact_name', 'emergency_contact_phone',
    ];

    protected function casts(): array
    {
        return ['date_of_birth' => 'date'];
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function prescriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Prescription::class);
    }
}
