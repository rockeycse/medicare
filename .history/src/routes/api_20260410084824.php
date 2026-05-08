<?php

use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\Appointment\AppointmentController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Doctor\DoctorController;
use App\Http\Controllers\Api\V1\Invoice\InvoiceController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\Prescription\PrescriptionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ===== Public Routes =====
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login',    [AuthController::class, 'login']);
    });

    Route::get('doctors',                    [DoctorController::class, 'index']);
    Route::get('doctors/{doctor}',           [DoctorController::class, 'show']);
    Route::get('doctors/{doctor}/schedules', [DoctorController::class, 'schedules']);

    // ===== Authenticated Routes =====
    Route::middleware('auth:api')->group(function () {

        // Auth
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me',      [AuthController::class, 'me']);

        // ===== Admin Only =====
        Route::middleware('role:admin')->group(function () {
            Route::post('doctors',            [DoctorController::class, 'store']);
            Route::delete('doctors/{doctor}', [DoctorController::class, 'destroy']);

            Route::apiResource('admin/users', UserController::class);
            Route::patch(
                'admin/users/{user}/toggle-status',
                [UserController::class, 'toggleStatus']
            );
        });

        // ===== Admin & Doctor =====
        Route::middleware('role:admin,doctor')->group(function () {
            Route::put('doctors/{doctor}', [DoctorController::class, 'update']);
            Route::post(
                'doctors/{doctor}/schedules',
                [DoctorController::class, 'syncSchedules']
            );
            Route::patch(
                'appointments/{appointment}/complete',
                [AppointmentController::class, 'complete']
            );
        });

        // ===== Appointments =====
        Route::apiResource('appointments', AppointmentController::class)
             ->except(['update', 'destroy']);
        Route::patch(
            'appointments/{appointment}/cancel',
            [AppointmentController::class, 'cancel']
        );

        // ===== Prescriptions =====
        Route::apiResource('prescriptions', PrescriptionController::class)
             ->only(['index', 'show', 'store']);

        // ===== Invoices =====
        Route::get('invoices',                   [InvoiceController::class, 'index']);
        Route::get('invoices/{invoice}',         [InvoiceController::class, 'show']);
        Route::post('invoices',                  [InvoiceController::class, 'store']);
        Route::post('invoices/{invoice}/pay',    [InvoiceController::class, 'pay']);
        Route::post('invoices/{invoice}/refund', [InvoiceController::class, 'refund']);

        // ===== Notifications =====
        Route::get('notifications',              [NotificationController::class, 'index']);
        Route::patch('notifications/read-all',   [NotificationController::class, 'markAllAsRead']);
        Route::patch('notifications/{id}/read',  [NotificationController::class, 'markAsRead']);
        Route::delete('notifications/{id}',      [NotificationController::class, 'destroy']);

        // ===== Broadcasting Auth =====
        Route::post('broadcasting/auth', function () {
            return broadcast()->auth(request());
        });
    });
});
