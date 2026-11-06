@extends('layouts.admin', ['title' => $order->reference])

@php
    $next = ['new' => 'confirmed', 'confirmed' => 'paid', 'paid' => 'ordered', 'ordered' => 'ready', 'ready' => 'completed'][$order->status] ?? null;
@endphp

@section('content')
<a href="{{ route('admin.orders.index') }}" class="link inline-flex min-h-11 items-center">← Requests</a>
<div class="mt-2 flex flex-wrap items-center gap-4">
    <h1 class="text-4xl">{{ $order->reference }}</h1>
    <span class="status status-{{ $order->status }} !text-[.95rem]">{{ $order->statusLabel() }}</span>
</div>
<p class="mt-1 text-cocoa">{{ $order->title() }} · received {{ $order->created_at->format('j M Y, g:ia') }}</p>

<div class="mt-8 grid gap-6 xl:grid-cols-[1.2fr_1fr]">
    <div class="grid content-start gap-6">
        <section class="card bg-white p-6">
            <h2 class="text-2xl">Request</h2>
            <dl class="mt-4 grid gap-3 sm:grid-cols-2">
                @if($order->texture)<div><dt class="hint">Texture</dt><dd class="font-semibold">{{ $order->texture->name }}</dd></div>@endif
                @if($order->format)<div><dt class="hint">Format</dt><dd class="font-semibold">{{ \App\Models\PreOrder::FORMATS[$order->format] ?? $order->format }}</dd></div>@endif
                @if($order->lengthsLabel())<div><dt class="hint">Lengths</dt><dd class="font-semibold">{{ $order->lengthsLabel() }}</dd></div>@endif
                @foreach(($order->details ?? []) as $k => $v)
                    @continue($k === 'length' && $order->lengthsLabel())
                    <div><dt class="hint">{{ \Illuminate\Support\Str::headline($k) }}</dt><dd class="font-semibold">{{ $v }}</dd></div>
                @endforeach
                @if($order->quoted_price)<div><dt class="hint">List price when requested</dt><dd class="font-semibold"><x-money :amount="$order->quoted_price"/></dd></div>@endif
            </dl>
            @if($order->notes)<p class="mt-5 rounded-xl bg-cream/60 p-4">“{{ $order->notes }}”</p>@endif
        </section>

        <section class="card bg-white p-6">
            <h2 class="text-2xl">Customer</h2>
            <dl class="mt-4 grid gap-3 sm:grid-cols-2">
                <div><dt class="hint">Name</dt><dd class="font-semibold">{{ $order->name }}</dd></div>
                <div><dt class="hint">Phone</dt><dd class="font-semibold"><a class="link" href="tel:{{ preg_replace('/[^0-9+]/', '', $order->phone) }}">{{ $order->phone }}</a></dd></div>
                <div><dt class="hint">Instagram</dt><dd class="font-semibold">@if($order->instagram)<a class="link" href="https://www.instagram.com/{{ $order->instagram }}/" rel="noopener" target="_blank">{{ '@'.$order->instagram }}</a>@else — @endif</dd></div>
                <div><dt class="hint">Email</dt><dd class="font-semibold">{{ $order->email ?: '—' }}</dd></div>
                <div><dt class="hint">Area</dt><dd class="font-semibold">{{ $order->area ?: '—' }}</dd></div>
                <div><dt class="hint">Prefers</dt><dd class="font-semibold">{{ $order->contactViaLabel() }}</dd></div>
            </dl>
            <div class="mt-5 flex flex-wrap gap-3">
                @if($whatsapp)<a href="{{ $whatsapp }}" class="btn btn-primary !min-h-11" target="_blank" rel="noopener">Message on WhatsApp</a>@endif
                @if($order->instagram)<a href="https://ig.me/m/{{ $order->instagram }}" class="btn btn-ghost !min-h-11" target="_blank" rel="noopener">Open Instagram DM</a>@endif
            </div>
        </section>
    </div>

    <div class="grid content-start gap-6">
        <section class="card bg-white p-6">
            <h2 class="text-2xl">Move this request</h2>
            <p class="hint mt-1">The customer sees the step and your note on their tracking page.</p>
            <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="mt-4 grid gap-3">
                @csrf
                <label class="field"><span>New step</span>
                    <select class="input" name="status">
                        @foreach(\App\Models\PreOrder::STATUSES as $k => $s)<option value="{{ $k }}" @selected(($next ?? $order->status) === $k)>{{ $s['label'] }}</option>@endforeach
                    </select>
                </label>
                <label class="field"><span>Note for the customer <span class="font-normal text-mocha">(optional)</span></span><input class="input" name="note" maxlength="240" placeholder="e.g. Shipped today, tracking with us"></label>
                <button class="btn btn-primary">Update</button>
            </form>
        </section>

        <section class="card bg-white p-6">
            <h2 class="text-2xl">Internal</h2>
            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="mt-4 grid gap-3">
                @csrf @method('PATCH')
                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="field"><span>Agreed price (TT$)</span><input class="input" type="number" min="0" name="quoted_price" value="{{ $order->quoted_price }}"></label>
                    <label class="field"><span>Expected by</span><input class="input" type="date" name="expected_at" value="{{ $order->expected_at?->format('Y-m-d') }}"></label>
                </div>
                <label class="field"><span>Staff notes (never shown to the customer)</span><textarea class="input min-h-24" name="staff_notes">{{ $order->staff_notes }}</textarea></label>
                <button class="btn btn-ghost">Save</button>
            </form>
        </section>

        <section class="card bg-white p-6">
            <h2 class="text-2xl">History</h2>
            <ol class="timeline mt-5 grid gap-5">
                @foreach($order->events as $e)
                    <li class="done"><span class="dot"></span>
                        <p class="font-semibold">{{ \App\Models\PreOrder::STATUSES[$e->status]['label'] ?? $e->status }}</p>
                        <p class="hint">{{ $e->created_at->format('j M Y, g:ia') }}@if($e->user) · {{ $e->user->name }}@endif</p>
                        @if($e->note)<p class="text-cocoa">“{{ $e->note }}”</p>@endif
                    </li>
                @endforeach
            </ol>
            <a class="link mt-4 inline-flex min-h-11 items-center" href="{{ route('track') }}" target="_blank" rel="noopener">Customer tracking page ↗</a>
        </section>
    </div>
</div>
@endsection
