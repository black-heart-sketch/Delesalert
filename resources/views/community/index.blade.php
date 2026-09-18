<x-layouts.dashboard :title="__('Community')">
    <div class="flex min-h-[calc(100dvh-8rem)] w-full flex-col gap-6">
        <section class="rounded-3xl bg-slate-950 p-5 text-white shadow-xl sm:p-8 lg:min-h-64 lg:p-10">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-sky-300">{{ __('Local power network') }}</p>
            <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">{{ __('Community updates') }}</h1>
                    <p class="mt-2 max-w-3xl text-slate-300 sm:text-lg">{{ __('Share verified local context, ask neighbours questions, and help your area stay informed during outages.') }}</p>
                </div>
                <span class="rounded-full bg-white/10 px-4 py-2 text-sm font-medium text-sky-100">{{ __('Respectful, helpful, local') }}</span>
            </div>
        </section>

        <div class="grid flex-1 gap-6 xl:grid-cols-[minmax(0,1fr)_26rem]">
            <div class="min-w-0 space-y-5">
                @forelse($posts as $post)
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs transition hover:border-slate-300 hover:shadow-sm sm:p-6 lg:p-7">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <div @class([
                                    'grid size-11 shrink-0 place-items-center rounded-full font-bold shadow-xs text-sm',
                                    'bg-sky-700 text-white' => $post->user->role === 'PROVIDER',
                                    'bg-slate-900 text-white' => $post->user->role === 'ADMIN',
                                    'bg-sky-100 text-sky-800' => $post->user->role === 'CLIENT'
                                ])>
                                    {{ mb_strtoupper(mb_substr($post->user->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <p class="truncate font-bold text-slate-950">{{ $post->user->name }}</p>
                                        @if($post->user->isCertified())
                                            <span class="inline-flex items-center text-sky-500" title="{{ __('Certified Official Account') }}">
                                                <svg class="size-4.5 fill-sky-500" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/></svg>
                                            </span>
                                            <span class="rounded bg-sky-100 px-1.5 py-0.2 text-[10px] font-bold text-sky-800 uppercase tracking-wider">
                                                {{ $post->user->role === 'PROVIDER' ? __('Official Provider') : __('Verified') }}
                                            </span>
                                        @endif
                                        <span class="text-xs text-slate-400 font-mono">{{ $post->user->handle }}</span>
                                    </div>
                                    <p class="text-xs leading-5 text-slate-500">
                                        📍 {{ $post->zone?->name ? $post->zone->name.', '.$post->zone->city : __('General Cameroon Network') }} · {{ $post->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <span @class(['rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide', 'bg-amber-100 text-amber-800 border border-amber-200' => $post->category === 'QUESTION', 'bg-emerald-100 text-emerald-800 border border-emerald-200' => $post->category === 'TIP', 'bg-sky-100 text-sky-800 border border-sky-200' => $post->category === 'UPDATE'])>{{ __($post->category) }}</span>
                        </div>

                        <h2 class="mt-4 text-base font-bold text-slate-950 sm:text-lg">{{ $post->title }}</h2>
                        <p class="mt-2 whitespace-pre-line text-sm sm:text-base leading-relaxed text-slate-700">{{ $post->body }}</p>

                        <div class="mt-5 flex flex-wrap items-center justify-between gap-4 border-t border-slate-100 pt-4 text-xs sm:text-sm">
                            <div class="flex items-center gap-4">
                                <form method="POST" action="{{ route('community.reactions.store', $post) }}">
                                    @csrf
                                    <button class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 font-semibold text-slate-600 transition hover:bg-rose-50 hover:text-rose-600">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                        <span>{{ __('Support') }} · {{ $post->reactions_count }}</span>
                                    </button>
                                </form>
                                <span class="text-slate-500 font-medium">💬 {{ trans_choice('{0} No comments|{1} :count comment|[2,*] :count comments', $post->comments_count, ['count' => $post->comments_count]) }}</span>
                            </div>
                            @if(auth()->user()->hasRole('ADMIN'))
                                <form method="POST" action="{{ route('community.hide', $post) }}">@csrf @method('PATCH') <button class="font-semibold text-rose-600 hover:text-rose-700">{{ __('Hide post') }}</button></form>
                            @endif
                        </div>

                        @if($post->comments->isNotEmpty())
                            <div class="mt-4 space-y-3 border-t border-slate-100 pt-4">
                                @foreach($post->comments->take(3) as $comment)
                                    <div class="rounded-xl bg-slate-50 px-4 py-3">
                                        <p class="text-sm text-slate-700"><span class="font-semibold text-slate-950">{{ $comment->user->name }}</span> · {{ $comment->created_at->diffForHumans() }}</p>
                                        <p class="mt-1 text-sm leading-6 text-slate-600">{{ $comment->body }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('community.comments.store', $post) }}" class="mt-4 flex flex-col gap-2 sm:flex-row">@csrf
                            <label class="sr-only" for="comment-{{ $post->id }}">{{ __('Add a comment') }}</label>
                            <input id="comment-{{ $post->id }}" name="body" maxlength="1000" required placeholder="{{ __('Add a helpful comment…') }}" class="min-w-0 flex-1 rounded-xl border-slate-200 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500">
                            <button class="min-h-11 rounded-xl bg-slate-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">{{ __('Reply') }}</button>
                        </form>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center"><p class="font-semibold text-slate-950">{{ __('No community updates yet.') }}</p><p class="mt-2 text-sm text-slate-500">{{ __('Start the conversation with a helpful local update.') }}</p></div>
                @endforelse
                {{ $posts->links() }}
            </div>

            <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 xl:sticky xl:top-24 xl:p-7">
                <h2 class="text-lg font-bold text-slate-950">{{ __('Share an update') }}</h2>
                <p class="mt-1 text-sm leading-6 text-slate-500">{{ __('Do not share personal account or payment information.') }}</p>
                <form method="POST" action="{{ route('community.store') }}" class="mt-5 space-y-4">@csrf
                    <div><label for="category" class="text-sm font-semibold text-slate-700">{{ __('Type') }}</label><select id="category" name="category" class="mt-1 block w-full rounded-xl border-slate-200 text-sm focus:border-sky-500 focus:ring-sky-500"><option value="UPDATE">{{ __('Local update') }}</option><option value="QUESTION">{{ __('Question') }}</option><option value="TIP">{{ __('Tip') }}</option></select></div>
                    <div><label for="zone_id" class="text-sm font-semibold text-slate-700">{{ __('Area') }}</label><select id="zone_id" name="zone_id" class="mt-1 block w-full rounded-xl border-slate-200 text-sm focus:border-sky-500 focus:ring-sky-500"><option value="">{{ __('General community') }}</option>@foreach($zones as $zone)<option value="{{ $zone->id }}">{{ $zone->name }}, {{ $zone->city }}</option>@endforeach</select></div>
                    <div><label for="title" class="text-sm font-semibold text-slate-700">{{ __('Title') }}</label><input id="title" name="title" value="{{ old('title') }}" maxlength="120" required class="mt-1 block w-full rounded-xl border-slate-200 text-sm focus:border-sky-500 focus:ring-sky-500"></div>
                    <div><label for="body" class="text-sm font-semibold text-slate-700">{{ __('Message') }}</label><textarea id="body" name="body" rows="5" maxlength="2000" required class="mt-1 block w-full rounded-xl border-slate-200 text-sm focus:border-sky-500 focus:ring-sky-500">{{ old('body') }}</textarea></div>
                    <button class="w-full rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-sky-700">{{ __('Post update') }}</button>
                </form>
            </aside>
        </div>
    </div>
</x-layouts.dashboard>
