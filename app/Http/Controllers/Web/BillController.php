<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillPayment;
use App\Services\BillPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class BillController extends Controller
{
    public function index(Request $request): View
    {
        return view('bills.index', [
            'bills' => $request->user()->bills()->with('payments')->latest('due_date')->paginate(12),
        ]);
    }

    public function pay(Request $request, Bill $bill, BillPaymentService $payments): RedirectResponse
    {
        abort_unless($bill->user_id === $request->user()->id, 404);
        $phone = $this->validatedPhone($request);

        try {
            $payment = $payments->initiate($bill, $request->user(), $phone);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors(['payment' => __('The payment could not be initiated. Please try again.')]);
        }

        $message = match ($payment->status) {
            'COMPLETED' => __('Payment confirmed. Receipt :reference is available in your history.', ['reference' => $payment->transaction_reference]),
            'REVIEW' => __('This payment requires verification. Do not initiate another payment.'),
            default => __('Mobile Money confirmation requested. Approve it on your phone, then check the status.'),
        };

        return redirect()->route('bills.index')->with('success', $message);
    }

    public function refresh(Request $request, BillPayment $payment, BillPaymentService $payments): RedirectResponse
    {
        abort_unless($payment->user_id === $request->user()->id, 404);

        try {
            $payment = $payments->refresh($payment);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors(['payment' => __('The payment status could not be verified. Please try again.')]);
        }

        return back()->with('success', $payment->status === 'COMPLETED'
            ? __('Payment confirmed. Your bill is now paid.')
            : __('Payment status: :status', ['status' => __($payment->status)]));
    }

    private function validatedPhone(Request $request): string
    {
        if (config('delestalert.payments.driver') !== 'digipay') {
            return '237600000000';
        }

        abort_unless(filled(config('services.digipay.key')), 503, 'DigiPay has not been configured.');
        $data = $request->validate([
            'phone' => ['required', 'string', 'regex:/^\\+?2376\\d{8}$/'],
        ]);

        return ltrim($data['phone'], '+');
    }
}
