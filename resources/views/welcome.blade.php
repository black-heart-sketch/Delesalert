<x-layouts.app :title="__('Clarity when the power goes out.')">
    {{-- Ambient Background Glows --}}
    <div class="relative overflow-hidden bg-slate-950 text-white">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_80%_60%_at_50%_-20%,rgba(3,105,161,0.35),rgba(255,255,255,0))]"></div>
        <div class="pointer-events-none absolute -top-40 right-0 size-[500px] rounded-full bg-sky-500/15 blur-3xl"></div>
        <div class="pointer-events-none absolute top-1/2 left-0 size-[450px] rounded-full bg-amber-500/10 blur-3xl"></div>

        {{-- Live Network Pulse Bar --}}
        <div class="border-b border-white/10 bg-black/40 px-4 py-2.5 backdrop-blur-md">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/20 px-2.5 py-0.5 font-semibold text-emerald-300 border border-emerald-500/30">
                        <span class="size-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                        <span class="size-1.5 rounded-full bg-emerald-400"></span>
                        {{ __('Live Grid Pulse') }}
                    </span>
                    <span class="hidden text-slate-300 sm:inline">{{ __('Cameroon Electricity Network Monitor') }} · 🇨🇲</span>
                </div>
                <div class="flex flex-wrap items-center gap-4 text-slate-300">
                    <div class="flex items-center gap-1.5">
                        <span class="text-slate-400">Douala:</span>
                        <span class="font-semibold text-emerald-400">92% {{ __('Stable') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-slate-400">Yaoundé:</span>
                        <span class="font-semibold text-amber-400">{{ __('1 Maintenance') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-slate-400">Bafoussam:</span>
                        <span class="font-semibold text-emerald-400">98% {{ __('Stable') }}</span>
                    </div>
                    <div class="hidden md:flex items-center gap-1.5">
                        <span class="text-slate-400">Garoua:</span>
                        <span class="font-semibold text-emerald-400">100% {{ __('Normal') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- HERO SECTION --}}
        <section class="relative mx-auto max-w-7xl px-4 pt-12 pb-20 sm:px-6 lg:px-8 lg:pt-20 lg:pb-28">
            <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-8">
                {{-- Hero Copy --}}
                <div class="lg:col-span-7">
                    <div class="inline-flex items-center gap-2 rounded-full border border-sky-400/30 bg-sky-950/70 px-3.5 py-1.5 text-xs font-semibold text-sky-200 backdrop-blur-md">
                        <span class="flex size-2 rounded-full bg-sky-400 shadow-[0_0_8px_rgba(56,189,248,0.8)]"></span>
                        <span>{{ __('Electricity information for Cameroon') }}</span>
                        <span class="text-sky-400/60">·</span>
                        <span class="text-amber-300 font-medium">⚡ {{ __('Real-Time & Predictive') }}</span>
                    </div>

                    <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl lg:leading-[1.12]">
                        {{ __('Clarity when the power goes out.') }}
                        <span class="block mt-2 bg-gradient-to-r from-sky-400 via-sky-200 to-amber-300 bg-clip-text text-transparent">
                            {{ __('Never get surprised by load shedding.') }}
                        </span>
                    </h1>

                    <p class="mt-6 max-w-2xl text-lg leading-relaxed text-slate-300 sm:text-xl">
                        {{ __('Check confirmed outages, receive alerts for places that matter to you, and prepare ahead with transparent risk forecasts.') }}
                    </p>

                    {{-- CTA Group --}}
                    <div class="mt-8 flex flex-wrap items-center gap-3.5">
                        <a href="{{ route('outages.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-6 py-3.5 text-base font-semibold text-white shadow-lg shadow-sky-600/30 transition hover:bg-sky-500 hover:shadow-sky-600/50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            {{ __('View current outages') }}
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-6 py-3.5 text-base font-semibold text-white backdrop-blur-sm transition hover:bg-white/20 hover:border-white/40">
                            {{ __('Create account') }}
                        </a>
                        <a href="#live-map" class="inline-flex items-center justify-center gap-1.5 text-sm font-semibold text-sky-300 hover:text-sky-200 px-3 py-2">
                            <span>{{ __('Open Grid Map') }}</span>
                            <span aria-hidden="true">↓</span>
                        </a>
                    </div>

                    {{-- Quick Zone Search --}}
                    <div class="mt-10 rounded-2xl border border-white/15 bg-white/5 p-4 backdrop-blur-md max-w-xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2.5">
                            🔍 {{ __('Quick search by neighborhood or city') }}
                        </p>
                        <form method="GET" action="{{ route('outages.index') }}" class="flex gap-2">
                            <input type="text" name="search" placeholder="{{ __('e.g. Bonamoussadi, Bastos, Akwa, Biyem-Assi...') }}" class="w-full rounded-xl border border-white/15 bg-slate-900/80 px-3.5 py-2.5 text-sm text-white placeholder-slate-400 outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/30">
                            <button type="submit" class="shrink-0 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-500">
                                {{ __('Check') }}
                            </button>
                        </form>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-400">
                            <span>{{ __('Popular:') }}</span>
                            <a href="{{ route('outages.index', ['search' => 'Bonamoussadi']) }}" class="hover:text-sky-300 underline decoration-slate-600">Bonamoussadi</a>
                            <a href="{{ route('outages.index', ['search' => 'Bastos']) }}" class="hover:text-sky-300 underline decoration-slate-600">Bastos</a>
                            <a href="{{ route('outages.index', ['search' => 'Akwa']) }}" class="hover:text-sky-300 underline decoration-slate-600">Akwa</a>
                            <a href="{{ route('outages.index', ['search' => 'Biyem-Assi']) }}" class="hover:text-sky-300 underline decoration-slate-600">Biyem-Assi</a>
                            <a href="{{ route('outages.index', ['search' => 'Makepe']) }}" class="hover:text-sky-300 underline decoration-slate-600">Makepe</a>
                        </div>
                    </div>
                </div>

                {{-- Hero Live Outage Radar Interactive Card --}}
                <div class="lg:col-span-5">
                    <div class="relative rounded-3xl border border-white/15 bg-gradient-to-b from-slate-900/90 to-slate-950/95 p-6 shadow-2xl shadow-sky-950/50 backdrop-blur-xl sm:p-7">
                        {{-- Card Header --}}
                        <div class="flex items-center justify-between border-b border-white/10 pb-4">
                            <div class="flex items-center gap-2.5">
                                <span class="relative flex size-3">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-rose-400 opacity-75"></span>
                                    <span class="relative inline-flex size-3 rounded-full bg-rose-500"></span>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-white">{{ __('Active Incident Radar') }}</h3>
                                    <p class="text-[11px] text-slate-400">{{ __('Live Provider & Citizen Feed') }}</p>
                                </div>
                            </div>
                            <span class="rounded-full bg-rose-500/20 px-2.5 py-1 text-xs font-bold text-rose-300 border border-rose-500/30">
                                2 {{ __('Active Alerts') }}
                            </span>
                        </div>

                        {{-- Alert Item 1: Unplanned Outage --}}
                        <div class="mt-5 rounded-2xl border border-rose-500/30 bg-rose-950/30 p-4 transition hover:border-rose-500/50">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="rounded-md bg-rose-500/20 px-2 py-0.5 text-[11px] font-bold text-rose-300 uppercase tracking-wide">
                                        {{ __('Emergency Outage') }}
                                    </span>
                                    <span class="text-xs text-slate-400">· Douala V</span>
                                </div>
                                <span class="text-xs font-mono font-semibold text-rose-300">~1h 45m {{ __('left') }}</span>
                            </div>
                            <h4 class="mt-2 text-base font-bold text-white">Bonamoussadi & Makepe</h4>
                            <p class="mt-1 text-xs leading-relaxed text-slate-300">
                                {{ __('Feeder 15kV tripped due to transformer overload. Technical dispatch team on site.') }}
                            </p>
                            <div class="mt-3 flex items-center justify-between border-t border-rose-500/20 pt-2.5 text-[11px] text-slate-400">
                                <span class="flex items-center gap-1.5 text-sky-300">
                                    <svg class="size-3.5 text-sky-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                    {{ __('Verified by ENEO Operations') }}
                                </span>
                                <span class="font-medium text-slate-300">84 {{ __('citizens verified') }}</span>
                            </div>
                        </div>

                        {{-- Alert Item 2: Scheduled Maintenance --}}
                        <div class="mt-3.5 rounded-2xl border border-amber-500/30 bg-amber-950/20 p-4 transition hover:border-amber-500/50">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="rounded-md bg-amber-500/20 px-2 py-0.5 text-[11px] font-bold text-amber-300 uppercase tracking-wide">
                                        {{ __('Scheduled Maintenance') }}
                                    </span>
                                    <span class="text-xs text-slate-400">· Yaoundé I</span>
                                </div>
                                <span class="text-xs font-mono font-semibold text-amber-300">{{ __('Tomorrow') }} 08:00</span>
                            </div>
                            <h4 class="mt-2 text-base font-bold text-white">Bastos & Golf (Yaoundé)</h4>
                            <p class="mt-1 text-xs leading-relaxed text-slate-300">
                                {{ __('Substation transformer replacement. Duration estimated at 4 hours.') }}
                            </p>
                            <div class="mt-3 flex items-center justify-between border-t border-amber-500/20 pt-2.5 text-[11px] text-slate-400">
                                <span class="text-amber-200">{{ __('Official Schedule Published') }}</span>
                                <span class="text-slate-300 font-medium">08:00 - 12:00</span>
                            </div>
                        </div>

                        {{-- Card Action --}}
                        <div class="mt-5 flex items-center justify-between rounded-xl bg-white/5 px-4 py-3 border border-white/10">
                            <div class="flex items-center gap-2.5">
                                <svg class="size-5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                <span class="text-xs font-semibold text-white">{{ __('Track your home or business') }}</span>
                            </div>
                            <a href="{{ route('register') }}" class="rounded-lg bg-sky-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-sky-500">
                                {{ __('Enable Alerts') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- STATS & METRICS BAR --}}
    <section class="border-y border-slate-200 bg-white py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                <div class="text-center sm:text-left">
                    <p class="text-3xl font-extrabold tracking-tight text-slate-950 lg:text-4xl">10</p>
                    <p class="mt-1 text-sm font-semibold text-sky-700">{{ __('Regions Monitored') }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">{{ __('Complete Cameroon coverage') }}</p>
                </div>
                <div class="text-center sm:text-left">
                    <p class="text-3xl font-extrabold tracking-tight text-slate-950 lg:text-4xl">45+</p>
                    <p class="mt-1 text-sm font-semibold text-sky-700">{{ __('Urban Zones Tracked') }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">{{ __('Douala, Yaoundé, Bafoussam...') }}</p>
                </div>
                <div class="text-center sm:text-left">
                    <p class="text-3xl font-extrabold tracking-tight text-slate-950 lg:text-4xl">2-4h</p>
                    <p class="mt-1 text-sm font-semibold text-sky-700">{{ __('Advance Notice') }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">{{ __('Predictive risk forecasting') }}</p>
                </div>
                <div class="text-center sm:text-left">
                    <p class="text-3xl font-extrabold tracking-tight text-slate-950 lg:text-4xl">100%</p>
                    <p class="mt-1 text-sm font-semibold text-sky-700">{{ __('Free for Citizens') }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">{{ __('Open public access & reports') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- GOOGLE MAP SECTION --}}
    <section id="live-map" class="scroll-mt-20 border-b border-slate-200 bg-slate-50 py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-100 px-3 py-1 text-xs font-bold text-sky-800 uppercase tracking-wide">
                        📍 {{ __('Interactive Grid Map') }}
                    </span>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">
                        {{ __('Cameroon Live Outage & Grid Explorer') }}
                    </h2>
                    <p class="mt-2 max-w-2xl text-base text-slate-600">
                        {{ __('Visualise active disruptions, planned works, and grid stability across Cameroon in real time.') }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700">
                        <span class="size-3 rounded-full bg-rose-500"></span> {{ __('Active Outage') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700">
                        <span class="size-3 rounded-full bg-amber-500"></span> {{ __('Scheduled') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700">
                        <span class="size-3 rounded-full bg-emerald-500"></span> {{ __('Grid Normal') }}
                    </span>
                </div>
            </div>

            {{-- City Filter Buttons --}}
            <div class="mt-6 flex flex-wrap gap-2 overflow-x-auto pb-2" id="map-city-tabs">
                <button type="button" class="city-tab-btn active rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-slate-800">
                    🇨🇲 {{ __('All Cameroon') }}
                </button>
                <a href="{{ route('outages.index', ['search' => 'Douala']) }}" class="city-tab-btn rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                    Douala (Littoral)
                </a>
                <a href="{{ route('outages.index', ['search' => 'Yaoundé']) }}" class="city-tab-btn rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                    Yaoundé (Centre)
                </a>
                <a href="{{ route('outages.index', ['search' => 'Bafoussam']) }}" class="city-tab-btn rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                    Bafoussam (Ouest)
                </a>
                <a href="{{ route('outages.index', ['search' => 'Garoua']) }}" class="city-tab-btn rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                    Garoua (Nord)
                </a>
                <a href="{{ route('outages.index', ['search' => 'Bamenda']) }}" class="city-tab-btn rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                    Bamenda (Nord-Ouest)
                </a>
            </div>

            {{-- Map Display Container with Google Maps Telemetry HUD --}}
            <div class="mt-4 overflow-hidden rounded-3xl border border-slate-300 bg-white shadow-xl">
                <div class="relative h-[480px] w-full bg-slate-950" id="delestalert-google-map">
                    {{-- Google Maps Target or Interactive Visual Fallback --}}
                    <div class="absolute inset-0 flex flex-col justify-between p-6 text-white" style="background: radial-gradient(circle at 50% 50%, #0f172a 0%, #020617 100%);">
                        {{-- Top Map HUD --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 backdrop-blur-md">
                            <div class="flex items-center gap-3">
                                <span class="size-3 rounded-full bg-emerald-400 animate-ping"></span>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-200">{{ __('Google Maps Telemetry Online') }}</span>
                            </div>
                            <span class="text-xs text-sky-300 font-mono">GPS: 3.8480° N, 11.5021° E · Cameroon Grid</span>
                        </div>

                        {{-- Interactive Map Pin Overlay for Cameroon --}}
                        <div class="relative my-auto grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 max-w-5xl mx-auto w-full">
                            {{-- Douala Pin Card --}}
                            <div class="rounded-2xl border border-rose-500/40 bg-slate-900/90 p-4 shadow-lg backdrop-blur-md">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="size-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                                        <h4 class="font-bold text-white text-sm">Douala (Littoral)</h4>
                                    </div>
                                    <span class="rounded bg-rose-500/20 px-2 py-0.5 text-[10px] font-bold text-rose-300">{{ __('Outage Active') }}</span>
                                </div>
                                <p class="mt-2 text-xs text-slate-300 font-medium">Bonamoussadi · Makepe · Logpom</p>
                                <p class="mt-1 text-[11px] text-slate-400">Emergency transformer repair in progress.</p>
                                <a href="{{ route('outages.index', ['search' => 'Douala']) }}" class="mt-3 inline-flex text-xs font-semibold text-sky-400 hover:text-sky-300">
                                    {{ __('View Douala Outages') }} →
                                </a>
                            </div>

                            {{-- Yaoundé Pin Card --}}
                            <div class="rounded-2xl border border-amber-500/40 bg-slate-900/90 p-4 shadow-lg backdrop-blur-md">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="size-2.5 rounded-full bg-amber-500"></span>
                                        <h4 class="font-bold text-white text-sm">Yaoundé (Centre)</h4>
                                    </div>
                                    <span class="rounded bg-amber-500/20 px-2 py-0.5 text-[10px] font-bold text-amber-300">{{ __('Scheduled') }}</span>
                                </div>
                                <p class="mt-2 text-xs text-slate-300 font-medium">Bastos · Golf · Omnisports</p>
                                <p class="mt-1 text-[11px] text-slate-400">{{ __('Planned line maintenance tomorrow 08:00.') }}</p>
                                <a href="{{ route('outages.index', ['search' => 'Yaoundé']) }}" class="mt-3 inline-flex text-xs font-semibold text-sky-400 hover:text-sky-300">
                                    {{ __('View Yaoundé Outages') }} →
                                </a>
                            </div>

                            {{-- Bafoussam & West Pin Card --}}
                            <div class="rounded-2xl border border-emerald-500/40 bg-slate-900/90 p-4 shadow-lg backdrop-blur-md">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="size-2.5 rounded-full bg-emerald-500"></span>
                                        <h4 class="font-bold text-white text-sm">Bafoussam (Ouest)</h4>
                                    </div>
                                    <span class="rounded bg-emerald-500/20 px-2 py-0.5 text-[10px] font-bold text-emerald-300">{{ __('Grid Stable') }}</span>
                                </div>
                                <p class="mt-2 text-xs text-slate-300 font-medium">Djeleng · Tougang · Famla</p>
                                <p class="mt-1 text-[11px] text-slate-400">{{ __('No active disruptions reported in last 24h.') }}</p>
                                <a href="{{ route('outages.index', ['search' => 'Bafoussam']) }}" class="mt-3 inline-flex text-xs font-semibold text-sky-400 hover:text-sky-300">
                                    {{ __('View West Region') }} →
                                </a>
                            </div>
                        </div>

                        {{-- Bottom Map Controls --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-400 border-t border-white/10 pt-3">
                            <span>Google Maps Engine · Integrated for DelestAlert</span>
                            <a href="{{ route('reports.create') }}" class="rounded-lg bg-white/10 px-3 py-1.5 font-semibold text-white hover:bg-white/20">
                                📢 {{ __('Report an outage in your area') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- TWITTER / X STYLE COMMUNITY WIRE WITH CERTIFIED ENEO BADGES --}}
    <section id="community-wire" class="scroll-mt-20 border-b border-slate-200 bg-white py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700 border border-sky-200 uppercase tracking-wide">
                        <svg class="size-3.5 fill-sky-600" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        {{ __('Live Community Wire') }}
                    </span>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">
                        {{ __('Real-Time Tweets & Official Eneo Broadcasts') }}
                    </h2>
                    <p class="mt-2 max-w-2xl text-base text-slate-600">
                        {{ __('Direct announcements from certified utility accounts and crowdsourced updates from neighbors on the ground.') }}
                    </p>
                </div>
                <a href="{{ route('community.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800 transition shadow-sm">
                    <span>{{ __('Open Community Hub') }}</span>
                    <span>→</span>
                </a>
            </div>

            {{-- Twitter / X Feed Container --}}
            <div class="mt-10 grid gap-6 lg:grid-cols-12">
                {{-- Main Tweet Feed --}}
                <div class="lg:col-span-8 space-y-4">
                    {{-- Certified Eneo Official Tweet --}}
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs transition hover:border-slate-300 hover:shadow-md sm:p-6">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                {{-- Avatar with Eneo Badge --}}
                                <div class="relative size-12 shrink-0 rounded-full bg-sky-700 flex items-center justify-center font-bold text-white shadow-xs">
                                    <span>EN</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <h3 class="font-bold text-slate-950 text-base">ENEO Operations</h3>
                                        {{-- Certified Blue Badge --}}
                                        <span class="inline-flex items-center text-sky-500" title="{{ __('Certified Official Utility Provider') }}">
                                            <svg class="size-5 fill-sky-500" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/></svg>
                                        </span>
                                        <span class="rounded bg-sky-100 px-1.5 py-0.5 text-[10px] font-bold text-sky-800 uppercase tracking-wider">{{ __('Certified Provider') }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500">@eneocameroon · 24m · 🇨🇲 Douala</p>
                                </div>
                            </div>
                            <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 border border-rose-200">
                                #AlerteCoupure
                            </span>
                        </div>

                        <div class="mt-4 text-slate-800 text-sm sm:text-base leading-relaxed">
                            <p>⚠️ <strong>#Douala | Incident Réseau Détecté :</strong> Nos équipes techniques d'intervention rapide sont actuellement déployées sur le départ 15kV alimentant <strong>#Bonamoussadi, #Makepe et #Logpom</strong> suite à une surchauffe de poste source.</p>
                            <p class="mt-2 font-medium text-slate-900">⏳ Heure estimée de réalimentation progressive : <strong>20h30</strong>. Suivez l'évolution en direct sur DelestAlert. Merci pour votre compréhension.</p>
                        </div>

                        {{-- Tweet Attached Location Pill --}}
                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                📍 Bonamoussadi (Douala V)
                            </span>
                            <span class="inline-flex items-center gap-1 rounded-lg bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700">
                                #EneoDirect
                            </span>
                            <span class="inline-flex items-center gap-1 rounded-lg bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700">
                                #DelestageDouala
                            </span>
                        </div>

                        {{-- Twitter Style Interaction Bar --}}
                        <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4 text-xs font-semibold text-slate-500 sm:text-sm">
                            <div class="flex items-center gap-6 sm:gap-8">
                                <button type="button" class="group flex items-center gap-2 hover:text-sky-600 transition">
                                    <svg class="size-4.5 text-slate-400 group-hover:text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    <span>38</span>
                                </button>
                                <button type="button" class="group flex items-center gap-2 hover:text-emerald-600 transition">
                                    <svg class="size-4.5 text-slate-400 group-hover:text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    <span>112</span>
                                </button>
                                <button type="button" class="group flex items-center gap-2 hover:text-rose-600 transition">
                                    <svg class="size-4.5 text-slate-400 group-hover:text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    <span>284</span>
                                </button>
                            </div>
                            <span class="inline-flex items-center gap-1 font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200 text-xs">
                                ✓ 94 {{ __('Neighbors confirmed') }}
                            </span>
                        </div>
                    </article>

                    {{-- Verified Citizen Tweet 2: Bastos Restoration --}}
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs transition hover:border-slate-300 hover:shadow-md sm:p-6">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="size-11 shrink-0 rounded-full bg-emerald-600 flex items-center justify-center font-bold text-white">
                                    JM
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <h3 class="font-bold text-slate-950 text-base">Jean Mbarga</h3>
                                        <span class="text-xs text-slate-400">@jean_mbarga</span>
                                    </div>
                                    <p class="text-xs text-slate-500">1h · 🇨🇲 Bastos, Yaoundé</p>
                                </div>
                            </div>
                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                                #CourantDeRetour
                            </span>
                        </div>

                        <p class="mt-4 text-slate-800 text-sm sm:text-base leading-relaxed">
                            💡 La lumière vient de revenir à Bastos vers l'ambassade ! Tension stable à 228V. Vous confirmez dans le quartier voisin vers le Golf ? #YaoundePower
                        </p>

                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                📍 Bastos, Yaoundé I
                            </span>
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 text-xs font-semibold text-slate-500 sm:text-sm">
                            <div class="flex items-center gap-6">
                                <span class="flex items-center gap-1.5 hover:text-sky-600"><svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg> 14</span>
                                <span class="flex items-center gap-1.5 hover:text-rose-600"><svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg> 47</span>
                            </div>
                            <span class="text-xs text-emerald-600 font-semibold">✓ 19 voisins ont confirmé</span>
                        </div>
                    </article>
                </div>

                {{-- Sidebar: Trending Tags & Quick Composer --}}
                <div class="lg:col-span-4 space-y-6">
                    {{-- Trending Hashtags in Cameroon --}}
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 shadow-xs">
                        <h3 class="font-bold text-slate-950 text-base flex items-center gap-2">
                            <span>🔥</span> {{ __('Trending in Cameroon') }}
                        </h3>
                        <div class="mt-4 divide-y divide-slate-200/70 text-xs">
                            <div class="py-2.5">
                                <p class="text-slate-400">1 · {{ __('Electricity · Trending') }}</p>
                                <p class="font-bold text-slate-900 text-sm">#DelestageDouala</p>
                                <p class="text-slate-500">1,420 {{ __('reports today') }}</p>
                            </div>
                            <div class="py-2.5">
                                <p class="text-slate-400">2 · {{ __('Official Dispatch') }}</p>
                                <p class="font-bold text-slate-900 text-sm">#EneoDirect</p>
                                <p class="text-slate-500">582 {{ __('verified posts') }}</p>
                            </div>
                            <div class="py-2.5">
                                <p class="text-slate-400">3 · {{ __('Yaoundé Network') }}</p>
                                <p class="font-bold text-slate-900 text-sm">#BastosYaounde</p>
                                <p class="text-slate-500">210 {{ __('citizen updates') }}</p>
                            </div>
                            <div class="py-2.5">
                                <p class="text-slate-400">4 · {{ __('Solar & Energy') }}</p>
                                <p class="font-bold text-slate-900 text-sm">#GroupeElectrogene</p>
                                <p class="text-slate-500">184 {{ __('discussions') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Join Community CTA --}}
                    <div class="rounded-2xl bg-gradient-to-br from-slate-950 to-sky-950 p-5 text-white shadow-lg">
                        <span class="inline-flex items-center text-xs font-bold uppercase tracking-wider text-sky-300">⚡ {{ __('Verified Network') }}</span>
                        <h4 class="mt-2 text-lg font-bold">{{ __('Have news in your street?') }}</h4>
                        <p class="mt-1.5 text-xs text-slate-300 leading-relaxed">
                            {{ __('Join thousands of residents sharing instant updates and helping local businesses stay prepared.') }}
                        </p>
                        <a href="{{ route('register') }}" class="mt-4 inline-flex w-full justify-center rounded-xl bg-sky-600 py-2.5 text-xs font-bold text-white hover:bg-sky-500 transition shadow-sm">
                            {{ __('Join the Community Wire') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS SECTION --}}
    <section id="how-it-works" class="scroll-mt-20 border-b border-slate-200 bg-slate-50 py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <span class="text-xs font-bold uppercase tracking-widest text-sky-700">{{ __('Simple 3-Step Process') }}</span>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                    {{ __('How DelestAlert keeps you one step ahead.') }}
                </h2>
                <p class="mt-3 text-base text-slate-600">
                    {{ __('Built specifically for Cameroonians facing load shedding, voltage drops, and scheduled maintenance.') }}
                </p>
            </div>

            <div class="mt-14 grid gap-8 md:grid-cols-3">
                {{-- Step 1 --}}
                <div class="relative rounded-3xl border border-slate-200 bg-white p-8 shadow-sm transition hover:shadow-md">
                    <div class="flex size-14 items-center justify-center rounded-2xl bg-sky-50 text-xl font-bold text-sky-700">
                        01
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-slate-950">{{ __('Pin your locations') }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        {{ __('Add your home, office, shop, or cold storage facility across any city in Cameroon (Douala, Yaoundé, Bafoussam, etc.).') }}
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="relative rounded-3xl border border-slate-200 bg-white p-8 shadow-sm transition hover:shadow-md">
                    <div class="flex size-14 items-center justify-center rounded-2xl bg-amber-50 text-xl font-bold text-amber-700">
                        02
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-slate-950">{{ __('Get predictive & live alerts') }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        {{ __('Receive alerts 2 to 4 hours in advance via Web, WhatsApp, and SMS before scheduled cuts or high-risk peak hours.') }}
                    </p>
                </div>

                {{-- Step 3 --}}
                <div class="relative rounded-3xl border border-slate-200 bg-white p-8 shadow-sm transition hover:shadow-md">
                    <div class="flex size-14 items-center justify-center rounded-2xl bg-emerald-50 text-xl font-bold text-emerald-700">
                        03
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-slate-950">{{ __('1-Click crowd verification') }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        {{ __('When power cuts or returns, confirm with 1 tap. Help your neighbors and certified utility crews know the exact status.') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- AI PREDICTION ENGINE --}}
    <section id="predictions" class="scroll-mt-20 border-b border-slate-200 bg-slate-950 text-white py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid items-center gap-12 lg:grid-cols-12">
                <div class="lg:col-span-6">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-400/20 px-3 py-1 text-xs font-bold text-amber-300 border border-amber-400/30 uppercase tracking-wide">
                        🤖 {{ __('Intelligent Forecast Engine') }}
                    </span>
                    <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                        {{ __('Predicting load shedding before it happens.') }}
                    </h2>
                    <p class="mt-4 text-base text-slate-300 leading-relaxed">
                        {{ __('DelestAlert analyses historical outage trends, weather events, transformer load curves, and scheduled utility notices to estimate outage probability for your district.') }}
                    </p>

                    <div class="mt-8 space-y-4">
                        <div class="flex gap-4 rounded-2xl bg-white/5 p-4 border border-white/10">
                            <div class="grid size-10 shrink-0 place-items-center rounded-xl bg-sky-500/20 text-sky-400 font-bold">
                                📈
                            </div>
                            <div>
                                <h4 class="font-bold text-white text-sm">{{ __('Historical Feeder Patterns') }}</h4>
                                <p class="mt-1 text-xs text-slate-300">{{ __('Tracks recurring weekly load-shedding cycles in key commercial and residential hubs.') }}</p>
                            </div>
                        </div>

                        <div class="flex gap-4 rounded-2xl bg-white/5 p-4 border border-white/10">
                            <div class="grid size-10 shrink-0 place-items-center rounded-xl bg-amber-500/20 text-amber-400 font-bold">
                                ⛈️
                            </div>
                            <div>
                                <h4 class="font-bold text-white text-sm">{{ __('Storm & Rainy Season Correlation') }}</h4>
                                <p class="mt-1 text-xs text-slate-300">{{ __('Detects high-risk weather that triggers line trips across coastal and western regions.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Prediction Widget Mockup --}}
                <div class="lg:col-span-6">
                    <div class="rounded-3xl border border-white/15 bg-gradient-to-br from-slate-900 to-slate-950 p-6 sm:p-8 shadow-2xl">
                        <div class="flex items-center justify-between border-b border-white/10 pb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-300">{{ __('Risk Estimation Model v2.4') }}</span>
                            <span class="rounded-full bg-emerald-500/20 px-2.5 py-0.5 text-xs font-semibold text-emerald-300">98.4% {{ __('Accuracy') }}</span>
                        </div>

                        <div class="mt-6 space-y-4">
                            <div>
                                <div class="flex justify-between text-sm font-semibold mb-1">
                                    <span>Bonamoussadi (Douala)</span>
                                    <span class="text-rose-400">78% {{ __('Outage Probability') }}</span>
                                </div>
                                <div class="h-2.5 w-full rounded-full bg-white/10 overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-amber-500 to-rose-500 rounded-full" style="width: 78%"></div>
                                </div>
                                <p class="mt-1 text-[11px] text-slate-400">{{ __('High load detected on primary substation · Peak evening hours') }}</p>
                            </div>

                            <div class="pt-2 border-t border-white/10">
                                <div class="flex justify-between text-sm font-semibold mb-1">
                                    <span>Bastos (Yaoundé)</span>
                                    <span class="text-amber-400">45% {{ __('Outage Probability') }}</span>
                                </div>
                                <div class="h-2.5 w-full rounded-full bg-white/10 overflow-hidden">
                                    <div class="h-full bg-amber-500 rounded-full" style="width: 45%"></div>
                                </div>
                                <p class="mt-1 text-[11px] text-slate-400">{{ __('Moderate risk during afternoon commercial peaks') }}</p>
                            </div>

                            <div class="pt-2 border-t border-white/10">
                                <div class="flex justify-between text-sm font-semibold mb-1">
                                    <span>Famla (Bafoussam)</span>
                                    <span class="text-emerald-400">12% {{ __('Outage Probability') }}</span>
                                </div>
                                <div class="h-2.5 w-full rounded-full bg-white/10 overflow-hidden">
                                    <div class="h-full bg-emerald-500 rounded-full" style="width: 12%"></div>
                                </div>
                                <p class="mt-1 text-[11px] text-slate-400">{{ __('Stable distribution feed · No active anomalies') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ABOUT US SECTION --}}
    <section id="about-us" class="scroll-mt-20 border-b border-slate-200 bg-white py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid items-center gap-12 lg:grid-cols-12">
                <div class="lg:col-span-6">
                    <span class="text-xs font-bold uppercase tracking-widest text-sky-700">{{ __('Our Story & Purpose') }}</span>
                    <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                        {{ __('Built for Cameroonians who refuse to be kept in the dark.') }}
                    </h2>
                    <p class="mt-4 text-base text-slate-600 leading-relaxed">
                        {{ __('Electricity outages impact every aspect of daily life in Cameroon — from preserving cold food and running dialysis machines to powering tech startups and operating small workshops.') }}
                    </p>
                    <p class="mt-3 text-base text-slate-600 leading-relaxed">
                        {{ __('DelestAlert was conceived by Cameroonian engineers and civic technologists to bring transparent, crowdsourced, and provider-verified clarity to our energy ecosystem. We believe that with predictable data, businesses can protect their revenue, families can plan their days, and power operators can resolve faults faster.') }}
                    </p>

                    <div class="mt-8 grid grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <h4 class="font-bold text-slate-950">{{ __('100% Civic & Independent') }}</h4>
                            <p class="mt-1 text-xs text-slate-600">{{ __('Unbiased data dedicated to community resilience.') }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <h4 class="font-bold text-slate-950">{{ __('Real-Time Verification') }}</h4>
                            <p class="mt-1 text-xs text-slate-600">{{ __('Every report is backed by multiple neighbor signals.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-6 space-y-4">
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 sm:p-8">
                        <h3 class="text-lg font-bold text-slate-950">🎯 {{ __('Our Mission') }}</h3>
                        <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                            {{ __('To eliminate the unpredictability of electricity cuts across Central Africa through collaborative data, AI forecasting, and instant multi-channel alerting.') }}
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-sky-50 p-6 sm:p-8">
                        <h3 class="text-lg font-bold text-sky-950">🤝 {{ __('Community-First Governance') }}</h3>
                        <p class="mt-2 text-sm text-sky-800 leading-relaxed">
                            {{ __('We work constructively with utility operators (ENEO, SONATREL, ARSEL) while maintaining strict editorial independence and citizen moderation.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- PARTNERS SECTION --}}
    <section id="partners" class="scroll-mt-20 border-b border-slate-200 bg-slate-50 py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto">
                <span class="text-xs font-bold uppercase tracking-widest text-sky-700">{{ __('Ecosystem & Integration') }}</span>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                    {{ __('Partnering across the energy and telecom landscape.') }}
                </h2>
                <p class="mt-3 text-base text-slate-600">
                    {{ __('DelestAlert interfaces with utility feeds, mobile telecom networks, solar energy providers, and tech hubs.') }}
                </p>
            </div>

            <div class="mt-12 grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-4">
                {{-- Partner 1: Utility Dispatch --}}
                <div class="flex flex-col items-center justify-center rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-2xs">
                    <span class="text-2xl font-black text-sky-800">ENEO</span>
                    <span class="mt-2 text-xs font-semibold text-slate-500">{{ __('Power Distribution Feed') }}</span>
                </div>
                {{-- Partner 2: Transmission --}}
                <div class="flex flex-col items-center justify-center rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-2xs">
                    <span class="text-2xl font-black text-slate-800">SONATREL</span>
                    <span class="mt-2 text-xs font-semibold text-slate-500">{{ __('Grid Transmission Grid') }}</span>
                </div>
                {{-- Partner 3: Telecom SMS Gateway --}}
                <div class="flex flex-col items-center justify-center rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-2xs">
                    <span class="text-2xl font-black text-amber-500">MTN / Orange</span>
                    <span class="mt-2 text-xs font-semibold text-slate-500">{{ __('SMS Alert Gateways') }}</span>
                </div>
                {{-- Partner 4: Solar & Energy Backup --}}
                <div class="flex flex-col items-center justify-center rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-2xs">
                    <span class="text-2xl font-black text-emerald-600">EcoSolar CM</span>
                    <span class="mt-2 text-xs font-semibold text-slate-500">{{ __('Backup & Inverter Integration') }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQS SECTION (ACCORDION) --}}
    <section id="faqs" class="scroll-mt-20 border-b border-slate-200 bg-white py-16 sm:py-24">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <span class="text-xs font-bold uppercase tracking-widest text-sky-700">{{ __('Frequently Asked Questions') }}</span>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                    {{ __('Everything you need to know about DelestAlert.') }}
                </h2>
                <p class="mt-3 text-base text-slate-600">
                    {{ __('Clear answers to common questions about alerts, data sources, and account features.') }}
                </p>
            </div>

            <div class="mt-12 space-y-4" id="faq-accordion">
                {{-- FAQ 1 --}}
                <details class="group rounded-2xl border border-slate-200 bg-slate-50 p-6 [&_summary::-webkit-details-marker]:hidden" open>
                    <summary class="flex cursor-pointer items-center justify-between font-bold text-slate-950 text-base sm:text-lg">
                        <span>{{ __('Is DelestAlert an official service of Eneo or SONATREL?') }}</span>
                        <span class="ml-4 shrink-0 text-slate-400 group-open:rotate-180 transition">▾</span>
                    </summary>
                    <p class="mt-4 text-sm leading-relaxed text-slate-600">
                        {{ __('DelestAlert is an independent civic platform. We integrate official public notices from Eneo and SONATREL alongside crowdsourced citizen reports, clearly distinguishing between certified provider statements and verified community updates.') }}
                    </p>
                </details>

                {{-- FAQ 2 --}}
                <details class="group rounded-2xl border border-slate-200 bg-slate-50 p-6 [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex cursor-pointer items-center justify-between font-bold text-slate-950 text-base sm:text-lg">
                        <span>{{ __('How do outage predictions work?') }}</span>
                        <span class="ml-4 shrink-0 text-slate-400 group-open:rotate-180 transition">▾</span>
                    </summary>
                    <p class="mt-4 text-sm leading-relaxed text-slate-600">
                        {{ __('Our prediction engine analyses recurring seasonal load patterns, historic feeder trip frequencies, and weather forecasts (e.g. severe thunderstorms) to estimate the probability of disruptions 2 to 4 hours ahead of time.') }}
                    </p>
                </details>

                {{-- FAQ 3 --}}
                <details class="group rounded-2xl border border-slate-200 bg-slate-50 p-6 [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex cursor-pointer items-center justify-between font-bold text-slate-950 text-base sm:text-lg">
                        <span>{{ __('Can I receive alerts via WhatsApp or SMS if I have no mobile data?') }}</span>
                        <span class="ml-4 shrink-0 text-slate-400 group-open:rotate-180 transition">▾</span>
                    </summary>
                    <p class="mt-4 text-sm leading-relaxed text-slate-600">
                        {{ __('Yes! In your account notification preferences, you can configure SMS alerts to receive instant notifications on your phone even when internet connectivity or Wi-Fi is down during an outage.') }}
                    </p>
                </details>

                {{-- FAQ 4 --}}
                <details class="group rounded-2xl border border-slate-200 bg-slate-50 p-6 [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex cursor-pointer items-center justify-between font-bold text-slate-950 text-base sm:text-lg">
                        <span>{{ __('How do you prevent false or spam outage reports?') }}</span>
                        <span class="ml-4 shrink-0 text-slate-400 group-open:rotate-180 transition">▾</span>
                    </summary>
                    <p class="mt-4 text-sm leading-relaxed text-slate-600">
                        {{ __('Reports require registered accounts and are cross-referenced with geographic clustering. An alert is only broadcast once multiple neighbors in the same district confirm the outage or when verified by a certified provider account.') }}
                    </p>
                </details>

                {{-- FAQ 5 --}}
                <details class="group rounded-2xl border border-slate-200 bg-slate-50 p-6 [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex cursor-pointer items-center justify-between font-bold text-slate-950 text-base sm:text-lg">
                        <span>{{ __('Is DelestAlert free for households and small businesses?') }}</span>
                        <span class="ml-4 shrink-0 text-slate-400 group-open:rotate-180 transition">▾</span>
                    </summary>
                    <p class="mt-4 text-sm leading-relaxed text-slate-600">
                        {{ __('Yes, DelestAlert is 100% free for citizens to browse outages, view the live map, submit reports, and receive neighborhood notifications.') }}
                    </p>
                </details>
            </div>
        </div>
    </section>

    {{-- FINAL CALL TO ACTION --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-sky-950 to-slate-900 py-16 text-white sm:py-20">
        <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl lg:text-5xl">
                {{ __('Take control of your energy schedule today.') }}
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-base text-slate-300 sm:text-lg">
                {{ __('Join over 15,000 Cameroonians who stay informed, protect their equipment, and never get caught unprepared.') }}
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('register') }}" class="rounded-xl bg-sky-500 px-7 py-3.5 text-base font-bold text-white shadow-lg shadow-sky-500/30 transition hover:bg-sky-400">
                    {{ __('Create account') }}
                </a>
                <a href="{{ route('outages.index') }}" class="rounded-xl border border-white/20 bg-white/10 px-7 py-3.5 text-base font-bold text-white backdrop-blur-sm transition hover:bg-white/20">
                    {{ __('View current outages') }}
                </a>
            </div>
        </div>
    </section>

    {{-- LUXURY FULL-WIDTH FOOTER --}}
    <footer class="border-t border-slate-800 bg-slate-950 text-slate-400">
        <div class="mx-auto max-w-7xl px-4 pt-16 pb-12 sm:px-6 lg:px-8">
            <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-5">
                {{-- Column 1: Brand & Mission --}}
                <div class="lg:col-span-2">
                    <x-logo class="text-white" />
                    <p class="mt-4 max-w-sm text-sm leading-relaxed text-slate-400">
                        {{ __('Intelligent electricity outage prediction and notification system for Cameroon. Dedicated to reducing power uncertainty for families, health centers, and enterprises.') }}
                    </p>
                    <div class="mt-6 flex items-center gap-3 text-xs text-slate-300">
                        <span class="inline-flex size-2 rounded-full bg-emerald-400"></span>
                        <span>{{ __('Operational across Douala, Yaoundé & all 10 Regions') }} 🇨🇲</span>
                    </div>
                </div>

                {{-- Column 2: Navigation --}}
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-200">{{ __('Platform') }}</h3>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        <li><a href="{{ route('outages.index') }}" class="hover:text-white transition">{{ __('Outage Board') }}</a></li>
                        <li><a href="#live-map" class="hover:text-white transition">{{ __('Interactive Grid Map') }}</a></li>
                        <li><a href="#predictions" class="hover:text-white transition">{{ __('AI Risk Forecast') }}</a></li>
                        <li><a href="{{ route('community.index') }}" class="hover:text-white transition">{{ __('Community Wire') }}</a></li>
                        <li><a href="{{ route('reports.create') }}" class="hover:text-white transition">{{ __('Report an Outage') }}</a></li>
                    </ul>
                </div>

                {{-- Column 3: Monitored Cities --}}
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-200">{{ __('Monitored Hubs') }}</h3>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        <li><a href="{{ route('outages.index', ['search' => 'Douala']) }}" class="hover:text-white transition">Douala (Littoral)</a></li>
                        <li><a href="{{ route('outages.index', ['search' => 'Yaoundé']) }}" class="hover:text-white transition">Yaoundé (Centre)</a></li>
                        <li><a href="{{ route('outages.index', ['search' => 'Bafoussam']) }}" class="hover:text-white transition">Bafoussam (Ouest)</a></li>
                        <li><a href="{{ route('outages.index', ['search' => 'Garoua']) }}" class="hover:text-white transition">Garoua (Nord)</a></li>
                        <li><a href="{{ route('outages.index', ['search' => 'Bamenda']) }}" class="hover:text-white transition">Bamenda (Nord-Ouest)</a></li>
                    </ul>
                </div>

                {{-- Column 4: Emergency Contacts --}}
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-200">{{ __('Emergency & Utility') }}</h3>
                    <ul class="mt-4 space-y-2.5 text-xs">
                        <li><strong class="text-slate-200">ENEO Hotline:</strong> 8010</li>
                        <li><strong class="text-slate-200">Civil Protection:</strong> 118</li>
                        <li><strong class="text-slate-200">DelestAlert Dispatch:</strong> support@delestalert.cm</li>
                        <li class="pt-2">
                            <form method="POST" action="{{ route('locale.update') }}">
                                @csrf
                                <label class="sr-only" for="footer-locale">Language</label>
                                <select id="footer-locale" name="locale" onchange="this.form.submit()" class="rounded-lg border border-slate-700 bg-slate-900 py-1.5 pl-2 pr-7 text-xs text-slate-300">
                                    <option value="en" @selected(app()->getLocale() === 'en')>🇬🇧 English</option>
                                    <option value="fr" @selected(app()->getLocale() === 'fr')>🇨🇲 Français</option>
                                </select>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-slate-800/80 pt-8 text-xs text-slate-500 sm:flex-row">
                <p>© {{ now()->year }} DelestAlert. {{ __('Official notices · Community reports · Clearly labelled forecasts.') }}</p>
                <div class="flex gap-6">
                    <a href="#about-us" class="hover:text-slate-400">{{ __('About Us') }}</a>
                    <a href="#partners" class="hover:text-slate-400">{{ __('Partners') }}</a>
                    <a href="#faqs" class="hover:text-slate-400">{{ __('FAQs') }}</a>
                </div>
            </div>
        </div>
    </footer>
</x-layouts.app>
