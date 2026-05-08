<?php

namespace App\Contracts\Services;

use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

interface MedicalRecordServiceInterface
{
    public function list(int $patientId, int $perPage = 15): LengthAwarePaginator;
    public function upload(UploadedFile $file, array $data, User $uploadedBy): MedicalRecord;
    public function delete(MedicalRecord $record, User $user): void;
    public function getDownloadUrl(MedicalRecord $record): string;
}
