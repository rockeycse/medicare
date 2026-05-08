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

    public function index(Request $request): JsonResponse
    {
        $user  = $request->user();
        $stats = match($user->role) {
            'admin'       => $this->service->getAdminStats(),
            'doctor'      => $this->service->getDoctorStats($user->doctor->id),
            'patient'     => $this->service->getPatientStats($user->patient->id),
            default       => [],
        };

        return response()->json([
            'success' => true,
            'data'    => $stats,
        ]);
    }
}
