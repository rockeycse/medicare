<?php

namespace App\Http\Controllers\Api\V1\Appointment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    use AuthorizesRequests;
    public function __construct(private AppointmentService $appointmentService) {}


    /**
     * @OA\Get(
     *     path="/api/v1/appointments",
     *     tags={"Appointments"},
     *     summary="List appointments",
     *     operationId="listAppointments",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="status", in="query", required=false,
     *         @OA\Schema(type="string", enum={"pending","confirmed","completed","cancelled"})
     *     ),
     *     @OA\Response(response=200, description="List of appointments"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Appointment::class);

        $appointments = Appointment::with(['doctor.user', 'patient.user'])
            ->when(
                $request->user()->isPatient(),
                fn($q) =>
                $q->where('patient_id', $request->user()->patient->id)
            )
            ->when(
                $request->user()->isDoctor(),
                fn($q) =>
                $q->where('doctor_id', $request->user()->doctor->id)
            )
            ->when(
                $request->status,
                fn($q, $status) =>
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

     /**
     * @OA\Post(
     *     path="/api/v1/appointments",
     *     tags={"Appointments"},
     *     summary="Book a new appointment (Patient only)",
     *     operationId="bookAppointment",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"doctor_schedule_id","appointment_date","appointment_time","type"},
     *             @OA\Property(property="doctor_schedule_id", type="integer", example=1),
     *             @OA\Property(property="appointment_date", type="string", format="date", example="2026-04-20"),
     *             @OA\Property(property="appointment_time", type="string", example="10:00"),
     *             @OA\Property(property="type", type="string", enum={"in-person","online"}, example="in-person"),
     *             @OA\Property(property="symptoms", type="string", example="Fever and headache")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Appointment booked successfully"),
     *     @OA\Response(response=403, description="Only patients can book appointments"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        if (!$request->user()->isPatient()) {
            return response()->json([
                'success' => false,
                'message' => 'Only patients can book appointments.',
            ], 403);
        }

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
