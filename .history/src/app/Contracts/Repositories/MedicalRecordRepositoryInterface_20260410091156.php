<?php

namespace App\Contracts\Repositories;

use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Pagination\LengthAwarePaginator;

interface MedicalRecordRepositoryInterface
{
    public function getByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?MedicalRecord;
    public function create(array $data): MedicalRecord;
    public function delete(MedicalRecord $record): bool;
}
