<?php

namespace App\Http\Controllers\Api\V1\Appointment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(private AppointmentService $appointmentService) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Appointment::class);

        $appointments = Appointment::with(['doctor.user', 'patient.user'])
            ->when($request->user()->isPatient(), fn($q) =>
                $q->where('patient_id', $request->user()->patient->id)
            )
            ->when($request->user()->isDoctor(), fn($q) =>
                $q->where('doctor_id', $request->user()->doctor->id)
            )
            ->when($request->status, fn($q, $status) =>
                $q->where('status', $status)
            )
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => AppointmentResource::collection($appointments),
            'meta'    => [
                'total'        => $appointments->total(),
                'current_page' => $appointments->currentPage(),
                'last_page'    => $appointments->lastPage(),
            ],
        ]);
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $this->authorize('create', Appointment::class);

        $appointment = $this->appointmentService->book(
            $request->validated(),
            $request->user()->patient->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Appointment booked successfully.',
            'data'    => new AppointmentResource($appointment),
        ], 201);
    }

    public function show(Appointment $appointment): JsonResponse
    {
        $this->authorize('view', $appointment);

        return response()->json([
            'success' => true,
            'data'    => new AppointmentResource(
                $appointment->load(['doctor.user', 'patient.user', 'prescription', 'invoice'])
            ),
        ]);
    }

    public function cancel(Appointment $appointment): JsonResponse
    {
        $this->authorize('cancel', $appointment);

        $appointment = $this->appointmentService->cancel($appointment);

        return response()->json([
            'success' => true,
            'message' => 'Appointment cancelled.',
            'data'    => new AppointmentResource($appointment),
        ]);
    }
}
