<?php

namespace App\Services;

use App\Contracts\Repositories\MedicalRecordRepositoryInterface;
use App\Contracts\Services\MedicalRecordServiceInterface;
use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MedicalRecordService implements MedicalRecordServiceInterface
{
    public function __construct(
        private MedicalRecordRepositoryInterface $repository
    ) {}

    public function list(int $patientId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByPatient($patientId, $perPage);
    }

    public function upload(UploadedFile $file, array $data, User $uploadedBy): MedicalRecord
    {
        // Generate unique filename
        $fileName  = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $filePath  = 'medical_records/' . $data['patient_id'] . '/' . $fileName;

        // Store file
        Storage::disk('local')->put($filePath, file_get_contents($file));

        return $this->repository->create([
            'patient_id'     => $data['patient_id'],
            'appointment_id' => $data['appointment_id'] ?? null,
            'uploaded_by'    => $uploadedBy->id,
            'file_name'      => $file->getClientOriginalName(),
            'file_path'      => $filePath,
            'file_type'      => $file->getMimeType(),
            'file_size'      => $file->getSize(),
            'description'    => $data['description'] ?? null,
        ]);
    }

    public function delete(MedicalRecord $record, User $user): void
    {
        // Only admin, doctor or the patient can delete
        if (!$user->isAdmin() && !$user->isDoctor() &&
            $record->patient->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'record' => ['You are not authorized to delete this record.'],
            ]);
        }

        // Delete file from storage
        Storage::disk('local')->delete($record->file_path);

        $this->repository->delete($record);
    }

    public function getDownloadUrl(MedicalRecord $record): string
    {
        return Storage::disk('local')->path($record->file_path);
    }
}
