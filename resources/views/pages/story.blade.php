@extends('layouts.app', ['title' => 'Our story', 'description' => 'Kinks & Queens specialises exclusively in premium textured hair extensions, because kinky, curly, coily and textured straight hair deserve to be celebrated.'])

@section('content')
<x-page-hero title="Our story" script="where texture reigns" lead="Not every queen wants to change her texture. Some of us simply want to enhance what we already love." image="assets/img/kinky-curly-portrait.webp" alt="Woman wearing Brazilian Kinky Curly with a centre part" coil="curl"/>

<section class="wrap grid gap-14 py-20 lg:grid-cols-[1.1fr_.9fr]">
    <div class="reveal prose-kq text-[1.12rem] text-cocoa">
        <p class="font-display text-[1.8rem] leading-snug text-ink">Kinks &amp; Queens specialises exclusively in premium textured hair extensions, because kinky, curly, coily and textured straight hair deserve to be celebrated too.</p>
        <p class="mt-6">{{ config('kq.promise') }}</p>
        <p>It has never been about natural versus relaxed. It’s about celebrating women wherever they are in their hair journey. Whether you wear your hair natural, silk-pressed or relaxed, choosing a texture that suits your lifestyle doesn’t make you any less authentic.</p>
        <p>That’s why every order is a pre-order. We don’t stock shelves of hair in the hope it suits someone. We help you find the right texture first, then order it for you.</p>
    </div>
    <div class="reveal grid content-start gap-4">
        @foreach(['More texture.', 'More versatility.', 'More confidence.'] as $i => $line)
            <p class="rounded-2xl bg-ink px-6 py-5 font-display text-[2rem] text-ivory" style="margin-left: {{ $i * 1.5 }}rem">{{ $line }}</p>
        @endforeach
    </div>
</section>

<section class="wrap" aria-label="Made for every texture">
    <div class="grid gap-5 sm:grid-cols-3">
        @foreach($textures as $t)
            <a href="{{ route('textures.show', $t) }}" class="reveal card p-6 hover:border-gold">
                <span class="flex h-9 items-center"><x-coil :type="$t->coil" :width="160"/></span>
                <p class="mt-3 font-display text-[1.6rem]">{{ $t->pattern }}</p>
                <p class="text-cocoa">For {{ lcfirst($t->best_for) }}</p>
            </a>
        @endforeach
    </div>
</section>
@endsection
