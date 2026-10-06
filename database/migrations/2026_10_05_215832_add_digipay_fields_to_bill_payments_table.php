<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bill_payments', function (Blueprint $table) {
            $table->string('provider_transaction_id')->nullable()->unique()->after('transaction_reference');
            $table->string('customer_phone', 20)->nullable()->after('provider_transaction_id');
            $table->json('provider_payload')->nullable()->after('customer_phone');
            $table->string('failure_message')->nullable()->after('provider_payload');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill_payments', function (Blueprint $table) {
            $table->dropUnique(['provider_transaction_id']);
            $table->dropColumn(['provider_transaction_id', 'customer_phone', 'provider_payload', 'failure_message']);
        });
    }
};
