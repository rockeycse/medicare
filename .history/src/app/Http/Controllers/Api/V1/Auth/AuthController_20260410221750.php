<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="MediCare API",
 *     version="1.0.0",
 *     description="MediCare Hospital Management System - Complete REST API",
 *     @OA\Contact(
 *         email="admin@medicare.com",
 *         name="MediCare Support"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Local Development Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter: Bearer {your_token}"
 * )
 *
 * @OA\Tag(name="Authentication", description="Auth endpoints")
 * @OA\Tag(name="Doctors", description="Doctor management")
 * @OA\Tag(name="Appointments", description="Appointment management")
 * @OA\Tag(name="Prescriptions", description="Prescription management")
 * @OA\Tag(name="Invoices", description="Invoice & payment management")
 * @OA\Tag(name="Medical Records", description="Medical records management")
 * @OA\Tag(name="Dashboard", description="Dashboard statistics")
 * @OA\Tag(name="Notifications", description="Notification management")
 * @OA\Tag(name="Admin", description="Admin only endpoints")
 */

class AuthController extends Controller
{

    public function __construct(private AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'data'    => [
                'user'  => new UserResource($result['user']),
                'token' => $result['token'],
            ],
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data'    => [
                'user'  => new UserResource($result['user']),
                'token' => $result['token'],
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => new UserResource(
                $request->user()->load(['doctor.specialization', 'patient'])
            ),
        ]);
    }
}
