<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\Outage;
use App\Models\OutageReport;
use App\Models\Prediction;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $baseData = ['role' => $user->role];

        if ($user->hasRole('ADMIN')) {
            return view('dashboard', $baseData + [
                'title' => 'Operations overview',
                'subtitle' => 'A concise view of verified activity and work that needs your attention.',
                'metrics' => [
                    ['label' => 'Active outages', 'value' => Outage::where('status', 'ONGOING')->count(), 'description' => 'Need live monitoring', 'tone' => 'rose'],
                    ['label' => 'Reports to review', 'value' => OutageReport::where('status', 'PENDING')->count(), 'description' => 'Awaiting validation', 'tone' => 'amber'],
                    ['label' => 'Registered clients', 'value' => User::where('role', 'CLIENT')->count(), 'description' => 'Receiving information', 'tone' => 'sky'],
                    ['label' => 'Managed zones', 'value' => Zone::count(), 'description' => 'Coverage areas', 'tone' => 'emerald'],
                ],
                'priority' => ['eyebrow' => 'Next priority', 'title' => trans_choice(':count community report needs review|:count community reports need review', OutageReport::where('status', 'PENDING')->count(), ['count' => OutageReport::where('status', 'PENDING')->count()]), 'description' => 'Validate, reject, or use corroborated reports to strengthen the current network picture.', 'actionLabel' => 'Review outage board', 'actionUrl' => route('outages.index')],
                'analytics' => [
                    ['label' => 'Verification queue', 'value' => OutageReport::where('status', 'PENDING')->count(), 'percentage' => min(100, OutageReport::where('status', 'PENDING')->count() * 15), 'tone' => 'bg-amber-500'],
                    ['label' => 'Live incident load', 'value' => Incident::whereIn('status', ['OPEN', 'IN_PROGRESS'])->count(), 'percentage' => min(100, Incident::whereIn('status', ['OPEN', 'IN_PROGRESS'])->count() * 20), 'tone' => 'bg-rose-500'],
                    ['label' => 'Zone coverage', 'value' => Zone::count(), 'percentage' => min(100, Zone::count() * 10), 'tone' => 'bg-sky-600'],
                ],
                'activity' => OutageReport::with('user', 'zone')->where('status', 'PENDING')->latest()->take(5)->get(),
                'currentOutages' => Outage::query()->where('status', 'ONGOING')->with('zone')->latest('actual_start')->take(5)->get(),
                'scheduledOutages' => Outage::query()->where('status', 'PLANNED')->with('zone')->orderBy('scheduled_start')->take(4)->get(),
            ]);
        }

        if ($user->hasRole('PROVIDER')) {
            return view('dashboard', $baseData + [
                'title' => 'Provider workspace',
                'subtitle' => 'Monitor your published notices and keep the network picture accurate.',
                'metrics' => [
                    ['label' => 'Open incidents', 'value' => Incident::whereBelongsTo($user, 'provider')->whereIn('status', ['OPEN', 'IN_PROGRESS'])->count(), 'description' => 'Require updates', 'tone' => 'rose'],
                    ['label' => 'Your active outages', 'value' => Outage::where('created_by', $user->id)->where('status', 'ONGOING')->count(), 'description' => 'Visible to customers', 'tone' => 'amber'],
                    ['label' => 'Planned notices', 'value' => Outage::where('created_by', $user->id)->where('status', 'PLANNED')->count(), 'description' => 'Upcoming work', 'tone' => 'sky'],
                    ['label' => 'Service zones', 'value' => Zone::count(), 'description' => 'Available areas', 'tone' => 'emerald'],
                ],
                'priority' => ['eyebrow' => 'Operational focus', 'title' => 'Keep incident status current', 'description' => 'A timely update helps people understand whether power is being restored or work is still in progress.', 'actionLabel' => 'Open outage board', 'actionUrl' => route('outages.index', ['status' => 'ONGOING'])],
                'analytics' => [
                    ['label' => 'Open incidents', 'value' => Incident::whereBelongsTo($user, 'provider')->whereIn('status', ['OPEN', 'IN_PROGRESS'])->count(), 'percentage' => min(100, Incident::whereBelongsTo($user, 'provider')->whereIn('status', ['OPEN', 'IN_PROGRESS'])->count() * 25), 'tone' => 'bg-rose-500'],
                    ['label' => 'Published maintenance', 'value' => Outage::where('created_by', $user->id)->where('status', 'PLANNED')->count(), 'percentage' => min(100, Outage::where('created_by', $user->id)->where('status', 'PLANNED')->count() * 25), 'tone' => 'bg-sky-600'],
                    ['label' => 'Resolved notices', 'value' => Outage::where('created_by', $user->id)->where('status', 'RESOLVED')->count(), 'percentage' => min(100, Outage::where('created_by', $user->id)->where('status', 'RESOLVED')->count() * 20), 'tone' => 'bg-emerald-500'],
                ],
                'activity' => Incident::whereBelongsTo($user, 'provider')->with('zone')->latest('occurred_at')->take(5)->get(),
                'currentOutages' => Outage::query()->where('created_by', $user->id)->where('status', 'ONGOING')->with('zone')->latest('actual_start')->take(5)->get(),
                'scheduledOutages' => Outage::query()->where('created_by', $user->id)->where('status', 'PLANNED')->with('zone')->orderBy('scheduled_start')->take(4)->get(),
            ]);
        }

        $savedZoneIds = $user->locations()->whereNotNull('zone_id')->pluck('zone_id');
        $locationCount = $user->locations()->count();
        $relevantCurrentOutages = Outage::query()->where('status', 'ONGOING')->when($savedZoneIds->isNotEmpty(), fn ($query) => $query->whereIn('zone_id', $savedZoneIds))->with('zone')->latest('actual_start')->take(5)->get();
        $relevantScheduledOutages = Outage::query()->where('status', 'PLANNED')->when($savedZoneIds->isNotEmpty(), fn ($query) => $query->whereIn('zone_id', $savedZoneIds))->with('zone')->orderBy('scheduled_start')->take(4)->get();
        $highRiskPredictions = Prediction::query()->when($savedZoneIds->isNotEmpty(), fn ($query) => $query->whereIn('zone_id', $savedZoneIds))->whereIn('risk_level', ['HIGH', 'CRITICAL'])->count();
        $pendingReports = $user->reports()->where('status', 'PENDING')->count();

        return view('dashboard', $baseData + [
            'title' => __('Good day, :name', ['name' => $user->first_name ?: $user->name]),
            'subtitle' => $locationCount ? 'Your dashboard is focused on the places you have saved.' : 'Save a location to receive information that is specific to you.',
            'metrics' => [
                ['label' => 'Saved locations', 'value' => $locationCount, 'description' => 'Places you follow', 'tone' => 'sky'],
                ['label' => 'Relevant outages', 'value' => $relevantCurrentOutages->count(), 'description' => $locationCount ? 'At your saved places' : 'Across the network', 'tone' => 'rose'],
                ['label' => 'Upcoming maintenance', 'value' => $relevantScheduledOutages->count(), 'description' => 'Plan ahead', 'tone' => 'amber'],
                ['label' => 'High-risk forecasts', 'value' => $highRiskPredictions, 'description' => 'Clearly marked forecasts', 'tone' => 'emerald'],
            ],
            'priority' => $locationCount ? ['eyebrow' => 'Your next step', 'title' => $relevantCurrentOutages->count() ? 'Review the outage affecting your saved area' : 'You have no active outage at saved locations', 'description' => $relevantCurrentOutages->count() ? 'Open the outage board for the latest status and expected restoration information.' : 'Keep your locations updated so future alerts stay accurate.', 'actionLabel' => $relevantCurrentOutages->count() ? 'View relevant outages' : 'Manage locations', 'actionUrl' => $relevantCurrentOutages->count() ? route('outages.index', ['status' => 'ONGOING']) : route('locations.index')] : ['eyebrow' => 'Personalise your dashboard', 'title' => 'Add your first location', 'description' => 'Save home, work, or school to see the outages and maintenance that matter to you.', 'actionLabel' => 'Add a location', 'actionUrl' => route('locations.index')],
            'analytics' => [
                ['label' => 'Locations followed', 'value' => $locationCount, 'percentage' => min(100, $locationCount * 25), 'tone' => 'bg-sky-600'],
                ['label' => 'Reports awaiting review', 'value' => $pendingReports, 'percentage' => min(100, $pendingReports * 25), 'tone' => 'bg-amber-500'],
                ['label' => 'Forecast risk', 'value' => $highRiskPredictions, 'percentage' => min(100, $highRiskPredictions * 30), 'tone' => 'bg-emerald-500'],
            ],
            'activity' => $user->reports()->with('zone')->latest('reported_at')->take(5)->get(),
            'currentOutages' => $relevantCurrentOutages,
            'scheduledOutages' => $relevantScheduledOutages,
        ]);
    }
}
