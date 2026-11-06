@extends('layouts.admin', ['title' => 'Today'])

@section('content')
<div class="flex flex-wrap items-end justify-between gap-4">
    <div>
        <p class="hint">{{ now()->format('l j F') }}</p>
        <h1 class="text-4xl">Today</h1>
    </div>
    <a href="{{ route('admin.orders.index', ['status' => 'open']) }}" class="btn btn-ghost">All open requests</a>
</div>

{{-- The one number to act on. --}}
<section class="mt-8 grid gap-5 lg:grid-cols-[1.2fr_1fr]">
    <div class="rounded-[1.5rem] bg-ink p-7 text-ivory">
        <p class="caps text-gold-light">To confirm</p>
        <p class="mt-2 font-display text-[4.5rem] leading-none">{{ $toConfirm->count() }}</p>
        <p class="mt-2 text-ivory/75">{{ $toConfirm->count() === 1 ? 'request is' : 'requests are' }} waiting for a message from you.</p>
        @if($toConfirm->isNotEmpty())
            <ul class="mt-6 grid gap-2">
                @foreach($toConfirm->take(5) as $o)
                    <li><a href="{{ route('admin.orders.show', $o) }}" class="flex min-h-12 items-center justify-between gap-3 rounded-xl bg-ivory/10 px-4 hover:bg-ivory/15">
                        <span><span class="font-semibold">{{ $o->name }}</span> <span class="text-ivory/70">· {{ $o->title() }}</span></span>
                        <span class="shrink-0 text-[.9rem] text-gold-light">{{ $o->created_at->diffForHumans(short: true) }}</span>
                    </a></li>
                @endforeach
            </ul>
        @endif
    </div>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-2">
        <div class="card bg-white p-5"><p class="hint">Ready to hand over</p><p class="mt-1 font-display text-4xl">{{ $ready->count() }}</p></div>
        <div class="card bg-white p-5"><p class="hint">On the way to you</p><p class="mt-1 font-display text-4xl">{{ $counts['ordered'] }}</p></div>
        <div class="card bg-white p-5"><p class="hint">Confirmed or paid, not yet ordered</p><p class="mt-1 font-display text-4xl">{{ $counts['confirmed'] + $counts['paid'] }}</p></div>
        <div class="card bg-white p-5"><p class="hint">Open orders at list price</p><p class="mt-1 font-display text-3xl"><x-money :amount="$pipelineValue"/></p></div>
    </div>
</section>

<section class="mt-10 grid gap-5 lg:grid-cols-2">
    <div class="card bg-white p-6">
        <h2 class="text-2xl">Arriving soon</h2>
        @forelse($arrivingSoon as $o)
            <a href="{{ route('admin.orders.show', $o) }}" class="flex min-h-12 items-center justify-between border-b border-sand py-2 last:border-0 hover:text-gold-text">
                <span>{{ $o->name }} · {{ $o->title() }}</span>
                <span class="hint">{{ $o->expected_at?->format('j M') ?? 'no date' }}</span>
            </a>
        @empty
            <p class="mt-3 text-cocoa">Nothing on the way right now.</p>
        @endforelse
    </div>
    <div class="card bg-white p-6">
        <h2 class="text-2xl">Ready for collection or delivery</h2>
        @forelse($ready as $o)
            <a href="{{ route('admin.orders.show', $o) }}" class="flex min-h-12 items-center justify-between border-b border-sand py-2 last:border-0 hover:text-gold-text">
                <span>{{ $o->name }} · {{ $o->title() }}</span><span class="hint">{{ $o->area }}</span>
            </a>
        @empty
            <p class="mt-3 text-cocoa">Nothing waiting.</p>
        @endforelse
        <p class="hint mt-4">{{ $monthCompleted }} completed this month.</p>
    </div>
</section>
@endsection
