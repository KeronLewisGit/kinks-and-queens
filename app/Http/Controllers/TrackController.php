<?php

namespace App\Http\Controllers;

use App\Models\PreOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackController extends Controller
{
    public function form(): View
    {
        return view('track.form');
    }

    public function lookup(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:12'],
            'phone' => ['required', 'string', 'max:40'],
        ], ['phone.required' => 'Enter the last four digits of the phone number on your request.']);

        $ref = strtoupper(trim($data['reference']));
        if (! str_starts_with($ref, 'KQ-')) {
            $ref = 'KQ-'.ltrim($ref, '-');
        }
        $order = PreOrder::where('reference', $ref)->first();

        if (! $order || ! $order->phoneMatches($data['phone'])) {
            return back()->withInput()->withErrors(['reference' => 'We could not find a request with that reference and phone number.']);
        }

        $request->session()->put('kq.orders.'.$order->reference, true);

        return redirect()->route('track.show', $order);
    }

    public function show(Request $request, PreOrder $preOrder): View|RedirectResponse
    {
        if (! $request->session()->get('kq.orders.'.$preOrder->reference)) {
            return redirect()->route('track')->withInput(['reference' => $preOrder->reference]);
        }

        return view('track.show', ['order' => $preOrder->load('texture', 'events')]);
    }
}
