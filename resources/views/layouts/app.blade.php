<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0369a1">
    <title>{{ isset($title) ? $title.' · ' : '' }}DelestAlert</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-canvas text-slate-900 antialiased">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-slate-950 focus:px-4 focus:py-2 focus:text-white">{{ __('Skip to content') }}</a>
    @unless($dashboard)
    <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 backdrop-blur-md shadow-xs">
        <div class="flex h-18 w-full items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <x-logo />
            <nav class="hidden items-center gap-1 xl:gap-2 lg:flex" aria-label="Primary navigation">
                <a href="{{ route('outages.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-slate-950">{{ __('Outages') }}</a>
                <a href="{{ route('home') }}#live-map" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-slate-950">{{ __('Live Map') }}</a>
                <a href="{{ route('home') }}#how-it-works" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-slate-950">{{ __('How It Works') }}</a>
                <a href="{{ route('home') }}#predictions" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-slate-950">{{ __('Predictions') }}</a>
                <a href="{{ route('community.index') }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-slate-950">
                    <span>{{ __('Community Wire') }}</span>
                    <span class="inline-flex size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                </a>
                <a href="{{ route('home') }}#about-us" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-slate-950">{{ __('About Us') }}</a>
                <a href="{{ route('home') }}#partners" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-slate-950">{{ __('Partners') }}</a>
                <a href="{{ route('home') }}#faqs" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-slate-950">{{ __('FAQs') }}</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-lg bg-sky-50 px-3 py-2 text-sm font-semibold text-sky-800 transition hover:bg-sky-100">{{ __('Dashboard') }}</a>
                @endauth
            </nav>
            <div class="hidden items-center gap-3 sm:flex">
                <form method="POST" action="{{ route('locale.update') }}">
                    <label class="sr-only" for="locale">Language</label>
                    @csrf
                    <select id="locale" name="locale" onchange="this.form.submit()" class="rounded-lg border border-slate-200 bg-white py-1.5 pl-2.5 pr-8 text-xs font-semibold text-slate-700 shadow-2xs outline-none hover:border-slate-300 focus:ring-2 focus:ring-sky-600">
                        <option value="en" @selected(app()->getLocale() === 'en')>🇬🇧 EN</option>
                        <option value="fr" @selected(app()->getLocale() === 'fr')>🇨🇲 FR</option>
                    </select>
                </form>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm font-medium text-slate-700 hover:text-sky-700">{{ auth()->user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">{{ __('Sign out') }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">{{ __('Sign in') }}</a>
                    <a href="{{ route('register') }}" class="rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-700">{{ __('Create account') }}</a>
                @endauth
            </div>
            <button type="button" data-menu-toggle aria-expanded="false" aria-controls="mobile-menu" class="grid size-11 place-items-center rounded-lg text-slate-700 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-700 lg:hidden">
                <span class="sr-only">Toggle navigation</span>
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
            </button>
        </div>
        <div id="mobile-menu" class="hidden border-t border-slate-200 bg-white px-4 py-4 shadow-lg lg:hidden">
            <div class="flex w-full flex-col gap-1.5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 px-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ __('Navigation') }}</span>
                    <form method="POST" action="{{ route('locale.update') }}">
                        @csrf
                        <select id="mobile-locale" name="locale" onchange="this.form.submit()" class="rounded-md border border-slate-200 bg-slate-50 py-1 pl-2 pr-6 text-xs font-semibold text-slate-700">
                            <option value="en" @selected(app()->getLocale() === 'en')>🇬🇧 English</option>
                            <option value="fr" @selected(app()->getLocale() === 'fr')>🇨🇲 Français</option>
                        </select>
                    </form>
                </div>
                <a href="{{ route('outages.index') }}" class="rounded-lg px-3 py-2 font-medium text-slate-700 hover:bg-slate-100">{{ __('Outages') }}</a>
                <a href="{{ route('home') }}#live-map" class="rounded-lg px-3 py-2 font-medium text-slate-700 hover:bg-slate-100">{{ __('Live Map') }}</a>
                <a href="{{ route('home') }}#how-it-works" class="rounded-lg px-3 py-2 font-medium text-slate-700 hover:bg-slate-100">{{ __('How It Works') }}</a>
                <a href="{{ route('home') }}#predictions" class="rounded-lg px-3 py-2 font-medium text-slate-700 hover:bg-slate-100">{{ __('Predictions') }}</a>
                <a href="{{ route('community.index') }}" class="rounded-lg px-3 py-2 font-medium text-slate-700 hover:bg-slate-100">{{ __('Community Wire') }}</a>
                <a href="{{ route('home') }}#about-us" class="rounded-lg px-3 py-2 font-medium text-slate-700 hover:bg-slate-100">{{ __('About Us') }}</a>
                <a href="{{ route('home') }}#partners" class="rounded-lg px-3 py-2 font-medium text-slate-700 hover:bg-slate-100">{{ __('Partners') }}</a>
                <a href="{{ route('home') }}#faqs" class="rounded-lg px-3 py-2 font-medium text-slate-700 hover:bg-slate-100">{{ __('FAQs') }}</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="mt-2 rounded-xl bg-sky-50 px-3 py-2.5 font-semibold text-sky-800">{{ __('Dashboard') }}</a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button class="w-full rounded-lg px-3 py-2 text-left font-medium text-slate-700 hover:bg-slate-100">{{ __('Sign out') }}</button>
                    </form>
                @else
                    <div class="mt-3 flex flex-col gap-2 pt-2 border-t border-slate-100">
                        <a href="{{ route('login') }}" class="rounded-xl border border-slate-200 px-3 py-2.5 text-center font-semibold text-slate-800 hover:bg-slate-50">{{ __('Sign in') }}</a>
                        <a href="{{ route('register') }}" class="rounded-xl bg-sky-700 px-3 py-2.5 text-center font-semibold text-white shadow-sm hover:bg-sky-800">{{ __('Create account') }}</a>
                    </div>
                @endauth
            </div>
        </div>
    </header>
    @endunless
    @if (session('success'))
        <div class="mt-5 w-full px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900" role="status">
                <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif
    <main @if(! $dashboard) id="main-content" @endif>{{ $slot }}</main>
    @if(! $dashboard && ! request()->routeIs('home'))
        <footer class="mt-16 border-t border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl flex-col justify-between gap-3 px-4 py-8 text-sm text-slate-500 sm:flex-row sm:px-6 lg:px-8">
                <span>© {{ now()->year }} DelestAlert · {{ __('Electricity information for Cameroon') }}</span>
                <span>{{ __('Official notices · Community reports · Clearly labelled forecasts') }}</span>
            </div>
        </footer>
    @endif
</body>
</html>
