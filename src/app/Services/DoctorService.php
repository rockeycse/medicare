<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class DoctorService
{
    public function list(array $filters): LengthAwarePaginator
    {
        return Doctor::with(['user', 'specialization'])
            ->available()
            ->when(
                isset($filters['specialization_id']),
                fn($q) => $q->where('specialization_id', $filters['specialization_id'])
            )
            ->when(
                isset($filters['search']),
                fn($q) => $q->whereHas('user', fn($q) =>
                    $q->where('name', 'like', "%{$filters['search']}%")
                )
            )
            ->paginate(15);
    }

    public function create(array $data): Doctor
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'],
                'password' => $data['password'],
                'role'     => 'doctor',
            ]);

            return Doctor::create([
                'user_id'           => $user->id,
                'specialization_id' => $data['specialization_id'],
                'bio'               => $data['bio'] ?? null,
                'experience_years'  => $data['experience_years'],
                'consultation_fee'  => $data['consultation_fee'],
                'license_number'    => $data['license_number'],
            ]);
        });
    }

    public function update(Doctor $doctor, array $data): Doctor
    {
        return DB::transaction(function () use ($doctor, $data) {
            // Update user fields
            $userFields = array_filter([
                'name'  => $data['name']  ?? null,
                'phone' => $data['phone'] ?? null,
            ]);

            if (!empty($userFields)) {
                $doctor->user->update($userFields);
            }

            // Update doctor fields
            $doctor->update(array_filter([
                'specialization_id' => $data['specialization_id'] ?? null,
                'bio'               => $data['bio']               ?? null,
                'experience_years'  => $data['experience_years']  ?? null,
                'consultation_fee'  => $data['consultation_fee']  ?? null,
                'license_number'    => $data['license_number']    ?? null,
                'is_available'      => $data['is_available']      ?? null,
            ], fn($v) => !is_null($v)));

            return $doctor->fresh(['user', 'specialization']);
        });
    }

    public function syncSchedules(Doctor $doctor, array $schedules): void
    {
        // Delete old schedules and insert new ones
        $doctor->schedules()->delete();

        $doctor->schedules()->createMany($schedules);
    }
}
