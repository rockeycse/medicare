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

    public function schedules(Doctor $doctor): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => DoctorScheduleResource::collection(
                $doctor->schedules()->where('is_available', true)->get()
            ),
        ]);
    }

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
