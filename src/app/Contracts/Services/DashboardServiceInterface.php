<?php

namespace App\Contracts\Services;

interface DashboardServiceInterface
{
    public function getAdminStats(): array;
    public function getDoctorStats(int $doctorId): array;
    public function getPatientStats(int $patientId): array;
}
