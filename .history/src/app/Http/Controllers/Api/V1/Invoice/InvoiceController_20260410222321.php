<?php

namespace App\Http\Controllers\Api\V1\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Invoice\StorePaymentRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    /**
     * @OA\Get(
     *     path="/api/v1/invoices",
     *     tags={"Invoices"},
     *     summary="List invoices",
     *     operationId="listInvoices",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="status", in="query", required=false,
     *         @OA\Schema(type="string", enum={"unpaid","paid","refunded"})
     *     ),
     *     @OA\Response(response=200, description="List of invoices")
     * )
     */

    public function index(Request $request): JsonResponse
    {
        $invoices = Invoice::with(['appointment', 'patient.user', 'payments'])
            ->when($request->user()->isPatient(), fn($q) =>
                $q->where('patient_id', $request->user()->patient->id)
            )
            ->when($request->status, fn($q, $status) =>
                $q->where('status', $status)
            )
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => InvoiceResource::collection($invoices),
            'meta'    => [
                'total'        => $invoices->total(),
                'current_page' => $invoices->currentPage(),
                'last_page'    => $invoices->lastPage(),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/invoices",
     *     tags={"Invoices"},
     *     summary="Generate invoice for appointment (Admin/Receptionist only)",
     *     operationId="generateInvoice",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"appointment_id"},
     *             @OA\Property(property="appointment_id", type="integer", example=1),
     *             @OA\Property(property="discount", type="number", example=0),
     *             @OA\Property(property="tax", type="number", example=0)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Invoice generated successfully"),
     *     @OA\Response(response=422, description="Invoice already exists")
     * )
     */

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $invoice = $this->invoiceService->generate($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Invoice generated successfully.',
            'data'    => new InvoiceResource(
                $invoice->load(['appointment', 'patient.user'])
            ),
        ], 201);
    }

     /**
     * @OA\Get(
     *     path="/api/v1/invoices/{id}",
     *     tags={"Invoices"},
     *     summary="Get invoice details",
     *     operationId="showInvoice",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Invoice details"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */

    public function show(Invoice $invoice): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => new InvoiceResource(
                $invoice->load(['appointment.doctor.user', 'patient.user', 'payments'])
            ),
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/v1/invoices/{id}/pay",
     *     tags={"Invoices"},
     *     summary="Pay an invoice",
     *     operationId="payInvoice",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"method"},
     *             @OA\Property(property="method", type="string", enum={"cash","card","mobile_banking"}, example="cash"),
     *             @OA\Property(property="transaction_id", type="string", example="TXN123456")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Payment successful"),
     *     @OA\Response(response=422, description="Already paid")
     * )
     */

    public function pay(StorePaymentRequest $request, Invoice $invoice): JsonResponse
    {
        $invoice = $this->invoiceService->pay($invoice, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Payment successful.',
            'data'    => new InvoiceResource($invoice),
        ]);
    }

    public function refund(Invoice $invoice): JsonResponse
    {
        $invoice = $this->invoiceService->refund($invoice);

        return response()->json([
            'success' => true,
            'message' => 'Invoice refunded successfully.',
            'data'    => new InvoiceResource($invoice),
        ]);
    }
}
