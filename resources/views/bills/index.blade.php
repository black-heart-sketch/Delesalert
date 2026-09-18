<x-layouts.dashboard :title="__('My bills')">
    <div class="flex min-h-[calc(100dvh-8rem)] w-full flex-col gap-6">
        <section class="rounded-3xl bg-slate-950 p-5 text-white shadow-xl sm:p-8 lg:min-h-64 lg:p-10">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-sky-300">{{ __('Bill centre') }}</p>
            <h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Your electricity bills') }}</h1>
            <p class="mt-2 max-w-3xl text-slate-300 sm:text-lg">{{ __('Review what is due, record a payment, and keep your receipts in one place.') }}</p>
        </section>

        <div class="rounded-2xl border border-sky-100 bg-sky-50 px-5 py-4 text-sm leading-6 text-sky-900"><span class="font-bold">{{ __('Demo checkout') }}.</span> {{ __('This environment records a test payment only and never collects card or mobile-money credentials.') }}</div>

        <div class="grid flex-1 gap-5 md:grid-cols-2 2xl:grid-cols-3">
            @forelse($bills as $bill)
                <article class="flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:p-7">
                    <div class="flex items-start justify-between gap-4"><div><p class="text-sm font-semibold text-sky-700">{{ $bill->provider_name }}</p><h2 class="mt-1 text-lg font-bold text-slate-950">{{ $bill->description }}</h2><p class="mt-1 text-sm text-slate-500">{{ __('Account') }}: {{ $bill->account_reference }}</p></div><span @class(['rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide', 'bg-emerald-100 text-emerald-800' => $bill->status === 'PAID', 'bg-amber-100 text-amber-800' => $bill->status !== 'PAID'])>{{ __($bill->status) }}</span></div>
                    <div class="mt-6 flex flex-col gap-3 border-y border-slate-100 py-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-sm text-slate-500">{{ __('Due date') }}</p><p class="mt-1 font-semibold text-slate-800">{{ $bill->due_date->translatedFormat('d F Y') }}</p></div><p class="text-2xl font-bold tracking-tight text-slate-950">{{ number_format((float) $bill->amount_due, 0, '.', ' ') }} <span class="text-base">{{ $bill->currency }}</span></p></div>
                    @if($bill->status === 'PAID')
                        <div class="mt-5 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-900"><p class="font-bold">{{ __('Paid') }} {{ $bill->paid_at?->translatedFormat('d M Y') }}</p>@if($bill->payments->first())<p class="mt-1">{{ __('Receipt') }}: {{ $bill->payments->first()->transaction_reference }}</p>@endif</div>
                    @else
                        <form method="POST" action="{{ route('bills.pay', $bill) }}" class="mt-auto pt-5">@csrf <button class="min-h-12 w-full rounded-xl bg-sky-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-sky-700">{{ __('Pay securely') }}</button></form>
                    @endif
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center"><p class="font-semibold text-slate-950">{{ __('There are no bills to show.') }}</p></div>
            @endforelse
        </div>
        {{ $bills->links() }}
    </div>
</x-layouts.dashboard>
