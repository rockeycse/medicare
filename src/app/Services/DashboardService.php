<?php

namespace App\Services;

use App\Contracts\Services\DashboardServiceInterface;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService implements DashboardServiceInterface
{
    public function getAdminStats(): array
    {
        return Cache::remember('admin_dashboard_stats', 300, function () {
            return [
                'overview' => [
                    'total_users'         => User::count(),
                    'total_doctors'       => Doctor::count(),
                    'total_patients'      => Patient::count(),
                    'total_appointments'  => Appointment::count(),
                ],
                'appointments' => [
                    'today'     => Appointment::today()->count(),
                    'pending'   => Appointment::pending()->count(),
                    'confirmed' => Appointment::confirmed()->count(),
                    'completed' => Appointment::where('status', 'completed')->count(),
                    'cancelled' => Appointment::where('status', 'cancelled')->count(),
                ],
                'revenue' => [
                    'total'   => Invoice::paid()->sum('total_amount'),
                    'today'   => Invoice::paid()
                        ->whereDate('paid_at', today())
                        ->sum('total_amount'),
                    'monthly' => Invoice::paid()
                        ->whereMonth('paid_at', now()->month)
                        ->sum('total_amount'),
                ],
                'monthly_appointments' => $this->getMonthlyAppointments(),
                'top_doctors'          => $this->getTopDoctors(),
                'recent_appointments'  => $this->getRecentAppointments(),
            ];
        });
    }

    public function getDoctorStats(int $doctorId): array
    {
        return Cache::remember("doctor_dashboard_{$doctorId}", 300, function () use ($doctorId) {
            return [
                'appointments' => [
                    'total'     => Appointment::where('doctor_id', $doctorId)->count(),
                    'today'     => Appointment::where('doctor_id', $doctorId)->today()->count(),
                    'pending'   => Appointment::where('doctor_id', $doctorId)->pending()->count(),
                    'completed' => Appointment::where('doctor_id', $doctorId)
                        ->where('status', 'completed')->count(),
                ],
                'prescriptions' => [
                    'total'   => Prescription::where('doctor_id', $doctorId)->count(),
                    'monthly' => Prescription::where('doctor_id', $doctorId)
                        ->whereMonth('created_at', now()->month)
                        ->count(),
                ],
                'revenue' => [
                    'total'   => Invoice::paid()
                        ->whereHas(
                            'appointment',
                            fn($q) =>
                            $q->where('doctor_id', $doctorId)
                        )->sum('total_amount'),
                    'monthly' => Invoice::paid()
                        ->whereMonth('paid_at', now()->month)
                        ->whereHas(
                            'appointment',
                            fn($q) =>
                            $q->where('doctor_id', $doctorId)
                        )->sum('total_amount'),
                ],
                'recent_appointments' => Appointment::with(['patient.user'])
                    ->where('doctor_id', $doctorId)
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(fn($a) => [
                        'id'           => $a->id,
                        'patient_name' => $a->patient->user->name,
                        'date'         => $a->appointment_date->toDateString(),
                        'time'         => $a->appointment_time,
                        'status'       => $a->status,
                    ]),
            ];
        });
    }

    public function getPatientStats(int $patientId): array
    {
        return Cache::remember("patient_dashboard_{$patientId}", 300, function () use ($patientId) {
            return [
                'appointments' => [
                    'total'     => Appointment::where('patient_id', $patientId)->count(),
                    'upcoming'  => Appointment::where('patient_id', $patientId)
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->whereDate('appointment_date', '>=', today())
                        ->count(),
                    'completed' => Appointment::where('patient_id', $patientId)
                        ->where('status', 'completed')->count(),
                ],
                'prescriptions' => [
                    'total' => Prescription::where('patient_id', $patientId)->count(),
                ],
                'invoices' => [
                    'total'   => Invoice::where('patient_id', $patientId)->count(),
                    'unpaid'  => Invoice::where('patient_id', $patientId)->unpaid()->count(),
                    'paid'    => Invoice::where('patient_id', $patientId)->paid()->count(),
                    'total_spent' => Invoice::where('patient_id', $patientId)
                        ->paid()->sum('total_amount'),
                ],
                'upcoming_appointments' => Appointment::with(['doctor.user', 'doctor.specialization'])
                    ->where('patient_id', $patientId)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->whereDate('appointment_date', '>=', today())
                    ->orderBy('appointment_date')
                    ->take(5)
                    ->get()
                    ->map(fn($a) => [
                        'id'             => $a->id,
                        'doctor_name'    => $a->doctor->user->name,
                        'specialization' => $a->doctor->specialization->name,
                        'date'           => $a->appointment_date->toDateString(),
                        'time'           => $a->appointment_time,
                        'status'         => $a->status,
                    ]),
            ];
        });
    }

    // Private helper methods (Single Responsibility)
    private function getMonthlyAppointments(): array
    {
        return Appointment::whereYear('appointment_date', now()->year)
            ->get(['appointment_date'])
            ->groupBy(fn($item) => $item->appointment_date->format('m'))
            ->map(fn($items, $month) => [
                'month' => date('F', mktime(0, 0, 0, (int) $month, 1)),
                'total' => $items->count(),
            ])
            ->values()
            ->toArray();
    }

    private function getTopDoctors(): array
    {
        return Doctor::with(['user', 'specialization'])
            ->withCount([
                'appointments' => fn($q) =>
                $q->where('status', 'completed')
            ])
            ->orderByDesc('appointments_count')
            ->take(5)
            ->get()
            ->map(fn($d) => [
                'id'             => $d->id,
                'name'           => $d->user->name,
                'specialization' => $d->specialization->name,
                'appointments'   => $d->appointments_count,
            ])
            ->toArray();
    }

    private function getRecentAppointments(): array
    {
        return Appointment::with(['doctor.user', 'patient.user'])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($a) => [
                'id'           => $a->id,
                'patient_name' => $a->patient->user->name,
                'doctor_name'  => $a->doctor->user->name,
                'date'         => $a->appointment_date->toDateString(),
                'status'       => $a->status,
            ])
            ->toArray();
    }
}
