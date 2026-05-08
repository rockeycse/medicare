<?php

namespace App\Http\Controllers\Api\V1\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\StoreDoctorRequest;
use App\Http\Requests\Doctor\UpdateDoctorRequest;
use App\Http\Requests\Doctor\StoreDoctorScheduleRequest;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\DoctorScheduleResource;
use App\Models\Doctor;
use App\Services\DoctorService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private DoctorService $doctorService) {}

    /**
     * @OA\Get(
     *     path="/api/v1/doctors",
     *     tags={"Doctors"},
     *     summary="List all available doctors",
     *     operationId="listDoctors",
     *     @OA\Parameter(name="specialization_id", in="query", required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(name="search", in="query", required=false,
     *         @OA\Schema(type="string", example="John")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of doctors",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="total", type="integer", example=10),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=2)
     *             )
     *         )
     *     )
     * )
     */

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Doctor::class);

        $doctors = $this->doctorService->list($request->only([
            'specialization_id', 'search'
        ]));

        return response()->json([
            'success' => true,
            'data'    => DoctorResource::collection($doctors),
            'meta'    => [
                'total'        => $doctors->total(),
                'current_page' => $doctors->currentPage(),
                'last_page'    => $doctors->lastPage(),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/doctors",
     *     tags={"Doctors"},
     *     summary="Create a new doctor (Admin only)",
     *     operationId="createDoctor",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","phone","password","password_confirmation","specialization_id","experience_years","consultation_fee","license_number"},
     *             @OA\Property(property="name", type="string", example="Dr. John Smith"),
     *             @OA\Property(property="email", type="string", example="drjohn@medicare.com"),
     *             @OA\Property(property="phone", type="string", example="01712345678"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123"),
     *             @OA\Property(property="specialization_id", type="integer", example=1),
     *             @OA\Property(property="experience_years", type="integer", example=5),
     *             @OA\Property(property="consultation_fee", type="number", example=800),
     *             @OA\Property(property="license_number", type="string", example="LIC-1234"),
     *             @OA\Property(property="bio", type="string", example="Experienced cardiologist")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Doctor created successfully"),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */

    public function store(StoreDoctorRequest $request): JsonResponse
    {
        $this->authorize('create', Doctor::class);

        $doctor = $this->doctorService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Doctor created successfully.',
            'data'    => new DoctorResource(
                $doctor->load(['user', 'specialization'])
            ),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/doctors/{id}",
     *     tags={"Doctors"},
     *     summary="Get doctor details",
     *     operationId="showDoctor",
     *     @OA\Parameter(name="id", in="path", required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Doctor details"),
     *     @OA\Response(response=404, description="Doctor not found")
     * )
     */


    public function show(Doctor $doctor): JsonResponse
    {
        $this->authorize('view', $doctor);

        return response()->json([
            'success' => true,
            'data'    => new DoctorResource(
                $doctor->load(['user', 'specialization', 'schedules'])
            ),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/doctors/{id}",
     *     tags={"Doctors"},
     *     summary="Update doctor profile",
     *     operationId="updateDoctor",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="bio", type="string"),
     *             @OA\Property(property="consultation_fee", type="number", example=1000),
     *             @OA\Property(property="is_available", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Doctor updated successfully"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */

    public function update(UpdateDoctorRequest $request, Doctor $doctor): JsonResponse
    {
        $this->authorize('update', $doctor);

        $doctor = $this->doctorService->update($doctor, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Doctor updated successfully.',
            'data'    => new DoctorResource($doctor),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/doctors/{id}",
     *     tags={"Doctors"},
     *     summary="Delete a doctor (Admin only)",
     *     operationId="deleteDoctor",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Doctor deleted successfully"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */

    public function destroy(Doctor $doctor): JsonResponse
    {
        $this->authorize('delete', Doctor::class);

        $doctor->user->delete(); // SoftDelete cascades
        $doctor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Doctor deleted successfully.',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/doctors/{id}/schedules",
     *     tags={"Doctors"},
     *     summary="Get doctor schedules",
     *     operationId="doctorSchedules",
     *     @OA\Parameter(name="id", in="path", required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Doctor schedules")
     * )
     */

    public function schedules(Doctor $doctor): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => DoctorScheduleResource::collection(
                $doctor->schedules()->where('is_available', true)->get()
            ),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/doctors/{id}/schedules",
     *     tags={"Doctors"},
     *     summary="Sync doctor schedules",
     *     operationId="syncSchedules",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="schedules", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="day_of_week", type="string", example="monday"),
     *                     @OA\Property(property="start_time", type="string", example="09:00"),
     *                     @OA\Property(property="end_time", type="string", example="17:00"),
     *                     @OA\Property(property="max_patients", type="integer", example=20)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Schedules updated successfully")
     * )
     */

    public function syncSchedules(StoreDoctorScheduleRequest $request, Doctor $doctor): JsonResponse
    {
        $this->authorize('update', $doctor);

        $this->doctorService->syncSchedules($doctor, $request->validated()['schedules']);

        return response()->json([
            'success' => true,
            'message' => 'Schedules updated successfully.',
            'data'    => DoctorScheduleResource::collection(
                $doctor->fresh()->schedules
            ),
        ]);
    }
}
