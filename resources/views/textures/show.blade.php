@extends('layouts.app', ['title' => $texture->name, 'description' => $texture->name.' for '.lcfirst($texture->best_for).'. '.$texture->tagline.' 100% human hair, pre-order price list in TT$.', 'ogImage' => $texture->image])

@php
    $uniform3 = $texture->activePriceSets->filter(fn ($s) => $s->bundle_count === 3 && $s->isUniform())->map(fn ($s) => $s->longest())->sort()->values();
    $rulerLengths = $uniform3->isNotEmpty() ? $uniform3 : $texture->activePriceSets->map(fn ($s) => $s->longest())->unique()->sort()->values();
    $start = $rulerLengths->contains(18) ? 18 : $rulerLengths->first();
    $cat = $catalogue[0];
    // Same shape the length ruler script draws, so the figure shows hair without JavaScript too.
    $end = 172; $half = min(64, 36 + ($end - 80) * 0.14); $l = 100 - $half; $r = 100 + $half; $w = ($r - $l) / 7; $edge = '';
    for ($k = 0; $k < 7; $k++) { $x1 = $r - $k * $w; $x2 = $r - ($k + 1) * $w; $edge .= sprintf(' Q %.1f %.1f %.1f %.1f', ($x1 + $x2) / 2, $end + 9, $x2, $end); }
    $hairPath = sprintf('M70 46 C74 14 126 14 130 46 C%.1f 70 %.1f %.1f %.1f %.1f%s C%.1f %.1f %.1f 70 70 46 Z', $r + 2, $r + 4, $end - 40, $r, $end, $edge, $l - 4, $end - 40, $l - 2);
@endphp

@section('content')
<x-page-hero :morph="'tex-'.$texture->slug" :title="$texture->lead()" :current="$texture->pattern" :script="$texture->script_word" :lead="'For '.lcfirst($texture->best_for).'. '.$texture->tagline" :image="$texture->image" :alt="$texture->image_alt" :crumb="['Textures', route('textures.index')]" :coil="$texture->coil">
    <a href="{{ route('preorder', ['texture' => $texture->slug]) }}" class="btn btn-primary">Pre-order {{ $texture->pattern }}</a>
    <a href="#prices" class="btn btn-ghost">See the price list</a>
</x-page-hero>

{{-- About the texture --}}
<section class="wrap grid gap-12 py-20 lg:grid-cols-[1.1fr_.9fr] lg:py-24">
    <div class="reveal">
        <h2 class="text-4xl">About {{ $texture->name }}</h2>
        <div class="prose-kq mt-6 text-[1.1rem] text-cocoa"><p>{{ $texture->description }}</p></div>
        <ul class="mt-8 grid gap-3 sm:grid-cols-2">
            @foreach($texture->highlights ?? [] as $h)
                <li class="flex gap-3 rounded-2xl border border-sand bg-white/60 p-4"><x-crown class="mt-1 shrink-0 text-gold"/><span class="font-medium">{{ $h }}</span></li>
            @endforeach
        </ul>
        <dl class="mt-8 grid grid-cols-2 gap-6 border-t border-sand pt-6 sm:grid-cols-3">
            <div><dt class="hint">Bundle weight</dt><dd class="mt-1 font-display text-[1.4rem]">{{ $texture->bundle_weight }}</dd></div>
            <div><dt class="hint">Hair</dt><dd class="mt-1 font-display text-[1.4rem]">100% human</dd></div>
            <div><dt class="hint">Available as</dt><dd class="mt-1 font-semibold">{{ collect($texture->formats)->map(fn ($f) => \App\Models\PreOrder::FORMATS[$f])->join(', ') }}</dd></div>
        </dl>
    </div>

    {{-- Length ruler --}}
    <div class="reveal" style="--d:.1s" x-data="lengthRuler(@js($cat['sets']), @js($rulerLengths), {{ $start }})">
        <div class="card p-6 sm:p-8">
            <h2 class="text-3xl">Length guide</h2>
            <p class="mt-2 text-cocoa">See roughly where each length falls, and what a set costs.</p>
            <div class="mt-6 grid grid-cols-[1fr_auto] items-stretch gap-6">
                <svg viewBox="0 0 200 400" class="h-[340px] w-full" role="img" :aria-label="`${len} inch hair falls to about ${where}`" aria-label="{{ $start }} inch hair falls to about below the collarbone">
                    <path d="M88 90v16c-18 2-44 6-52 22-6 14-6 42-4 72l8 60c2 40-4 80-6 140h132c-2-60-8-100-6-140l8-60c2-30 2-58-4-72-8-16-34-20-52-22V90z" fill="#e9d8c3"/>
                    <path class="ruler-hair" :d="hairPath" d="{{ $hairPath }}" fill="#2b1a11"/>
                    <ellipse cx="100" cy="60" rx="25" ry="31" fill="#a8714f"/>
                    <path d="M74 56c2-24 14-34 26-34s24 10 26 34c-7-11-16-16-26-16s-19 5-26 16z" fill="#2b1a11"/>
                    <g class="ruler-marker" :style="`transform: translateY(${y * 4}px)`" style="transform: translateY(172px)">
                        <line x1="6" x2="194" y1="0" y2="0" stroke="#845d1d" stroke-width="1.5" stroke-dasharray="5 4"/>
                    </g>
                </svg>
                <div class="flex flex-col items-center justify-between gap-3">
                    <button type="button" class="inline-flex size-12 items-center justify-center rounded-full border border-ink/20 text-xl" @click="step(-1)" aria-label="Shorter">−</button>
                    <p class="text-center"><span class="font-display text-[3rem] font-semibold leading-none" x-text="len + '″'">{{ $start }}″</span><span class="hint mt-1 block">inches</span></p>
                    <button type="button" class="inline-flex size-12 items-center justify-center rounded-full border border-ink/20 text-xl" @click="step(1)" aria-label="Longer">+</button>
                </div>
            </div>
            <label class="mt-6 block">
                <span class="sr-only">Length in inches</span>
                <input type="range" class="h-11 w-full cursor-pointer accent-[#845d1d]" :min="0" :max="lengths.length - 1" :value="lengths.indexOf(len)" @input="len = lengths[$event.target.value]" min="0" max="{{ $rulerLengths->count() - 1 }}" value="{{ $rulerLengths->search($start) }}" aria-label="Length">
            </label>
            <p class="mt-3 text-cocoa" aria-live="polite">At <strong x-text="len + '″'">{{ $start }}″</strong>, hair falls to about <strong x-text="where"></strong>.</p>
            <div class="mt-5 grid gap-2">
                <template x-if="three"><a :href="`{{ route('preorder', ['texture' => $texture->slug]) }}&set=${three.id}`" class="flex min-h-12 items-center justify-between rounded-xl bg-ink px-4 text-ivory"><span x-text="three.label"></span><span class="font-semibold" x-text="money(three.price)"></span></a></template>
                <template x-if="four"><a :href="`{{ route('preorder', ['texture' => $texture->slug]) }}&set=${four.id}`" class="flex min-h-12 items-center justify-between rounded-xl border border-sand bg-white px-4"><span x-text="four.label"></span><span class="font-semibold" x-text="money(four.price)"></span></a></template>
                <template x-if="two"><a :href="`{{ route('preorder', ['texture' => $texture->slug]) }}&set=${two.id}`" class="flex min-h-12 items-center justify-between rounded-xl border border-sand bg-white px-4"><span x-text="two.label"></span><span class="font-semibold" x-text="money(two.price)"></span></a></template>
            </div>
            <p class="hint mt-4">Lengths are measured stretched. Curly and coily textures sit shorter than their stretched length, so go one length up if in doubt.</p>
        </div>
    </div>
</section>

{{-- Price list --}}
<section id="prices" class="scroll-mt-28 bg-cream/60 py-20 lg:py-24" aria-labelledby="prices-h">
    <div class="wrap">
        <div class="reveal flex flex-wrap items-end justify-between gap-6">
            <div>
                <p class="caps text-gold-text">Price list · TT$</p>
                <h2 id="prices-h" class="mt-3 text-4xl sm:text-5xl">{{ $texture->lead() }} <span class="script text-[1.15em] text-gold-text">{{ $texture->script_word }}</span></h2>
                <p class="mt-3 text-cocoa">{{ $texture->bundle_weight }} per bundle in each set. Tap a set to pre-order it.</p>
            </div>
            @if($mto = $texture->madeToOrderLabel())
                <p class="max-w-[34ch] rounded-2xl border border-sand bg-white/70 p-4 text-[.98rem] text-cocoa">{{ $mto }} are made to order and quoted when we confirm.</p>
            @endif
        </div>
        <div class="mt-12 gap-6 md:columns-2 xl:columns-3">
            @foreach($groups as $group)
                <div class="reveal card mb-6 break-inside-avoid bg-ivory p-6">
                    <h3 class="flex items-start gap-2 text-[1.35rem] sm:text-[1.5rem]"><x-crown class="mt-[.3em] shrink-0 text-gold"/> {{ $group['title'] }}</h3>
                    <ul class="mt-4 divide-y divide-sand">
                        @foreach($group['sets'] as $set)
                            <li>
                                <a href="{{ route('preorder', ['texture' => $texture->slug, 'format' => 'bundles', 'set' => $set->id]) }}" class="group flex min-h-12 items-center justify-between gap-4 py-2 hover:text-gold-text">
                                    <span class="font-medium">{{ $set->label() }}</span>
                                    <span class="flex items-center gap-3"><x-money :amount="$set->price" class="font-semibold tabular-nums"/><span aria-hidden="true" class="text-gold opacity-0 transition group-hover:opacity-100">→</span></span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</section>

@if($texture->photos->isNotEmpty())
<section class="wrap py-20" aria-labelledby="worn-h">
    <h2 id="worn-h" class="reveal text-4xl">Worn by our queens</h2>
    <div class="mt-10 grid grid-cols-2 gap-4 md:grid-cols-4">
        @foreach($texture->photos->take(4) as $p)
            <figure class="reveal">
                <img src="{{ $p->url() }}" alt="{{ $p->alt }}" width="400" height="520" loading="lazy" class="aspect-[4/5] w-full rounded-[1.25rem] object-cover">
                @if($p->caption)<figcaption class="mt-2 text-[.95rem] text-cocoa">{{ $p->caption }}</figcaption>@endif
            </figure>
        @endforeach
    </div>
</section>
@endif

<section class="wrap" aria-labelledby="other-h">
    <h2 id="other-h" class="reveal text-3xl">Other textures</h2>
    <div class="mt-8 grid gap-5 sm:grid-cols-2">
        @foreach($others as $o)
            <a href="{{ route('textures.show', $o) }}" class="reveal card group flex items-center gap-5 p-4 hover:border-gold">
                <img src="{{ asset($o->image) }}" alt="" width="120" height="140" loading="lazy" class="h-28 w-24 rounded-xl object-cover">
                <span class="grid">
                    <span class="font-display text-[1.3rem] leading-tight sm:text-[1.5rem]">{{ $o->name }}</span>
                    <span class="text-cocoa">For {{ lcfirst($o->best_for) }}</span>
                    <span class="mt-1 font-semibold">From <x-money :amount="$o->fromPrice()"/></span>
                </span>
            </a>
        @endforeach
    </div>
</section>
@endsection
