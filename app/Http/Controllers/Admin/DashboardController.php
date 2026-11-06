<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreOrder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $open = PreOrder::open()->get();

        return view('admin.dashboard', [
            'toConfirm' => PreOrder::with('texture')->where('status', 'new')->oldest()->get(),
            'counts' => collect(array_keys(PreOrder::STATUSES))->mapWithKeys(fn ($s) => [$s => $open->where('status', $s)->count()]),
            'arrivingSoon' => PreOrder::with('texture')->where('status', 'ordered')->orderBy('expected_at')->take(5)->get(),
            'ready' => PreOrder::with('texture')->where('status', 'ready')->oldest('updated_at')->get(),
            'pipelineValue' => $open->whereIn('status', ['confirmed', 'paid', 'ordered', 'ready'])->sum('quoted_price'),
            'monthCompleted' => PreOrder::where('status', 'completed')->where('updated_at', '>=', now()->startOfMonth())->count(),
        ]);
    }
}
