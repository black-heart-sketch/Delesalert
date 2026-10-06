@props([
    'mapPayload',
    'mapTileUrl',
    'title',
    'description',
    'eyebrow' => __('Interactive Grid Map'),
    'compact' => false,
])

<section data-live-map-shell {{ $attributes }}>
    <div @class(['mx-auto max-w-7xl px-4 sm:px-6 lg:px-8' => ! $compact])>
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-sky-800">📍 {{ __($eyebrow) }}</span>
                <h2 @class(['mt-3 font-bold tracking-tight text-slate-950', 'text-3xl sm:text-4xl' => ! $compact, 'text-2xl sm:text-3xl' => $compact])>{{ __($title) }}</h2>
                <p class="mt-2 max-w-2xl text-base text-slate-600">{{ __($description) }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700"><span class="size-3 rounded-full bg-rose-500"></span> {{ __('Active Outage') }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700"><span class="size-3 rounded-full bg-amber-500"></span> {{ __('Scheduled') }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700"><span class="size-3 rounded-full bg-emerald-500"></span> {{ __('Grid Normal') }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700"><span class="size-3 rounded-full bg-violet-600"></span> {{ __('High AI risk') }}</span>
            </div>
        </div>

        @if($mapPayload['zones']->isNotEmpty())
            <div class="mt-6 flex flex-wrap items-center gap-2 overflow-x-auto pb-2" aria-label="{{ __('Filter map by city') }}">
                <button type="button" data-map-city-filter="all" aria-pressed="true" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-slate-800">🇨🇲 {{ __('All Cameroon') }}</button>
                @foreach($mapPayload['zones']->pluck('city')->unique()->values() as $city)
                    <button type="button" data-map-city-filter="{{ $city }}" aria-pressed="false" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">{{ $city }}</button>
                @endforeach
                <button type="button" data-map-locate class="ml-auto inline-flex items-center gap-1.5 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2 text-xs font-bold text-sky-800 transition hover:bg-sky-100">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path stroke-linecap="round" d="M12 2v3m0 14v3M2 12h3m14 0h3"/></svg>
                    {{ __('My position') }}
                </button>
            </div>

            <div class="mt-4 grid overflow-hidden rounded-3xl border border-slate-300 bg-white shadow-xl lg:grid-cols-[minmax(0,1fr)_21rem]">
                <div @class(['relative', 'min-h-[28rem] lg:min-h-[34rem]' => ! $compact, 'min-h-[24rem] lg:min-h-[30rem]' => $compact])>
                    <div data-live-map data-tile-url="{{ $mapTileUrl }}" class="absolute inset-0 z-0 bg-slate-200" aria-label="{{ __('Interactive map of electricity zones') }}"></div>
                    <div class="pointer-events-none absolute left-4 top-4 z-[500] rounded-xl border border-white/70 bg-white/95 px-3 py-2 text-xs font-semibold text-slate-700 shadow-lg backdrop-blur">
                        <span class="inline-flex items-center gap-2"><span class="size-2 animate-pulse rounded-full bg-emerald-500"></span>{{ trans_choice(':count mapped zone|:count mapped zones', $mapPayload['zones']->count(), ['count' => $mapPayload['zones']->count()]) }}</span>
                    </div>
                </div>
                <aside @class(['overflow-y-auto border-t border-slate-200 bg-slate-50 lg:border-l lg:border-t-0', 'max-h-[34rem]' => ! $compact, 'max-h-[30rem]' => $compact]) aria-label="{{ __('Mapped zones') }}">
                    <div class="sticky top-0 z-10 border-b border-slate-200 bg-white/95 p-4 backdrop-blur">
                        <h3 class="font-bold text-slate-950">{{ __('Zone information') }}</h3>
                        <p class="mt-1 text-xs leading-5 text-slate-500">{{ __('Select a zone to center the map and view its details.') }}</p>
                    </div>
                    <div class="divide-y divide-slate-200">
                        @foreach($mapPayload['zones'] as $zone)
                            @php
                                $statusClasses = match ($zone['status']) {
                                    'outage' => 'bg-rose-500',
                                    'planned' => 'bg-amber-500',
                                    'risk' => 'bg-violet-600',
                                    default => 'bg-emerald-500',
                                };
                                $statusLabel = match ($zone['status']) {
                                    'outage' => __('Outage in progress'),
                                    'planned' => __('Scheduled maintenance'),
                                    'risk' => __('High AI risk'),
                                    default => __('Grid stable'),
                                };
                            @endphp
                            <button type="button" data-map-zone-card data-map-zone="{{ $zone['id'] }}" data-map-city="{{ $zone['city'] }}" class="flex w-full gap-3 p-4 text-left transition hover:bg-white focus-visible:bg-white focus-visible:outline-2 focus-visible:outline-inset focus-visible:outline-sky-600">
                                <span class="mt-1.5 size-3 shrink-0 rounded-full {{ $statusClasses }}"></span>
                                <span class="min-w-0 flex-1">
                                    <span class="flex items-start justify-between gap-2"><strong class="text-sm text-slate-950">{{ $zone['name'] }}</strong><span class="shrink-0 text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $statusLabel }}</span></span>
                                    <span class="mt-1 block text-xs text-slate-500">{{ $zone['city'] }} · {{ $zone['region'] }}</span>
                                    <span class="mt-2 flex flex-wrap gap-x-3 gap-y-1 text-[11px] font-medium text-slate-600">
                                        <span>{{ $zone['activeOutages'] }} {{ __('active') }}</span>
                                        <span>{{ $zone['openIncidents'] }} {{ __('incidents') }}</span>
                                        @if($zone['prediction'])<span>{{ $zone['prediction']['probability'] }}% {{ __('risk') }}</span>@endif
                                    </span>
                                </span>
                            </button>
                        @endforeach
                    </div>
                </aside>
            </div>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500">
                <span>{{ __('Map data from OpenStreetMap. Operational information comes from DelestAlert records.') }}</span>
                <a href="{{ route('reports.create') }}" class="rounded-lg bg-slate-900 px-3 py-2 font-semibold text-white transition hover:bg-slate-700">📢 {{ __('Report an outage in your area') }}</a>
            </div>
        @else
            <div class="mt-6 rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center shadow-sm">
                <p class="font-semibold text-slate-950">{{ __('No mapped zone is available yet.') }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ __('Zones will appear here as soon as their coordinates are registered.') }}</p>
            </div>
        @endif
    </div>

    <script>window.delestAlertMap = {{ Illuminate\Support\Js::from($mapPayload) }};</script>
</section>
