@extends('layouts.app', ['title' => 'Start a pre-order', 'description' => 'Request Kinks & Queens textured hair: choose your texture, format and set. We confirm everything with you before anything is ordered.'])

@section('content')
<x-page-hero title="Start a pre-order" script="your crown, your way" lead="Choose your texture and set. Nothing is charged here: we message you to confirm lengths, price and payment before anything is ordered." image="assets/img/kinky-curly-red.webp" alt="Woman wearing Brazilian Kinky Curly extensions" coil="curl"/>

<section class="wrap py-16 lg:py-20">
    @unless($accepting)
        <x-orders-closed/>
    @else
    <form method="POST" action="{{ route('preorder.store') }}" data-once x-data="preorder(@js($catalogue), @js($prefill))" class="grid gap-10 lg:grid-cols-[1fr_360px] lg:gap-14" novalidate>
        @csrf
        <input type="hidden" name="submission" value="{{ \Illuminate\Support\Str::uuid() }}">
        <div class="grid gap-12">
            <x-form-alert/>

            {{-- 1. Texture --}}
            <fieldset @error('texture_id') aria-describedby="e-texture" @enderror>
                <legend class="mb-4 flex items-baseline gap-3 font-display text-[1.7rem]"><span class="text-gold-text">1</span> Texture</legend>
                <div class="grid gap-3 sm:grid-cols-3">
                    @foreach($textures as $t)
                        <label class="tile !grid grid-cols-[4.5rem_1fr] !items-center gap-x-4 !p-3 sm:!flex sm:flex-col sm:!items-start">
                            <input type="radio" name="texture_id" value="{{ $t->id }}" x-model.number="texture" @checked((int) $prefill['texture'] === $t->id) required>
                            <img src="{{ asset($t->image) }}" alt="" width="300" height="200" loading="lazy" class="row-span-2 aspect-square w-full rounded-xl object-cover sm:aspect-[3/2]">
                            <span class="self-end font-semibold leading-snug sm:mt-1">{{ $t->name }}</span>
                            <span class="tile-sub self-start">For {{ lcfirst($t->best_for) }}</span>
                        </label>
                    @endforeach
                </div>
                @error('texture_id')<p id="e-texture" class="err mt-2">{{ $message }}</p>@enderror
                <p class="hint mt-3">Not sure? <a href="{{ route('match') }}" class="link">Take Texture Match</a>.</p>
            </fieldset>

            {{-- 2. Format --}}
            <fieldset @error('format') aria-describedby="e-format" @enderror>
                <legend class="mb-4 flex items-baseline gap-3 font-display text-[1.7rem]"><span class="text-gold-text">2</span> How you’ll wear it</legend>
                <div class="grid gap-3 sm:grid-cols-3">
                    @foreach(\App\Models\PreOrder::FORMATS as $value => $label)
                        <label class="tile">
                            <input type="radio" name="format" value="{{ $value }}" x-model="format" :disabled="!offers('{{ $value }}')" @checked($prefill['format'] === $value) required>
                            <span class="grid"><span class="font-semibold">{{ $label }}</span>
                                <span class="tile-sub" x-text="offers('{{ $value }}') ? '{{ $value === 'bundles' ? 'Priced from the list' : 'Quoted when we confirm' }}' : 'Not offered in this texture'">{{ $value === 'bundles' ? 'Priced from the list' : 'Quoted when we confirm' }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('format')<p id="e-format" class="err mt-2">{{ $message }}</p>@enderror
                <p class="hint mt-3">Have bundles already? <a href="{{ route('clipins') }}" class="link">We can turn them into clip-ins</a>.</p>
            </fieldset>

            {{-- 3. Set or length --}}
            <fieldset @error('price_set_id') aria-describedby="e-set" @enderror>
                <legend class="mb-4 flex items-baseline gap-3 font-display text-[1.7rem]"><span class="text-gold-text">3</span> <span x-text="format === 'bundles' || !format ? 'Choose your set' : 'Choose your length'">Choose your set</span></legend>

                <div x-show="format === 'bundles'" x-cloak>
                    <p class="hint mb-3" x-show="!t">Choose a texture first.</p>
                    <div class="mb-4 flex flex-wrap gap-2" role="group" aria-label="Number of bundles" x-show="t">
                        <template x-for="c in counts" :key="c">
                            <button type="button" class="min-h-11 rounded-full border px-4 font-semibold" :class="count === c ? 'border-ink bg-ink text-ivory' : 'border-sand bg-white/70'" @click="count = c" :aria-pressed="(count === c).toString()" x-text="`${c} bundles`"></button>
                        </template>
                    </div>
                    <div class="grid gap-2 sm:grid-cols-2">
                        <template x-for="s in visibleSets" :key="s.id">
                            <label class="tile justify-between">
                                <input type="radio" name="price_set_id" :value="s.id" x-model.number="set">
                                <span class="font-semibold" x-text="s.label"></span>
                                <span class="tile-sub font-semibold tabular-nums" x-text="money(s.price)"></span>
                            </label>
                        </template>
                    </div>
                    @error('price_set_id')<p id="e-set" class="err mt-2">{{ $message }}</p>@enderror
                </div>

                <noscript>
                    <label class="field">
                        <span>Bundle set (for bundles)</span>
                        <select name="price_set_id" class="input">
                            <option value="">Choose a set</option>
                            @foreach($textures as $t)
                                <optgroup label="{{ $t->name }}">
                                    @foreach($t->activePriceSets as $s)<option value="{{ $s->id }}" @selected((int) $prefill['set'] === $s->id)>{{ $s->label() }} · TT${{ number_format($s->price) }}</option>@endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </label>
                </noscript>

                <div x-show="format && format !== 'bundles'" class="grid gap-5" @if(! in_array($prefill['format'], ['ponytail', 'clip-ins'])) x-cloak @endif>
                    <label class="field max-w-[320px]">
                        <span>Length (for ponytails and clip-ins)</span>
                        <select name="length" class="input" x-model.number="length" @error('length') aria-invalid="true" aria-describedby="e-length" @enderror>
                            <option value="">Choose a length</option>
                            @foreach($lengths as $l)<option value="{{ $l }}" @selected((int) $prefill['length'] === $l)>{{ $l }}″</option>@endforeach
                        </select>
                        @error('length')<span id="e-length" class="err">{{ $message }}</span>@enderror
                    </label>
                    {{-- Only clip-ins have a fullness; disabled inputs are not sent with other formats. --}}
                    <fieldset x-show="format === 'clip-ins'" :disabled="format !== 'clip-ins'">
                        <legend class="label">Fullness</legend>
                        <div class="mt-2 grid gap-2 sm:grid-cols-2">
                            <label class="tile"><input type="radio" name="fullness" value="double" @checked(old('fullness', 'double') === 'double')><span class="grid"><span class="font-semibold">Double tracked</span><span class="tile-sub">Natural, everyday fullness</span></span></label>
                            <label class="tile"><input type="radio" name="fullness" value="triple" @checked(old('fullness') === 'triple')><span class="grid"><span class="font-semibold">Triple tracked</span><span class="tile-sub">Extra volume</span></span></label>
                        </div>
                    </fieldset>
                </div>
                <p class="hint mt-3" x-show="!format">Choose how you’ll wear it first.</p>
            </fieldset>

            <x-contact-fields step="4" legend="Your details"/>

            <div class="lg:hidden">
                {{-- Phone: the same summary as the desktop sidebar, right above the button. --}}
                <div class="mb-4 rounded-2xl bg-espresso p-5 text-ivory" x-show="summary" x-cloak aria-live="polite">
                    <p class="caps text-gold-light">Your request</p>
                    <template x-if="summary">
                        <div class="mt-2 flex items-end justify-between gap-4">
                            <div class="min-w-0">
                                <p class="font-display text-[1.3rem] leading-tight" x-text="summary.name"></p>
                                <p class="mt-1 text-[.95rem] text-ivory/80" x-text="[summary.format, summary.detail].filter(Boolean).join(' · ')"></p>
                            </div>
                            <p class="shrink-0 font-display text-[1.5rem] text-gold-light" x-text="summary.price"></p>
                        </div>
                    </template>
                </div>
                <button class="btn btn-primary w-full" type="submit">Send my pre-order request</button>
                <p class="hint mt-3 text-center">Nothing to pay now. We confirm with you first.</p>
            </div>
        </div>

        {{-- Summary --}}
        <aside class="hidden lg:block">
            <div class="sticky top-28 overflow-hidden rounded-[1.75rem] bg-espresso text-ivory">
                <div class="relative h-40 overflow-hidden">
                    <img :src="t ? t.image : '{{ asset('assets/img/kinky-curly-closeup.webp') }}'" src="{{ asset('assets/img/kinky-curly-closeup.webp') }}" alt="" class="h-full w-full object-cover opacity-80">
                    <div class="absolute inset-0 bg-gradient-to-t from-espresso to-transparent"></div>
                </div>
                <div class="p-6">
                    <p class="caps text-gold-light">Your request</p>
                    <template x-if="summary">
                        <div class="mt-3">
                            <p class="font-display text-[1.6rem] leading-tight" x-text="summary.name"></p>
                            <p class="mt-1 text-ivory/80" x-text="summary.format"></p>
                            <p class="mt-1 text-ivory/80" x-text="summary.detail"></p>
                            <p class="mt-4 border-t border-ivory/15 pt-4 font-display text-[2rem] text-gold-light" x-text="summary.price"></p>
                        </div>
                    </template>
                    <p class="mt-3 text-ivory/70" x-show="!summary">Choose a texture to begin.</p>
                    <button class="btn btn-gold mt-6 w-full" type="submit" :disabled="!ready" :class="!ready && 'opacity-60'">Send my pre-order request</button>
                    <ul class="mt-5 grid gap-2 text-[.92rem] text-ivory/75">
                        <li class="flex gap-2"><x-crown class="mt-1 shrink-0 text-gold"/> Nothing to pay now</li>
                        <li class="flex gap-2"><x-crown class="mt-1 shrink-0 text-gold"/> We confirm on Instagram or WhatsApp</li>
                        <li class="flex gap-2"><x-crown class="mt-1 shrink-0 text-gold"/> Arrives within 14 business days or less</li>
                    </ul>
                </div>
            </div>
        </aside>
    </form>
    @endunless
</section>
@endsection
