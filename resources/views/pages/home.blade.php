@extends('layouts.app')

@php
    $heroImages = [
        'brazilian-kinky-curly' => ['assets/img/kinky-curly-portrait.webp', 'assets/img/kinky-curly-closeup.webp'],
        'mongolian-afro-coily' => ['assets/img/afro-coily-updo.webp', 'assets/img/afro-coily-queen-cam.webp'],
        'brazilian-textured-straight' => ['assets/img/straight-coloured-curls.webp', 'assets/img/straight-blend.webp'],
    ];
    $items = $textures->map(fn ($t) => [
        'name' => $t->name,
        'pattern' => $t->pattern,
        'bestFor' => $t->best_for,
        'tagline' => $t->tagline,
        'from' => $t->fromPrice(),
        'url' => route('textures.show', $t),
        'image' => asset($heroImages[$t->slug][0] ?? $t->image),
        'alt' => $t->image_alt,
    ])->values();
@endphp

@section('content')
{{-- ============ HERO: texture switcher ============ --}}
<section class="relative overflow-hidden" x-data="textureSwitcher(@js($items))" x-init="start()">
    <div aria-hidden="true" class="watermark absolute -left-10 top-6 hidden text-[30rem] xl:block">KQ</div>
    <div class="wrap relative grid items-center gap-12 pb-16 pt-10 lg:grid-cols-[1.05fr_.95fr] lg:gap-16 lg:pb-24 lg:pt-14">
        <div class="reveal">
            <p class="crown-rule left caps text-gold-text"><x-crown/> Premium textured hair</p>
            <h1 class="mt-6 text-[2.75rem] leading-[1.02] sm:text-6xl xl:text-[5.1rem]">
                Extensions that look like they grew from
                <span class="script block text-[1.3em] leading-[.85] text-gold-text">your own scalp.</span>
            </h1>
            <p class="mt-7 max-w-[52ch] text-[1.15rem] text-cocoa">
                100% human hair in Kinky Curly, Afro Coily and Textured Straight. Bundles, ponytails and custom clip-ins, ordered for you and delivered within 14 business days.
            </p>
            <div class="mt-9 flex flex-wrap gap-3">
                <a href="{{ route('preorder') }}" class="btn btn-primary">Start a pre-order</a>
                <a href="{{ route('match') }}" class="btn btn-ghost">Find my texture</a>
            </div>

            <div class="mt-10 max-w-[480px]" role="group" aria-label="Choose a texture to preview">
                <div class="grid grid-cols-3 gap-2 sm:grid-cols-1 sm:gap-2.5">
                    @foreach($textures as $i => $t)
                        <button type="button" class="swatch" @click="pick({{ $i }})" :aria-pressed="(i === {{ $i }}).toString()" aria-pressed="{{ $i === 0 ? 'true' : 'false' }}">
                            <img src="{{ asset($heroImages[$t->slug][1] ?? $t->image) }}" alt="" width="88" height="88" loading="lazy">
                            <span class="grid flex-1 leading-tight">
                                <span class="font-semibold"><span class="sm:hidden">{{ $t->pattern }}</span><span class="hidden sm:inline">{{ $t->name }}</span></span>
                                <span class="tile-sub hidden sm:block">For {{ lcfirst($t->best_for) }}</span>
                            </span>
                            <span class="hidden text-[.92rem] font-semibold sm:inline">from <x-money :amount="$t->fromPrice()"/></span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="reveal relative mx-auto w-full max-w-[520px]" style="--d:.15s">
            <div class="frame">
                <div class="stage frame-media overflow-hidden">
                    @foreach($items as $i => $item)
                        <img src="{{ $item['image'] }}" alt="{{ $item['alt'] }}" width="720" height="900"
                             @if($i > 0) loading="lazy" data-off @else fetchpriority="high" @endif
                             :data-off="i !== {{ $i }} ? '' : null">
                    @endforeach
                </div>
            </div>
            <div class="absolute -bottom-6 left-4 right-4 rounded-2xl border border-sand bg-ivory/95 p-4 shadow-[0_20px_40px_-24px_rgb(43_26_17/.6)] backdrop-blur sm:left-auto sm:right-[-1.5rem] sm:w-[300px]" aria-live="polite">
                <p class="caps text-gold-text" x-text="current.pattern">{{ $items[0]['pattern'] }}</p>
                <p class="mt-1 font-display text-[1.25rem] leading-snug" x-text="current.tagline">{{ $items[0]['tagline'] }}</p>
                <a :href="current.url" href="{{ $items[0]['url'] }}" class="link mt-2 inline-flex min-h-11 items-center text-[.95rem]">See lengths &amp; prices <span aria-hidden="true" class="ml-1">→</span></a>
            </div>
        </div>
    </div>
</section>

{{-- ============ PROMISE STRIP ============ --}}
<section class="border-y border-sand/80 bg-cream/60">
    <div class="wrap grid gap-8 py-10 sm:grid-cols-3">
        @foreach(config('kq.stats') as $i => $s)
            <div class="reveal flex items-center gap-4" style="--d:{{ $i * .08 }}s">
                <span class="font-display text-[2.8rem] font-semibold leading-none text-ink">{{ $s['value'] }}</span>
                <span class="max-w-[16ch] text-[1rem] leading-snug text-cocoa">{{ $s['label'] }}</span>
            </div>
        @endforeach
    </div>
</section>

{{-- ============ TEXTURES ============ --}}
<section class="wrap py-20 lg:py-28" aria-labelledby="textures-h">
    <div class="reveal max-w-[720px]">
        <p class="caps text-gold-text">The collection</p>
        <h2 id="textures-h" class="mt-4 text-4xl sm:text-5xl">Three textures. <span class="italic">One promise:</span> enhance your crown, never change it.</h2>
    </div>
    <div class="mt-14 grid gap-8 md:grid-cols-3">
        @foreach($textures as $i => $t)
            <article class="reveal group relative" style="--d:{{ $i * .1 }}s">
                <a href="{{ route('textures.show', $t) }}" class="block focus:outline-none" aria-label="{{ $t->name }}: lengths and prices">
                    <div class="frame">
                        <img src="{{ asset($t->image) }}" alt="{{ $t->image_alt }}" width="600" height="760" loading="lazy" class="aspect-[4/5] w-full object-cover transition duration-700 group-hover:scale-[1.015]">
                    </div>
                </a>
                <div class="mt-8">
                    <p class="caps text-gold-text">{{ $t->origin }}</p>
                    <h3 class="mt-2 text-[2rem]"><a href="{{ route('textures.show', $t) }}" class="hover:text-cocoa">{{ $t->pattern }}</a></h3>
                    <x-coil :type="$t->coil" :width="180" class="mt-3"/>
                    <p class="mt-3 text-cocoa">For {{ lcfirst($t->best_for) }}. {{ $t->tagline }}</p>
                    <p class="mt-4 flex flex-wrap items-center gap-2">
                        @foreach($t->formats as $f)<span class="chip">{{ \App\Models\PreOrder::FORMATS[$f] }}</span>@endforeach
                    </p>
                    <p class="mt-5 flex items-center justify-between border-t border-sand pt-4">
                        <span class="text-mocha">3-bundle sets from</span>
                        <x-money :amount="$t->fromPrice()" class="font-display text-[1.5rem] font-semibold"/>
                    </p>
                </div>
            </article>
        @endforeach
    </div>
</section>

{{-- ============ HOW PRE-ORDER WORKS ============ --}}
<section class="bg-espresso text-ivory" aria-labelledby="how-h">
    <div class="wrap grid gap-14 py-20 lg:grid-cols-[.9fr_1.1fr] lg:py-28">
        <div class="reveal">
            <p class="caps text-gold-light">Pre-order only</p>
            <h2 id="how-h" class="mt-4 text-4xl text-ivory sm:text-5xl">We don’t keep shelves of hair. <span class="italic text-gold-light">We order yours.</span></h2>
            <p class="mt-6 max-w-[48ch] text-ivory/80">Every order is placed for you once we have confirmed the texture, lengths and price together. Most orders land within 14 business days of being confirmed and shipped, and you can follow each step online.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('preorder') }}" class="btn btn-gold">Start a pre-order</a>
                <a href="{{ route('track') }}" class="btn btn-ghost-light">Track an order</a>
            </div>
        </div>
        <ol class="relative grid gap-5">
            @php $steps = [
                ['Request', 'Pick your texture and set on the site, or DM us. It takes about two minutes.'],
                ['Confirm', 'We message you on Instagram or WhatsApp to confirm lengths, price and payment.'],
                ['We order', 'Your hair is ordered for you. Your tracking page updates as it moves.'],
                ['Yours', 'It lands within 14 business days or less, ready for collection or delivery.'],
            ]; @endphp
            @foreach($steps as $i => [$h, $p])
                <li class="reveal flex gap-5 rounded-2xl border border-ivory/10 bg-espresso-2/70 p-5 sm:p-6" style="--d:{{ $i * .08 }}s">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-full border border-gold/50 font-display text-[1.3rem] text-gold-light">{{ $i + 1 }}</span>
                    <div>
                        <h3 class="text-[1.45rem] text-ivory">{{ $h }}</h3>
                        <p class="mt-1 text-ivory/75">{{ $p }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ============ WEAR DIARY ============ --}}
<section class="py-20 lg:py-28" aria-labelledby="diary-h">
    <div class="wrap">
        <div class="reveal flex flex-wrap items-end justify-between gap-6">
            <div class="max-w-[640px]">
                <p class="caps text-gold-text">The wear diary</p>
                <h2 id="diary-h" class="mt-4 text-4xl sm:text-5xl">Proof, not promises.</h2>
                <p class="mt-4 text-cocoa">Beyond the install, it’s about performance. These are real wear tests from our own feed, from day one to two years.</p>
            </div>
            <a href="{{ route('care') }}" class="link inline-flex min-h-11 items-center">How to care for your hair →</a>
        </div>
    </div>
    <div class="wrap mt-12">
        <ol class="track">
            @foreach($diary as $i => $d)
                <li class="reveal" style="--d:{{ $i * .07 }}s">
                    <article class="h-full">
                        <div class="relative overflow-hidden rounded-[1.4rem]">
                            <img src="{{ asset($d['image']) }}" alt="" width="420" height="560" loading="lazy" class="aspect-[3/4] w-full object-cover">
                            <span class="absolute left-3 top-3 rounded-full bg-ivory/95 px-3 py-1 font-display text-[1.05rem] font-semibold">{{ $d['when'] }}</span>
                        </div>
                        <h3 class="mt-5 text-[1.45rem]">{{ $d['title'] }}</h3>
                        <p class="mt-2 text-[1rem] text-cocoa">{{ $d['body'] }}</p>
                    </article>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ============ CLIP-IN SERVICE ============ --}}
<section class="wrap" aria-labelledby="clip-h">
    <div class="grid overflow-hidden rounded-[2rem] bg-cream lg:grid-cols-2">
        <div class="relative min-h-[320px]">
            <img src="{{ asset('assets/img/clip-ins-afro-coily.webp') }}" alt="A custom clip-in set sewn from Mongolian Afro Coily bundles" width="740" height="1310" loading="lazy" class="absolute inset-0 h-full w-full object-cover">
        </div>
        <div class="reveal p-8 sm:p-12 lg:p-16">
            <p class="caps text-gold-text">Custom clip-in service</p>
            <h2 id="clip-h" class="mt-4 text-4xl sm:text-5xl">Bundles sitting at home? <span class="script block text-[1.2em] text-gold-text">Repurpose them.</span></h2>
            <p class="mt-5 text-cocoa">Sometimes you don’t want a sew-in or a wig. Bring the bundles you already own and we machine-sew them into a reusable clip-in set: install, style, remove, repeat.</p>
            <ul class="mt-7 grid gap-3">
                @foreach(['About 10–13 pieces, depending on the hair you bring', 'Double or triple tracked for the fullness you want', 'Machine-sewn for strength, made for your styles'] as $line)
                    <li class="flex gap-3"><x-crown class="mt-1.5 shrink-0 text-gold"/><span>{{ $line }}</span></li>
                @endforeach
            </ul>
            <a href="{{ route('clipins') }}" class="btn btn-primary mt-9">Ask about clip-ins</a>
        </div>
    </div>
</section>

{{-- ============ QUEEN CAM ============ --}}
<section class="wrap py-20 lg:py-28" aria-labelledby="cam-h">
    <div class="reveal flex flex-wrap items-end justify-between gap-6">
        <div class="max-w-[640px]">
            <p class="caps text-gold-text">Queen Cam</p>
            <h2 id="cam-h" class="mt-4 text-4xl sm:text-5xl">Some textures don’t need taming. <span class="italic">They deserve celebrating.</span></h2>
        </div>
        <a href="{{ route('gallery') }}" class="btn btn-ghost">See every look</a>
    </div>
    <div class="mt-12 grid auto-rows-[180px] grid-cols-2 gap-3 sm:auto-rows-[220px] md:grid-cols-4 md:gap-4">
        @foreach($photos as $i => $p)
            <figure @class(['reveal relative overflow-hidden rounded-[1.25rem]', 'row-span-2' => in_array($i, [0, 3, 5]), 'md:col-span-2' => $i === 3]) style="--d:{{ $i * .05 }}s">
                <img src="{{ $p->url() }}" alt="{{ $p->alt }}" loading="lazy" width="600" height="760" class="h-full w-full object-cover transition duration-700 hover:scale-[1.03]">
                @if($p->label)<figcaption class="absolute bottom-3 left-3 rounded-full bg-espresso/80 px-3 py-1 text-[.8rem] font-semibold text-gold-light backdrop-blur">{{ $p->label }}</figcaption>@endif
            </figure>
        @endforeach
    </div>
    <p class="mt-8 text-center text-cocoa">Wearing Kinks &amp; Queens? Tag <a class="link" href="{{ \App\Models\Setting::instagramUrl() }}" rel="noopener">{{ '@'.\App\Models\Setting::get('instagram_handle') }}</a> for your Queen Cam.</p>
</section>

{{-- ============ MATCH + FAQ ============ --}}
<section class="wrap grid gap-8 lg:grid-cols-[1fr_1.1fr]" aria-labelledby="match-h">
    <div class="reveal relative overflow-hidden rounded-[2rem] bg-ink p-8 text-ivory sm:p-12">
        <div aria-hidden="true" class="watermark absolute -bottom-10 -right-4 text-[12rem] opacity-70">?</div>
        <p class="caps text-gold-light">Texture Match</p>
        <h2 id="match-h" class="mt-4 text-4xl text-ivory">Between two textures?</h2>
        <p class="mt-4 max-w-[40ch] text-ivory/80">Three quick questions about your own hair and the look you want. We will point you to a texture, a format and a starting set.</p>
        <x-coil type="coil" :width="220" dark class="mt-6"/>
        <a href="{{ route('match') }}" class="btn btn-gold mt-8">Take the quiz</a>
    </div>
    <div class="reveal" style="--d:.1s">
        <h2 class="text-3xl">Good to know</h2>
        <div class="mt-6 divide-y divide-sand border-y border-sand">
            @foreach(array_slice(config('kq.faqs'), 0, 4) as $f)
                <details class="group py-2">
                    <summary class="flex min-h-12 cursor-pointer list-none items-center justify-between gap-4 font-semibold">
                        {{ $f['q'] }}
                        <span class="text-gold-text transition group-open:rotate-45" aria-hidden="true">+</span>
                    </summary>
                    <p class="pb-3 text-cocoa">{{ $f['a'] }}</p>
                </details>
            @endforeach
        </div>
        <a href="{{ route('faq') }}" class="link mt-5 inline-flex min-h-11 items-center">All questions →</a>
    </div>
</section>
@endsection
