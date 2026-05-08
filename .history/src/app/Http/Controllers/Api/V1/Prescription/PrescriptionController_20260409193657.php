<?php

namespace App\Http\Controllers\Api\V1\Prescription;

use App\Http\Controllers\Controller;
use App\Http\Requests\Prescription\StorePrescriptionRequest;
use App\Http\Resources\PrescriptionResource;
use App\Models\Prescription;
use App\Services\PrescriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function __construct(private PrescriptionService $prescriptionService) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Prescription::class);

        $prescriptions = Prescription::with(['doctor.user', 'patient.user', 'medicines'])
            ->when($request->user()->isDoctor(), fn($q) =>
                $q->where('doctor_id', $request->user()->doctor->id)
            )
            ->when($request->user()->isPatient(), fn($q) =>
                $q->where('patient_id', $request->user()->patient->id)
            )
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => PrescriptionResource::collection($prescriptions),
            'meta'    => [
                'total'        => $prescriptions->total(),
                'current_page' => $prescriptions->currentPage(),
                'last_page'    => $prescriptions->lastPage(),
            ],
        ]);
    }

    public function store(StorePrescriptionRequest $request): JsonResponse
    {
        $this->authorize('create', Prescription::class);

        $prescription = $this->prescriptionService->create(
            $request->validated(),
            $request->user()->doctor->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Prescription created successfully.',
            'data'    => new PrescriptionResource($prescription),
        ], 201);
    }

    public function show(Prescription $prescription): JsonResponse
    {
        $this->authorize('view', $prescription);

        return response()->json([
            'success' => true,
            'data'    => new PrescriptionResource(
                $prescription->load(['doctor.user', 'patient.user', 'medicines', 'appointment'])
            ),
        ]);
    }
}
