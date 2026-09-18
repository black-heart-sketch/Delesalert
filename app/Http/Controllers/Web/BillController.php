<?php

namespace App\Http\Controllers\Web;

use App\Contracts\BillPaymentGateway;
use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BillController extends Controller
{
    public function index(Request $request): View
    {
        return view('bills.index', [
            'bills' => $request->user()->bills()->with('payments')->latest('due_date')->paginate(12),
        ]);
    }

    public function pay(Request $request, Bill $bill, BillPaymentGateway $paymentGateway): RedirectResponse
    {
        abort_unless($bill->user_id === $request->user()->id, 404);
        abort_unless(config('delestalert.payments.driver') === 'demo', 503, 'A payment provider has not been configured.');

        $payment = DB::transaction(function () use ($bill, $paymentGateway, $request): BillPayment {
            $lockedBill = Bill::query()->lockForUpdate()->findOrFail($bill->id);
            abort_if($lockedBill->status === 'PAID', 422, 'This bill has already been paid.');

            $payment = BillPayment::create([
                'bill_id' => $lockedBill->id,
                'user_id' => $request->user()->id,
                'amount' => $lockedBill->amount_due,
                'currency' => $lockedBill->currency,
                'provider' => mb_strtoupper(config('delestalert.payments.driver')),
                'status' => 'COMPLETED',
                'transaction_reference' => $paymentGateway->charge($lockedBill),
                'paid_at' => now(),
            ]);

            $lockedBill->update(['status' => 'PAID', 'paid_at' => $payment->paid_at]);

            return $payment;
        });

        return redirect()->route('bills.index')->with('success', 'Payment recorded. Receipt '.$payment->transaction_reference.' is available in your payment history.');
    }
}
