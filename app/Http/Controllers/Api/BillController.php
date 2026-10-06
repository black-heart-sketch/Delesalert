<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillPayment;
use App\Services\BillPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class BillController extends Controller
{
    public function index(Request $request): array
    {
        return [
            'success' => true,
            'data' => $request->user()->bills()->with('payments')->latest('due_date')->paginate($request->integer('per_page', 15)),
        ];
    }

    public function store(Request $request, Bill $bill, BillPaymentService $payments): JsonResponse
    {
        abort_unless($bill->user_id === $request->user()->id, 404);
        if (config('delestalert.payments.driver') === 'digipay') {
            abort_unless(filled(config('services.digipay.key')), 503, 'DigiPay has not been configured.');
        }
        $data = $request->validate(['phone' => ['required', 'string', 'regex:/^\\+?2376\\d{8}$/']]);

        try {
            $payment = $payments->initiate($bill, $request->user(), ltrim($data['phone'], '+'));
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(['success' => false, 'message' => 'The payment could not be initiated.'], 502);
        }

        return response()->json(['success' => true, 'data' => $payment], 202);
    }

    public function refresh(Request $request, BillPayment $payment, BillPaymentService $payments): JsonResponse
    {
        abort_unless($payment->user_id === $request->user()->id, 404);

        try {
            $payment = $payments->refresh($payment);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(['success' => false, 'message' => 'The payment status could not be verified.'], 502);
        }

        return response()->json(['success' => true, 'data' => $payment]);
    }
}
