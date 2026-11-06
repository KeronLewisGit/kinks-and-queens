@props(['title', 'current' => null, 'script' => null, 'lead' => null, 'image' => null, 'alt' => '', 'crumb' => null, 'coil' => 'curl'])
{{-- The one inner-page hero used by every page except the home page. --}}
<section class="relative overflow-hidden border-b border-sand/70 bg-cream/60">
    <div aria-hidden="true" class="watermark absolute -right-6 -top-10 hidden text-[22rem] lg:block">KQ</div>
    <div class="wrap relative grid items-center gap-10 py-12 sm:py-16 lg:grid-cols-[1.25fr_.75fr] lg:py-20">
        <div class="reveal">
            <nav aria-label="Breadcrumb" class="mb-6 text-[.92rem] text-mocha">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('home') }}" class="hover:text-ink">Home</a></li>
                    @if($crumb)
                        <li aria-hidden="true">/</li><li><a href="{{ $crumb[1] }}" class="hover:text-ink">{{ $crumb[0] }}</a></li>
                    @endif
                    <li aria-hidden="true">/</li><li aria-current="page" class="font-semibold text-ink">{{ $current ?? $title }}</li>
                </ol>
            </nav>
            <h1 class="text-[2.6rem] sm:text-6xl lg:text-[4.4rem]">
                {{ $title }}@if($script)<span class="script mt-1 block text-[1.25em] text-gold-text sm:-mt-2">{{ $script }}</span>@endif
            </h1>
            <x-coil :type="$coil" :width="200" class="mt-6"/>
            @if($lead)<p class="mt-6 max-w-[56ch] text-[1.15rem] text-cocoa">{{ $lead }}</p>@endif
            @if(trim($slot))<div class="mt-8 flex flex-wrap gap-3">{{ $slot }}</div>@endif
        </div>
        @if($image)
            <div class="reveal mx-auto w-full max-w-[360px] lg:max-w-none" style="--d:.12s">
                <div class="frame">
                    <img src="{{ asset($image) }}" alt="{{ $alt }}" width="720" height="860" class="aspect-[5/6] w-full object-cover" fetchpriority="high">
                </div>
            </div>
        @endif
    </div>
</section>
