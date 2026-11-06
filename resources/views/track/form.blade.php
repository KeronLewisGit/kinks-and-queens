@extends('layouts.app', ['title' => 'Track your order', 'description' => 'Check the status of your Kinks & Queens pre-order with your reference and phone number.'])

@section('content')
<x-page-hero title="Track your order" script="every step" lead="Pre-orders take up to 14 business days once confirmed and shipped. Enter your reference to see where yours is." image="assets/img/kinky-curly-red-back.webp" alt="Back view of voluminous Brazilian Kinky Curly hair" coil="curl"/>
<section class="wrap py-16">
    <form method="POST" action="{{ route('track.lookup') }}" data-once class="card mx-auto grid max-w-[560px] gap-5 p-6 sm:p-8">
        @csrf
        @if(session('notice'))<p class="rounded-xl bg-cream p-3 text-cocoa">{{ session('notice') }}</p>@endif
        <label class="field">
            <span>Reference</span>
            <input class="input uppercase tracking-wider" name="reference" value="{{ old('reference') }}" placeholder="KQ-AB234" autocapitalize="characters" required maxlength="12" @error('reference') aria-invalid="true" aria-describedby="e-ref" @enderror>
            @error('reference')<span id="e-ref" class="err">{{ $message }}</span>@enderror
        </label>
        <label class="field">
            <span>Last 4 digits of your phone number</span>
            <input class="input" name="phone" inputmode="numeric" autocomplete="off" maxlength="40" required placeholder="0101" @error('phone') aria-invalid="true" aria-describedby="e-phone" @enderror>
            @error('phone')<span id="e-phone" class="err">{{ $message }}</span>@enderror
        </label>
        <button class="btn btn-primary">Show my order</button>
        <p class="hint">Lost your reference? <a href="{{ \App\Models\Setting::instagramDm() }}" class="link" rel="noopener">DM us</a> and we’ll find it.</p>
    </form>
</section>
@endsection
