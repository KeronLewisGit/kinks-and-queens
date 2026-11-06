@extends('layouts.app', ['title' => 'Textures & prices', 'description' => 'Brazilian Kinky Curly, Mongolian Afro Coily and Brazilian Textured Straight: 100% human hair bundles, ponytails and clip-ins. Full TT$ price lists.'])

@section('content')
<x-page-hero title="Our textures" script="made to blend" lead="Three textures, each made for a different crown. Choose the one closest to your own hair and it will blend like it grew there." image="assets/img/kinky-curly-half-up.webp" alt="Client wearing Brazilian Kinky Curly half-up, half-down" coil="curl">
    <a href="{{ route('match') }}" class="btn btn-primary">Not sure? Take Texture Match</a>
</x-page-hero>

<section class="wrap py-20 lg:py-24">
    <div class="grid gap-16">
        @foreach($textures as $i => $t)
            <article @class(['reveal grid items-center gap-10 lg:grid-cols-2 lg:gap-16']) aria-labelledby="t-{{ $t->id }}">
                <div @class(['frame mx-auto w-full max-w-[520px]', 'lg:order-2' => $i % 2 === 1])>
                    <img src="{{ asset($t->image) }}" alt="{{ $t->image_alt }}" width="640" height="760" loading="lazy" class="aspect-[5/6] w-full object-cover" style="view-transition-name: tex-{{ $t->slug }}">
                </div>
                <div>
                    <p class="caps text-gold-text">{{ $t->origin }} · for {{ lcfirst($t->best_for) }}</p>
                    <h2 id="t-{{ $t->id }}" class="mt-3 text-5xl">{{ $t->pattern }}</h2>
                    <x-coil :type="$t->coil" :width="220" class="mt-4"/>
                    <p class="mt-5 max-w-[56ch] text-[1.1rem] text-cocoa">{{ $t->description }}</p>
                    <p class="mt-5 flex flex-wrap gap-2">@foreach($t->formats as $f)<span class="chip">{{ \App\Models\PreOrder::FORMATS[$f] }}</span>@endforeach <span class="chip">{{ $t->bundle_weight }} per bundle</span></p>
                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <a href="{{ route('textures.show', $t) }}" class="btn btn-primary">Lengths &amp; prices</a>
                        <span class="text-cocoa">Sets from <x-money :amount="$t->fromPrice()" class="font-display text-[1.4rem] font-semibold text-ink"/></span>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>
@endsection
