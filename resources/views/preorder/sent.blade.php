@extends('layouts.app', ['title' => 'Request sent'])

@section('content')
<section class="wrap py-16 lg:py-24">
    <div class="mx-auto max-w-[760px] text-center">
        <x-crown class="mx-auto size-10 text-gold" />
        <p class="caps mt-6 text-gold-text">Request received</p>
        <h1 class="mt-4 text-5xl sm:text-6xl">Thank you, {{ strtok($order->name, ' ') }}.</h1>
        <p class="mx-auto mt-5 max-w-[52ch] text-[1.15rem] text-cocoa">We will message you to confirm {{ $order->isClipInService() ? 'your clip-in set' : 'texture, lengths and price' }}. Nothing is ordered or charged until you agree.</p>

        <div class="mt-10 rounded-[1.75rem] border border-sand bg-white/70 p-6 text-left sm:p-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="hint">Your reference</p>
                    <p class="font-display text-[2.4rem] font-semibold tracking-wide">{{ $order->reference }}</p>
                </div>
                <div x-data="copy(@js($order->handoffMessage()))">
                    <button type="button" class="btn btn-ghost" @click="go()"><span x-text="done ? 'Copied' : 'Copy details'">Copy details</span></button>
                </div>
            </div>
            <dl class="mt-6 grid gap-3 border-t border-sand pt-6 sm:grid-cols-2">
                <div><dt class="hint">Request</dt><dd class="font-semibold">{{ $order->title() }}</dd></div>
                @if($order->lengthsLabel())<div><dt class="hint">Length</dt><dd class="font-semibold">{{ $order->lengthsLabel() }}</dd></div>@endif
                @if($order->quoted_price)<div><dt class="hint">List price</dt><dd class="font-semibold"><x-money :amount="$order->quoted_price"/></dd></div>@endif
                <div><dt class="hint">We’ll reach you by</dt><dd class="font-semibold">{{ $order->contactViaLabel() }}</dd></div>
            </dl>
        </div>

        <h2 class="mt-12 text-3xl">Want to skip the wait?</h2>
        <p class="mt-3 text-cocoa">Send us your reference and we’ll pick it up straight away.</p>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            @if($whatsapp)<a href="{{ $whatsapp }}" class="btn btn-primary" rel="noopener">Send on WhatsApp</a>@endif
            <a href="{{ $instagram }}" class="btn {{ $whatsapp ? 'btn-ghost' : 'btn-primary' }}" rel="noopener">DM us on Instagram</a>
        </div>
        <p class="mt-8"><a href="{{ route('track.show', $order) }}" class="link">Follow this request</a> · save your reference to check it any time.</p>
    </div>
</section>
@endsection
