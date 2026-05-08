<?php

namespace App\Http\Controllers;


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

abstract class Controller
{
    //
}
