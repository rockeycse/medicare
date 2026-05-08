<?php

namespace App\Repositories;

use App\Contracts\Repositories\MedicalRecordRepositoryInterface;
use App\Models\MedicalRecord;
use Illuminate\Pagination\LengthAwarePaginator;

class MedicalRecordRepository implements MedicalRecordRepositoryInterface
{
    public function getByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator
    {
        return MedicalRecord::with(['uploadedBy', 'appointment'])
            ->where('patient_id', $patientId)
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?MedicalRecord
    {
        return MedicalRecord::with(['patient.user', 'uploadedBy', 'appointment'])
            ->findOrFail($id);
    }

    public function create(array $data): MedicalRecord
    {
        return MedicalRecord::create($data);
    }

    public function delete(MedicalRecord $record): bool
    {
        return $record->delete();
    }
}
