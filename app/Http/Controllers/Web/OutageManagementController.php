<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Outage;
use App\Models\Zone;
use App\Services\NotifyAffectedClients;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OutageManagementController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Outage::class);
        $outages = Outage::query()
            ->when($request->user()->hasRole('PROVIDER'), fn ($query) => $query->whereBelongsTo($request->user(), 'creator'))
            ->latest()
            ->paginate(12);

        return view('outages.manage', [
            'outages' => $outages,
            'zones' => Zone::query()->orderBy('city')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, NotifyAffectedClients $notifyAffectedClients): RedirectResponse
    {
        Gate::authorize('create', Outage::class);
        $data = $this->validatedData($request);
        $outage = Outage::create($this->withLifecycleTimestamps($data) + [
            'created_by' => $request->user()->id,
            'source' => $request->user()->isAdmin() ? 'ADMIN' : 'PROVIDER',
        ]);
        $notifyAffectedClients->handle($outage);

        return back()->with('success', __('The outage notice was published.'));
    }

    public function update(Request $request, Outage $outage, NotifyAffectedClients $notifyAffectedClients): RedirectResponse
    {
        Gate::authorize('update', $outage);
        $data = $request->validate(['status' => ['required', 'in:PLANNED,ONGOING,RESOLVED,CANCELLED']]);
        $previousStatus = $outage->status;
        $outage->update($this->withLifecycleTimestamps($data, $previousStatus));

        if ($outage->status !== $previousStatus) {
            $notifyAffectedClients->handle($outage->fresh());
        }

        return back()->with('success', __('The outage status was updated.'));
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'zone_id' => ['required', 'exists:zones,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'type' => ['required', 'in:SCHEDULED,UNPLANNED'],
            'status' => ['required', 'in:PLANNED,ONGOING'],
            'scheduled_start' => ['nullable', 'required_if:status,PLANNED', 'date'],
            'expected_end' => ['nullable', 'date', 'after:scheduled_start'],
            'estimated_duration_minutes' => ['nullable', 'integer', 'min:1', 'max:10080'],
        ]);
    }

    private function withLifecycleTimestamps(array $data, ?string $previousStatus = null): array
    {
        if (($data['status'] ?? null) === 'ONGOING' && $previousStatus !== 'ONGOING') {
            $data['actual_start'] = now();
        }

        if (($data['status'] ?? null) === 'RESOLVED' && $previousStatus !== 'RESOLVED') {
            $data['actual_end'] = now();
        }

        return $data;
    }
}
