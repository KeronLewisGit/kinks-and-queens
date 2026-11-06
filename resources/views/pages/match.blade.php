@extends('layouts.app', ['title' => 'Texture Match', 'description' => 'Three quick questions to find the Kinks & Queens texture, format and set that will blend with your hair.'])

@section('content')
<x-page-hero title="Texture Match" script="three questions" lead="Your own hair decides the texture; the look you want helps us suggest a format and a starting set." image="assets/img/kinky-curly-fulani.webp" alt="Woman with Fulani cornrows and Brazilian Kinky Curly hair" coil="coil"/>

<section class="wrap py-16 lg:py-20" x-data="quiz(@js($quiz), @js($catalogue))">
    <div class="mx-auto max-w-[820px]">
        <div class="mb-8 flex items-center gap-3" aria-hidden="true">
            <template x-for="(q, n) in questions" :key="n">
                <span class="h-1.5 flex-1 rounded-full transition" :class="n < step ? 'bg-gold' : 'bg-sand'"></span>
            </template>
        </div>

        <div x-ref="panel" tabindex="-1" class="outline-none" aria-live="polite">
            @foreach($quiz as $n => $q)
                <fieldset x-show="step === {{ $n }}" @if($n > 0) x-cloak @endif class="reveal">
                    <legend class="mb-2 hint">Question {{ $n + 1 }} of {{ count($quiz) }}</legend>
                    <h2 class="text-4xl">{{ $q['q'] }}</h2>
                    <div class="mt-8 grid gap-3">
                        @foreach($q['options'] as $o)
                            <button type="button" class="tile w-full text-left" @click="choose('{{ $q['key'] }}', '{{ $o['value'] }}')">
                                <span class="grid"><span class="text-[1.1rem] font-semibold">{{ $o['label'] }}</span><span class="tile-sub">{{ $o['hint'] }}</span></span>
                            </button>
                        @endforeach
                    </div>
                    @if($n > 0)<button type="button" class="link mt-6 inline-flex min-h-11 items-center" @click="back()">← Back</button>@endif
                </fieldset>
            @endforeach

            <div x-show="step === questions.length" x-cloak>
                <template x-if="step === questions.length">
                    <div class="grid items-center gap-10 md:grid-cols-[.8fr_1fr]">
                        <div class="frame"><img :src="result.t.image" :alt="result.t.name" class="aspect-[4/5] w-full object-cover"></div>
                        <div>
                            <p class="caps text-gold-text">Your match</p>
                            <h2 class="mt-3 text-5xl" x-text="result.t.name"></h2>
                            <p class="mt-3 text-[1.1rem] text-cocoa" x-text="`Made for ${result.t.bestFor.toLowerCase()}, so it blends with the hair you already have.`"></p>
                            <p class="mt-3 text-cocoa" x-show="result.lookMismatch">You picked a look from a different texture. Your own hair matters most for a seamless blend, but you can absolutely style this texture toward that look, or ask us.</p>
                            <p class="mt-3 text-cocoa" x-show="result.note" x-text="result.note"></p>
                            <div class="mt-6 rounded-2xl border border-sand bg-white/70 p-5" x-show="result.format === 'bundles' && result.set">
                                <p class="hint">A good place to start</p>
                                <p class="mt-1 flex items-center justify-between font-semibold"><span x-text="result.set?.label"></span><span x-text="money(result.set?.price ?? 0)"></span></p>
                            </div>
                            <div class="mt-8 flex flex-wrap gap-3">
                                <a class="btn btn-primary" :href="result.format === 'clip-ins' && !result.t.formats.includes('clip-ins') ? '{{ route('clipins') }}' : `{{ route('preorder') }}?texture=${result.t.slug}&format=${result.format}` + (result.format === 'bundles' && result.set ? `&set=${result.set.id}` : '')">Pre-order this</a>
                                <a class="btn btn-ghost" :href="result.t.url">See every length</a>
                            </div>
                            <button type="button" class="link mt-6 inline-flex min-h-11 items-center" @click="restart()">Start again</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <noscript>
            <div class="card mt-10 p-6">
                <p class="font-semibold">Quick guide</p>
                <ul class="mt-3 grid gap-2 text-cocoa">
                    @foreach($textures as $t)<li><a class="link" href="{{ route('textures.show', $t) }}">{{ $t->name }}</a>: for {{ lcfirst($t->best_for) }}.</li>@endforeach
                </ul>
            </div>
        </noscript>
    </div>
</section>
@endsection
