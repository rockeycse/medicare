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

    public function show(Invoice $invoice): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => new InvoiceResource(
                $invoice->load(['appointment.doctor.user', 'patient.user', 'payments'])
            ),
        ]);
    }

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
