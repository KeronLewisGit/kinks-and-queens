@extends('layouts.app', ['title' => 'FAQ', 'description' => 'Pre-order times, bundle weights, colouring, clip-ins and more: answers from Kinks & Queens.'])

@push('head')
<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => collect($faqs)->map(fn ($f) => ['@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']]])->all()], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<x-page-hero title="Questions" script="answered" lead="Everything about pre-orders, textures, bundles and clip-ins. Still unsure? Send us a DM." image="assets/img/kinky-curly-bleached.webp" alt="Lightly bleached Brazilian Kinky Curly, back view" coil="curl"/>

<section class="wrap grid gap-12 py-16 lg:grid-cols-[1fr_320px]">
    <div class="divide-y divide-sand border-y border-sand">
        @foreach($faqs as $f)
            <details class="group py-2" @if($loop->first) open @endif>
                <summary class="flex min-h-14 cursor-pointer list-none items-center justify-between gap-4 font-display text-[1.35rem]">
                    {{ $f['q'] }}
                    <span class="text-[1.4rem] text-gold-text transition group-open:rotate-45" aria-hidden="true">+</span>
                </summary>
                <p class="prose-kq pb-5 text-[1.05rem] text-cocoa">{{ $f['a'] }}</p>
            </details>
        @endforeach
    </div>
    <aside class="card h-fit p-6">
        <h2 class="text-2xl">Still have a question?</h2>
        <p class="mt-2 text-cocoa">We answer DMs personally.</p>
        <a href="{{ \App\Models\Setting::instagramDm() }}" class="btn btn-primary mt-5 w-full" rel="noopener">DM on Instagram</a>
        @if($wa = \App\Models\Setting::whatsappLink())<a href="{{ $wa }}" class="btn btn-ghost mt-3 w-full" rel="noopener">WhatsApp us</a>@endif
    </aside>
</section>
@endsection
