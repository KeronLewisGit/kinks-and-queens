@extends('layouts.app', ['title' => 'Order '.$order->reference])

@php
    $flow = ['new', 'confirmed', 'paid', 'ordered', 'ready', 'completed'];
    $at = array_search($order->status, $flow, true);
    $reached = $order->events->groupBy('status');
@endphp

@section('content')
<section class="wrap py-14 lg:py-20">
    <div class="mx-auto grid max-w-[1000px] gap-10 lg:grid-cols-[1fr_.9fr]">
        <div>
            <p class="caps text-gold-text">Order {{ $order->reference }}</p>
            <h1 class="mt-3 text-5xl">{{ $order->statusLabel() }}</h1>
            <p class="mt-4 text-[1.1rem] text-cocoa">{{ \App\Models\PreOrder::STATUSES[$order->status]['copy'] ?? '' }}</p>
            @if($order->status === 'ordered' && $order->expected_at)
                <p class="mt-5 inline-flex rounded-full bg-ink px-4 py-2 font-semibold text-ivory">Expected by {{ $order->expected_at->format('l j F') }}</p>
            @endif

            @if($order->status === 'cancelled')
                <p class="mt-8 rounded-2xl border border-sand bg-white/70 p-5 text-cocoa">If you would like to start again, <a href="{{ route('preorder') }}" class="link">send a new request</a>.</p>
            @else
            <ol class="timeline mt-10 grid gap-6" aria-label="Order progress">
                @foreach($flow as $i => $s)
                    @php $ev = $reached[$s] ?? collect(); @endphp
                    <li @class(['done' => $at !== false && $i < $at, 'current' => $i === $at])>
                        <span class="dot" aria-hidden="true"></span>
                        <p class="font-semibold">{{ \App\Models\PreOrder::STATUSES[$s]['label'] }}
                            @if($i === $at)<span class="sr-only">(current step)</span>@endif
                        </p>
                        @if($ev->isNotEmpty())
                            <p class="hint">{{ $ev->last()->created_at->format('j M Y') }}</p>
                            @foreach($ev->whereNotNull('note') as $e)<p class="mt-1 text-cocoa">“{{ $e->note }}”</p>@endforeach
                        @endif
                    </li>
                @endforeach
            </ol>
            @endif
        </div>
        <aside class="card h-fit p-6 sm:p-8">
            <h2 class="text-2xl">{{ $order->title() }}</h2>
            <dl class="mt-5 grid gap-3">
                @if($order->lengthsLabel())<div class="flex justify-between gap-4"><dt class="text-mocha">Length</dt><dd class="font-semibold">{{ $order->lengthsLabel() }}</dd></div>@endif
                @foreach(($order->details ?? []) as $k => $v)
                    @continue($k === 'length')
                    <div class="flex justify-between gap-4"><dt class="text-mocha">{{ \Illuminate\Support\Str::headline($k) }}</dt><dd class="font-semibold">{{ $v }}</dd></div>
                @endforeach
                @if($order->quoted_price)<div class="flex justify-between gap-4"><dt class="text-mocha">Price</dt><dd class="font-semibold"><x-money :amount="$order->quoted_price"/></dd></div>@endif
                <div class="flex justify-between gap-4"><dt class="text-mocha">Requested</dt><dd class="font-semibold">{{ $order->created_at->format('j M Y') }}</dd></div>
            </dl>
            <a href="{{ \App\Models\Setting::instagramDm() }}" class="btn btn-ghost mt-6 w-full" rel="noopener">Questions? DM us</a>
        </aside>
    </div>
</section>
@endsection
