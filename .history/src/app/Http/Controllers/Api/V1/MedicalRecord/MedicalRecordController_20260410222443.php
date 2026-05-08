<?php

namespace App\Http\Controllers\Api\V1\MedicalRecord;

use App\Contracts\Services\MedicalRecordServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\MedicalRecord\StoreMedicalRecordRequest;
use App\Http\Resources\MedicalRecordResource;
use App\Models\MedicalRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MedicalRecordController extends Controller
{
    public function __construct(
        private MedicalRecordServiceInterface $service
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/medical-records",
     *     tags={"Medical Records"},
     *     summary="List medical records",
     *     operationId="listMedicalRecords",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="patient_id", in="query", required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="List of medical records"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */

    public function index(Request $request): JsonResponse
    {
        $patientId = $request->user()->isPatient()
            ? $request->user()->patient->id
            : $request->query('patient_id');

        if (!$patientId) {
            return response()->json([
                'success' => false,
                'message' => 'Patient ID is required.',
            ], 422);
        }

        $records = $this->service->list($patientId);

        return response()->json([
            'success' => true,
            'data'    => MedicalRecordResource::collection($records),
            'meta'    => [
                'total'        => $records->total(),
                'current_page' => $records->currentPage(),
                'last_page'    => $records->lastPage(),
            ],
        ]);
    }

    public function store(StoreMedicalRecordRequest $request): JsonResponse
    {
        $record = $this->service->upload(
            $request->file('file'),
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Medical record uploaded successfully.',
            'data'    => new MedicalRecordResource(
                $record->load(['patient.user', 'uploadedBy'])
            ),
        ], 201);
    }

    public function show(MedicalRecord $medicalRecord): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => new MedicalRecordResource(
                $medicalRecord->load(['patient.user', 'uploadedBy', 'appointment'])
            ),
        ]);
    }

    public function download(MedicalRecord $medicalRecord): BinaryFileResponse
    {
        $path = $this->service->getDownloadUrl($medicalRecord);

        return response()->download($path, $medicalRecord->file_name);
    }

    public function destroy(Request $request, MedicalRecord $medicalRecord): JsonResponse
    {
        $this->service->delete($medicalRecord, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Medical record deleted successfully.',
        ]);
    }
}
