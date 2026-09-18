<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Outage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OutageController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $outages = Outage::query()
            ->with('zone')
            ->when(in_array($status, ['ONGOING', 'PLANNED', 'RESOLVED'], true), fn ($query) => $query->where('status', $status))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(fn ($query) => $query
                    ->where('title', 'like', '%'.$search.'%')
                    ->orWhereHas('zone', fn ($query) => $query->where('name', 'like', '%'.$search.'%')->orWhere('city', 'like', '%'.$search.'%')));
            })
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('outages.index', compact('outages', 'status'));
    }
}
