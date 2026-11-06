<?php

namespace App\Http\Controllers;

use App\Models\PreOrder;
use App\Support\RequestNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClipInController extends Controller
{
    public function show(): View
    {
        return view('pages.clipins');
    }

    public function store(Request $request, RequestNotifier $notifier): RedirectResponse
    {
        if ($request->filled('website')) {
            return redirect()->route('home');
        }

        $data = $request->validate([
            'texture' => ['required', 'string', 'max:80'],
            'bundles' => ['required', Rule::in(['1', '2', '3', '4', '5+'])],
            'length' => ['nullable', 'string', 'max:40'],
            'fullness' => ['required', Rule::in(['double', 'triple', 'advise'])],
            'own_hair' => ['nullable', Rule::in(['Natural', 'Relaxed', 'Silk-pressed', 'Not sure'])],
            ...PreOrderController::contactRules(),
        ], PreOrderController::messages() + ['texture.required' => 'Tell us the texture of the bundles you have.']);

        $order = PreOrder::create([
            'kind' => 'clip_in_service',
            'format' => 'clip-ins',
            'details' => array_filter([
                'texture' => $data['texture'],
                'bundles' => $data['bundles'],
                'length' => $data['length'] ?? null,
                'fullness' => ['double' => 'Double tracked', 'triple' => 'Triple tracked', 'advise' => 'Advise me'][$data['fullness']],
                'own_hair' => $data['own_hair'] ?? null,
            ]),
            ...PreOrderController::contactFields($data),
        ]);
        $order->events()->create(['status' => 'new']);
        $notifier->send($order);

        $request->session()->put('kq.orders.'.$order->reference, true);

        return redirect()->route('preorder.sent', $order);
    }
}
