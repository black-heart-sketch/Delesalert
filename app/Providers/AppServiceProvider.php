<?php

namespace App\Providers;

use App\Contracts\BillPaymentGateway;
use App\Services\DemoBillPaymentGateway;
use App\Services\DigiPayBillPaymentGateway;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BillPaymentGateway::class, function (): BillPaymentGateway {
            if (config('delestalert.payments.driver') === 'digipay') {
                return new DigiPayBillPaymentGateway;
            }

            return new DemoBillPaymentGateway;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request): Limit {
            return Limit::perMinute(5)->by(Str::transliterate(
                Str::lower($request->string('email')->toString()).'|'.$request->ip(),
            ));
        });

        RateLimiter::for('payments', fn (Request $request): Limit => Limit::perMinute(5)->by(
            (string) ($request->user()?->id ?? $request->ip()),
        ));
    }
}
