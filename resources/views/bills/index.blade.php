<x-layouts.dashboard :title="__('My bills')">
    <div class="flex min-h-[calc(100dvh-8rem)] w-full flex-col gap-6">
        <section class="rounded-3xl bg-slate-950 p-5 text-white shadow-xl sm:p-8 lg:min-h-64 lg:p-10">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-sky-300">{{ __('Bill centre') }}</p>
            <h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Your electricity bills') }}</h1>
            <p class="mt-2 max-w-3xl text-slate-300 sm:text-lg">{{ __('Review what is due, pay securely with Mobile Money, and keep your receipts in one place.') }}</p>
        </section>

        @if(config('delestalert.payments.driver') === 'digipay')
            <div class="rounded-2xl border border-sky-100 bg-sky-50 px-5 py-4 text-sm leading-6 text-sky-900"><span class="font-bold">{{ __('DigiPay Mobile Money') }}.</span> {{ __('The amount comes directly from your bill. DelestAlert never asks for your Mobile Money PIN.') }}</div>
        @else
            <div class="rounded-2xl border border-amber-100 bg-amber-50 px-5 py-4 text-sm leading-6 text-amber-900"><span class="font-bold">{{ __('Demo checkout') }}.</span> {{ __('This environment records a test payment only and never collects card or mobile-money credentials.') }}</div>
        @endif

        @error('payment')<div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-800" role="alert">{{ $message }}</div>@enderror

        <div class="grid flex-1 gap-5 md:grid-cols-2 2xl:grid-cols-3">
            @forelse($bills as $bill)
                @php($latestPayment = $bill->payments->first())
                <article class="flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:p-7">
                    <div class="flex items-start justify-between gap-4"><div><p class="text-sm font-semibold text-sky-700">{{ $bill->provider_name }}</p><h2 class="mt-1 text-lg font-bold text-slate-950">{{ $bill->description }}</h2><p class="mt-1 text-sm text-slate-500">{{ __('Account') }}: {{ $bill->account_reference }}</p></div><span @class(['rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide', 'bg-emerald-100 text-emerald-800' => $bill->status === 'PAID', 'bg-amber-100 text-amber-800' => $bill->status !== 'PAID'])>{{ __($bill->status) }}</span></div>
                    <div class="mt-6 flex flex-col gap-3 border-y border-slate-100 py-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-sm text-slate-500">{{ __('Due date') }}</p><p class="mt-1 font-semibold text-slate-800">{{ $bill->due_date->translatedFormat('d F Y') }}</p></div><p class="text-2xl font-bold tracking-tight text-slate-950">{{ number_format((float) $bill->amount_due, 0, '.', ' ') }} <span class="text-base">{{ $bill->currency }}</span></p></div>
                    @if($bill->status === 'PAID')
                        <div class="mt-5 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-900"><p class="font-bold">{{ __('Paid') }} {{ $bill->paid_at?->translatedFormat('d M Y') }}</p>@if($latestPayment)<p class="mt-1">{{ __('Receipt') }}: {{ $latestPayment->transaction_reference }}</p>@endif</div>
                    @elseif($latestPayment && in_array($latestPayment->status, ['INITIATING', 'PENDING'], true))
                        <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-950"><p class="font-bold">{{ __('Mobile Money confirmation pending') }}</p><p class="mt-1 leading-6">{{ __('Approve the request on :phone, then check the transaction status.', ['phone' => $latestPayment->customer_phone]) }}</p><p class="mt-2 text-xs text-amber-800">{{ __('Reference') }}: {{ $latestPayment->provider_transaction_id ?: $latestPayment->transaction_reference }}</p></div>
                        @if($latestPayment->provider_transaction_id)<form method="POST" action="{{ route('bill-payments.refresh', $latestPayment) }}" class="mt-auto pt-4">@csrf <button class="min-h-12 w-full rounded-xl bg-slate-950 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-800">{{ __('Check payment status') }}</button></form>@endif
                    @elseif($latestPayment && $latestPayment->status === 'REVIEW')
                        <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-950"><p class="font-bold">{{ __('Payment verification required') }}</p><p class="mt-1 leading-6">{{ __('Do not start another payment. Contact support with reference :reference.', ['reference' => $latestPayment->transaction_reference]) }}</p></div>
                    @else
                        <form method="POST" action="{{ route('bills.pay', $bill) }}" class="mt-auto space-y-3 pt-5">@csrf @if(config('delestalert.payments.driver') === 'digipay')<div><label for="phone-{{ $bill->id }}" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('Mobile Money number') }}</label><input id="phone-{{ $bill->id }}" name="phone" value="{{ old('phone', auth()->user()->phone) }}" inputmode="tel" autocomplete="tel" placeholder="237699000000" required pattern="\+?2376[0-9]{8}" class="h-12 w-full rounded-xl border border-slate-300 px-3 outline-none focus:border-sky-600 focus:ring-4 focus:ring-sky-100"><p class="mt-1 text-xs text-slate-500">{{ __('Cameroon number in international format.') }}</p></div>@endif<button class="min-h-12 w-full rounded-xl bg-sky-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-sky-700">{{ __('Pay :amount XAF securely', ['amount' => number_format((float) $bill->amount_due, 0, '.', ' ')]) }}</button></form>
                    @endif
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center"><p class="font-semibold text-slate-950">{{ __('There are no bills to show.') }}</p></div>
            @endforelse
        </div>
        {{ $bills->links() }}
    </div>
</x-layouts.dashboard>
