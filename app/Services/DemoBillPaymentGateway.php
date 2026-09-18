<?php

namespace App\Services;

use App\Contracts\BillPaymentGateway;
use App\Models\Bill;
use Illuminate\Support\Str;

class DemoBillPaymentGateway implements BillPaymentGateway
{
    public function charge(Bill $bill): string
    {
        return 'DEMO-'.Str::upper(Str::random(12));
    }
}
