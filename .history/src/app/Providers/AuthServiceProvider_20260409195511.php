<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Prescription;
use App\Policies\AppointmentPolicy;
use App\Policies\DoctorPolicy;
use App\Policies\PrescriptionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Appointment::class  => AppointmentPolicy::class,
        Doctor::class       => DoctorPolicy::class,
        Prescription::class => PrescriptionPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
