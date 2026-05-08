<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Doctor\DoctorController;
use App\Http\Controllers\Api\V1\Appointment\AppointmentController;
use App\Http\Controllers\Api\V1\Prescription\PrescriptionController;
use App\Http\Controllers\Api\V1\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Public routes
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login',    [AuthController::class, 'login']);
    });

    Route::get('doctors',       [DoctorController::class, 'index']);
    Route::get('doctors/{doctor}', [DoctorController::class, 'show']);

    // Authenticated routes
    Route::middleware('auth:api')->group(function () {

        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me',      [AuthController::class, 'me']);

        // Appointments
        Route::apiResource('appointments', AppointmentController::class)
            ->except(['update', 'destroy']);
        Route::patch(
            'appointments/{appointment}/cancel',
            [AppointmentController::class, 'cancel']
        );

        // Prescriptions
        Route::apiResource('prescriptions', PrescriptionController::class)
            ->only(['index', 'show', 'store']);

        // Admin only
        Route::middleware('role:admin')->prefix('admin')->group(function () {
            Route::apiResource('users', UserController::class);
        });

        // Doctor only
        Route::middleware('role:doctor')->prefix('doctor')->group(function () {
            Route::get('appointments', [AppointmentController::class, 'index']);
            Route::patch(
                'appointments/{appointment}/complete',
                [AppointmentController::class, 'complete']
            );
        });
    });
});


// Doctor routes (public)
Route::get('doctors',                   [DoctorController::class, 'index']);
Route::get('doctors/{doctor}',          [DoctorController::class, 'show']);
Route::get('doctors/{doctor}/schedules', [DoctorController::class, 'schedules']);

// Authenticated routes
Route::middleware('auth:api')->group(function () {

    // Doctor management (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::post('doctors',           [DoctorController::class, 'store']);
        Route::delete('doctors/{doctor}', [DoctorController::class, 'destroy']);

        // User management
        Route::apiResource('admin/users', UserController::class);
        Route::patch(
            'admin/users/{user}/toggle-status',
            [UserController::class, 'toggleStatus']
        );
    });

    // Doctor can update own profile & schedules
    Route::middleware('role:admin,doctor')->group(function () {
        Route::put('doctors/{doctor}',   [DoctorController::class, 'update']);
        Route::post(
            'doctors/{doctor}/schedules',
            [DoctorController::class, 'syncSchedules']
        );
    });

    // Prescriptions
    Route::apiResource('prescriptions', PrescriptionController::class)
        ->only(['index', 'show', 'store']);
});
