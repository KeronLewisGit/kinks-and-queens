@extends('layouts.app', ['title' => 'Queen Cam', 'description' => 'Real Kinks & Queens installs, client cams and texture shots: Kinky Curly, Afro Coily and Textured Straight.'])

@section('content')
<x-page-hero title="Queen Cam" script="real hair, real queens" lead="Client cams, wear tests and texture shots from our feed. No filters on the hair, just texture doing what it does best." image="assets/img/afro-coily-queen-cam.webp" alt="Woman on a lantern-lit terrace wearing a Mongolian Afro Coily puff" coil="coil"/>

<section class="wrap py-16" x-data="lightbox()" @keydown.escape.window="open && close()">
    <nav aria-label="Filter by texture" class="flex flex-wrap gap-2">
        <a href="{{ route('gallery') }}" @class(['chip min-h-11 !px-4', '!border-ink !bg-ink !text-ivory' => ! $active]) @if(! $active) aria-current="page" @endif>All</a>
        @foreach($textures as $t)
            <a href="{{ route('gallery', ['texture' => $t->slug]) }}" @class(['chip min-h-11 !px-4', '!border-ink !bg-ink !text-ivory' => $active?->is($t)]) @if($active?->is($t)) aria-current="page" @endif>{{ $t->pattern }}</a>
        @endforeach
    </nav>

    <ul class="mosaic wide mt-10">
        @forelse($photos as $p)
            <li class="reveal">
                <button type="button" class="group block w-full overflow-hidden rounded-[1.25rem] text-left" @click="show($el)" data-full="{{ $p->url() }}" data-alt="{{ $p->alt }}" data-caption="{{ trim(($p->label ? $p->label.' · ' : '').($p->caption ?? '')) }}" aria-label="Open photo: {{ $p->alt }}">
                    <img src="{{ $p->url() }}" alt="{{ $p->alt }}" loading="lazy" width="600" height="800" class="w-full transition duration-700 group-hover:scale-[1.03]">
                </button>
                <p class="mt-2 flex flex-wrap items-center gap-2 text-[.95rem] text-cocoa">
                    @if($p->label)<span class="chip !py-0.5">{{ $p->label }}</span>@endif
                    {{ $p->caption }}
                </p>
            </li>
        @empty
            <li class="text-cocoa">No photos for this texture yet.</li>
        @endforelse
    </ul>

    <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-espresso/90 p-4" role="dialog" aria-modal="true" aria-label="Photo" @click.self="close()" x-trap.noscroll="open">
        <figure class="max-h-full max-w-[min(92vw,720px)]">
            <img :src="src" :alt="alt" class="max-h-[80vh] w-auto rounded-2xl object-contain">
            <figcaption class="mt-3 text-center text-ivory/85" x-text="caption"></figcaption>
        </figure>
        <button type="button" x-ref="close" class="absolute right-4 top-4 inline-flex size-12 items-center justify-center rounded-full bg-ivory text-ink" @click="close()" aria-label="Close photo">✕</button>
    </div>

    <div class="card mt-16 flex flex-col items-center gap-4 p-8 text-center">
        <h2 class="text-3xl">Wearing Kinks &amp; Queens?</h2>
        <p class="max-w-[48ch] text-cocoa">Tag {{ '@'.\App\Models\Setting::get('instagram_handle') }} in your install and you could be our next Queen Cam.</p>
        <a href="{{ \App\Models\Setting::instagramUrl() }}" class="btn btn-primary" rel="noopener">Follow on Instagram</a>
    </div>
</section>
@endsection
