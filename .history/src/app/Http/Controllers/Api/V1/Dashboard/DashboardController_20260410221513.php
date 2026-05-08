<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Contracts\Services\DashboardServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardServiceInterface $service
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/dashboard",
     *     tags={"Dashboard"},
     *     summary="Get dashboard statistics based on user role",
     *     operationId="dashboard",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard statistics",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="overview", type="object",
     *                     @OA\Property(property="total_users", type="integer", example=100),
     *                     @OA\Property(property="total_doctors", type="integer", example=10),
     *                     @OA\Property(property="total_patients", type="integer", example=80),
     *                     @OA\Property(property="total_appointments", type="integer", example=200)
     *                 ),
     *                 @OA\Property(property="appointments", type="object"),
     *                 @OA\Property(property="revenue", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $user  = $request->user();
        $stats = match($user->role) {
            'admin'   => $this->service->getAdminStats(),
            'doctor'  => $this->service->getDoctorStats($user->doctor->id),
            'patient' => $this->service->getPatientStats($user->patient->id),
            default   => [],
        };

        return response()->json([
            'success' => true,
            'data'    => $stats,
        ]);
    }
}
