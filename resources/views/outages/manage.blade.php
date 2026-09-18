<x-layouts.dashboard :title="__('Manage outages')">
    <div class="mx-auto max-w-7xl">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold text-sky-700">{{ __('Network operations') }}</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">{{ __('Publish and update outages') }}</h1>
            <p class="mt-3 text-slate-600">{{ __('Affected clients are notified automatically when an outage is published, begins, or is resolved.') }}</p>
        </div>

        <div class="mt-8 grid gap-6 xl:grid-cols-[.8fr_1.2fr]">
            <section class="h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-lg font-semibold text-slate-950">{{ __('Publish an outage') }}</h2>
                <form method="POST" action="{{ route('outages.manage.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div><label for="zone_id" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('Affected zone') }}</label><select id="zone_id" name="zone_id" required class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3"><option value="">{{ __('Select a zone') }}</option>@foreach($zones as $zone)<option value="{{ $zone->id }}" @selected(old('zone_id') == $zone->id)>{{ $zone->name }}, {{ $zone->city }}</option>@endforeach</select>@error('zone_id')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror</div>
                    <div><label for="title" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('Title') }}</label><input id="title" name="title" value="{{ old('title') }}" required class="h-11 w-full rounded-xl border border-slate-300 px-3">@error('title')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror</div>
                    <div><label for="description" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('Description') }}</label><textarea id="description" name="description" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">{{ old('description') }}</textarea></div>
                    <div class="grid gap-4 sm:grid-cols-2"><div><label for="type" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('Type') }}</label><select id="type" name="type" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3"><option value="UNPLANNED">{{ __('Unplanned') }}</option><option value="SCHEDULED">{{ __('Scheduled') }}</option></select></div><div><label for="outage-status" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('Status') }}</label><select id="outage-status" name="status" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3"><option value="ONGOING">{{ __('Ongoing') }}</option><option value="PLANNED">{{ __('Planned') }}</option></select></div></div>
                    <div><label for="scheduled_start" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('Scheduled start') }}</label><input id="scheduled_start" name="scheduled_start" value="{{ old('scheduled_start') }}" type="datetime-local" class="h-11 w-full rounded-xl border border-slate-300 px-3">@error('scheduled_start')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror</div>
                    <div class="grid gap-4 sm:grid-cols-2"><div><label for="expected_end" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('Expected end') }}</label><input id="expected_end" name="expected_end" value="{{ old('expected_end') }}" type="datetime-local" class="h-11 w-full rounded-xl border border-slate-300 px-3"></div><div><label for="estimated_duration_minutes" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('Duration in minutes') }}</label><input id="estimated_duration_minutes" name="estimated_duration_minutes" value="{{ old('estimated_duration_minutes') }}" type="number" min="1" class="h-11 w-full rounded-xl border border-slate-300 px-3"></div></div>
                    <button class="h-11 w-full rounded-xl bg-sky-700 px-5 text-sm font-semibold text-white transition hover:bg-sky-800">{{ __('Publish notice') }}</button>
                </form>
            </section>

            <section>
                <div class="flex items-center justify-between gap-4"><h2 class="text-lg font-semibold text-slate-950">{{ __('Published outages') }}</h2><span class="text-sm text-slate-500">{{ trans_choice(':count notice|:count notices', $outages->total(), ['count' => $outages->total()]) }}</span></div>
                <div class="mt-4 grid gap-4">
                    @forelse($outages as $outage)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start"><div><div class="flex items-center gap-2"><span class="size-2.5 rounded-full {{ $outage->status === 'ONGOING' ? 'bg-rose-500' : ($outage->status === 'PLANNED' ? 'bg-amber-500' : 'bg-emerald-500') }}"></span><span class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __(ucfirst(strtolower($outage->status))) }}</span></div><h3 class="mt-2 font-semibold text-slate-950">{{ $outage->title }}</h3><p class="mt-1 text-sm text-slate-600">{{ $outage->zone->name }}, {{ $outage->zone->city }}</p></div><form method="POST" action="{{ route('outages.manage.update', $outage) }}" class="flex gap-2">@csrf @method('PATCH')<label class="sr-only" for="status-{{ $outage->id }}">{{ __('Status') }}</label><select id="status-{{ $outage->id }}" name="status" class="h-10 rounded-lg border border-slate-300 bg-white px-3 text-sm"><option value="PLANNED" @selected($outage->status === 'PLANNED')>{{ __('Planned') }}</option><option value="ONGOING" @selected($outage->status === 'ONGOING')>{{ __('Ongoing') }}</option><option value="RESOLVED" @selected($outage->status === 'RESOLVED')>{{ __('Resolved') }}</option><option value="CANCELLED" @selected($outage->status === 'CANCELLED')>{{ __('Cancelled') }}</option></select><button class="h-10 rounded-lg bg-slate-950 px-3 text-sm font-semibold text-white">{{ __('Update') }}</button></form></div>
                            @if($outage->description)<p class="mt-4 border-t border-slate-100 pt-4 text-sm leading-6 text-slate-600">{{ $outage->description }}</p>@endif
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center"><p class="font-semibold text-slate-950">{{ __('No outage notices yet') }}</p><p class="mt-2 text-sm text-slate-600">{{ __('Published notices will appear here.') }}</p></div>
                    @endforelse
                </div>
                <div class="mt-6">{{ $outages->links() }}</div>
            </section>
        </div>
    </div>
</x-layouts.dashboard>
