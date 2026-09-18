<?php

namespace App\Contracts;

use App\Models\Bill;

interface BillPaymentGateway
{
    public function charge(Bill $bill): string;
}
