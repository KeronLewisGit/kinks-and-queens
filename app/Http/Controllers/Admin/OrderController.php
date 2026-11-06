<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreOrder;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $kind = $request->query('kind');
        $q = trim((string) $request->query('q'));

        $orders = PreOrder::with('texture')
            ->when($status === 'open', fn ($w) => $w->open())
            ->when($status && $status !== 'open' && isset(PreOrder::STATUSES[$status]), fn ($w) => $w->where('status', $status))
            ->when(in_array($kind, ['hair', 'clip_in_service'], true), fn ($w) => $w->where('kind', $kind))
            ->when($q !== '', fn ($w) => $w->where(fn ($s) => $s
                ->where('reference', 'like', "%{$q}%")->orWhere('name', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%")->orWhere('instagram', 'like', "%{$q}%")))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.orders.index', compact('orders', 'status', 'kind', 'q'));
    }

    public function show(PreOrder $preOrder): View
    {
        return view('admin.orders.show', [
            'order' => $preOrder->load('texture', 'priceSet', 'events.user'),
            'whatsapp' => $this->customerWhatsapp($preOrder),
        ]);
    }

    public function status(Request $request, PreOrder $preOrder): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(PreOrder::STATUSES))],
            'note' => ['nullable', 'string', 'max:240'],
        ]);
        if ($data['status'] === $preOrder->status && blank($data['note'] ?? null)) {
            return back()->withErrors(['status' => 'This request is already “'.$preOrder->statusLabel().'”. Choose another step, or add a note for the customer.']);
        }
        $preOrder->moveTo($data['status'], $data['note'] ?? null, $request->user());

        return back()->with('ok', 'Moved to “'.$preOrder->statusLabel().'”. The customer sees this on their tracking page.');
    }

    public function update(Request $request, PreOrder $preOrder): RedirectResponse
    {
        $data = $request->validate([
            'quoted_price' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'expected_at' => ['nullable', 'date', 'after:2020-01-01', 'before:2100-01-01'],
            'staff_notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $preOrder->update($data);

        return back()->with('ok', 'Saved.');
    }

    private function customerWhatsapp(PreOrder $order): ?string
    {
        $digits = Setting::normalisePhone($order->phone);
        if (strlen($digits) < 11) {
            return null;
        }
        $text = 'Hi '.strtok($order->name, ' ').', this is '.config('kq.name').' about your request '.$order->reference.'.';

        return 'https://wa.me/'.$digits.'?text='.rawurlencode($text);
    }
}
