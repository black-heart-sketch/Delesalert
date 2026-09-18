<x-layouts.dashboard :title="__('Notifications')">
    <div class="mx-auto max-w-4xl">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><p class="text-sm font-semibold text-sky-700">{{ __('Personal alerts') }}</p><h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">{{ __('Notifications') }}</h1><p class="mt-2 text-slate-600">{{ trans_choice(':count unread notification|:count unread notifications', $unreadCount, ['count' => $unreadCount]) }}</p></div>@if($unreadCount)<form method="POST" action="{{ route('notifications.read-all') }}">@csrf @method('PATCH')<button class="h-10 rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">{{ __('Mark all as read') }}</button></form>@endif</div>
        <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="divide-y divide-slate-100">
                @forelse($notifications as $notification)
                    @php($data = $notification->data)
                    <article class="flex gap-4 p-5 {{ $notification->read_at ? 'bg-white' : 'bg-sky-50/60' }}"><span class="mt-1 grid size-10 shrink-0 place-items-center rounded-full {{ ($data['status'] ?? '') === 'RESOLVED' ? 'bg-emerald-100 text-emerald-700' : (($data['status'] ?? '') === 'PLANNED' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}"><svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg></span><div class="min-w-0 flex-1"><div class="flex flex-wrap items-start justify-between gap-2"><h2 class="font-semibold text-slate-950">{{ __($data['title_key'] ?? 'Outage update') }}</h2><time class="text-xs text-slate-500">{{ $notification->created_at->diffForHumans() }}</time></div><p class="mt-1 text-sm leading-6 text-slate-600">{{ __($data['message_key'] ?? 'The outage information for :zone has been updated.', ['zone' => $data['zone'] ?? '']) }}</p><div class="mt-3 flex items-center gap-4"><a href="{{ route('outages.index', ['search' => $data['zone'] ?? '']) }}" class="text-sm font-semibold text-sky-700">{{ __('View outage') }}</a>@unless($notification->read_at)<form method="POST" action="{{ route('notifications.read', $notification->id) }}">@csrf @method('PATCH')<button class="text-sm font-semibold text-slate-600 hover:text-slate-950">{{ __('Mark as read') }}</button></form>@endunless</div></div></article>
                @empty
                    <div class="px-6 py-16 text-center"><p class="font-semibold text-slate-950">{{ __('No notifications yet') }}</p><p class="mt-2 text-sm text-slate-600">{{ __('Alerts for your saved locations will appear here.') }}</p></div>
                @endforelse
            </div>
        </div>
        <div class="mt-6">{{ $notifications->links() }}</div>
    </div>
</x-layouts.dashboard>
