@extends('layouts.app', ['flushFooter' => true, 'title' => 'Care guide', 'description' => 'How to keep Kinks & Queens textured extensions soft, defined and in rotation for years.'])

@section('content')
<x-page-hero title="Care guide" script="low effort, long life" lead="Good textured hair shouldn’t only look good on install day. With the right care, it’s something you can reach for again and again." image="assets/img/kinky-curly-closeup.webp" alt="Close-up of defined Brazilian Kinky Curly curls" coil="curl"/>

<section class="wrap py-20">
    <div class="reveal max-w-[640px]">
        <p class="caps text-gold-text">The daily routine</p>
        <h2 class="mt-3 text-4xl sm:text-5xl">Our 7-day curl routine, in four steps</h2>
        <p class="mt-4 text-cocoa">This is the exact routine from our week-long Kinky Curly wear test. No curl creams, gels or mousses.</p>
    </div>
    <ol class="mt-12 grid gap-5 md:grid-cols-4">
        @foreach($care as $i => $c)
            <li class="reveal card relative overflow-hidden p-6" style="--d:{{ $i * .08 }}s">
                <span class="flex size-12 items-center justify-center rounded-full bg-ink font-display text-[1.3rem] text-gold-light"><span class="sr-only">Step </span>{{ $i + 1 }}</span>
                <h3 class="relative mt-5 text-[1.5rem]">{{ $c['title'] }}</h3>
                <p class="relative mt-3 text-cocoa">{{ $c['body'] }}</p>
            </li>
        @endforeach
    </ol>
</section>

<section class="bg-cream/60 py-20">
    <div class="wrap grid items-center gap-12 lg:grid-cols-[.8fr_1.2fr]">
        <div class="frame reveal mx-auto w-full max-w-[420px]"><img src="{{ asset('assets/img/afro-coily-updo.webp') }}" alt="Mongolian Afro Coily, two years old, styled into an updo" width="560" height="760" loading="lazy" class="aspect-[4/5] w-full object-cover"></div>
        <div class="reveal">
            <h2 class="text-4xl">Wash day and storage</h2>
            <ul class="mt-8 grid gap-4">
                @foreach($wash as $w)
                    <li class="flex gap-4"><x-crown class="mt-1.5 shrink-0 text-gold"/><span class="text-[1.05rem]">{{ $w }}</span></li>
                @endforeach
            </ul>
            <p class="mt-8 rounded-2xl bg-ivory p-5 text-cocoa">Proof it works: one client’s Afro Coily bundles are about two years old and still in rotation.</p>
        </div>
    </div>
</section>
@endsection
