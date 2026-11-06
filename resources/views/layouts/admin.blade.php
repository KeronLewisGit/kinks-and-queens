@php
    $links = [
        ['Today', route('admin.dashboard'), 'admin.dashboard'],
        ['Requests', route('admin.orders.index'), 'admin.orders.*'],
        ['Price lists', route('admin.prices.index'), 'admin.prices.*'],
        ['Photos', route('admin.photos.index'), 'admin.photos.*'],
    ];
    if (auth()->user()->isOwner()) {
        $links[] = ['Settings', route('admin.settings'), 'admin.settings*'];
        $links[] = ['Team', route('admin.team'), 'admin.team*'];
    }
    $newCount = \App\Models\PreOrder::where('status', 'new')->count();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Admin' }} · Kinks &amp; Queens admin</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @include('partials.transition-guard')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f4ede3]" x-data="{ nav: false }">
    @include('partials.gold-gradient')
    <div class="lg:grid lg:grid-cols-[250px_1fr]">
        <aside class="bg-espresso text-ivory lg:sticky lg:top-0 lg:h-screen">
            <div class="flex items-center justify-between p-5">
                <a href="{{ route('admin.dashboard') }}"><x-logo dark compact/><span class="sr-only">Admin home</span></a>
                <button class="inline-flex size-11 items-center justify-center rounded-full border border-ivory/20 lg:hidden" @click="nav = !nav" :aria-expanded="nav.toString()" aria-controls="adm-nav" aria-label="Menu">☰</button>
            </div>
            <nav id="adm-nav" class="hidden px-3 pb-5 lg:block" :class="nav && '!block'" aria-label="Admin">
                <ul class="grid gap-1">
                    @foreach($links as [$label, $href, $pattern])
                        <li><a href="{{ $href }}" @class(['flex min-h-11 items-center justify-between rounded-xl px-4 font-medium', 'bg-ivory/10 text-gold-light' => request()->routeIs($pattern), 'text-ivory/80 hover:bg-ivory/5' => ! request()->routeIs($pattern)])>
                            {{ $label }}
                            @if($label === 'Requests' && $newCount)<span class="rounded-full bg-gold px-2 text-[.8rem] font-bold text-espresso">{{ $newCount }}</span>@endif
                        </a></li>
                    @endforeach
                </ul>
                <div class="mt-8 border-t border-ivory/10 px-4 pt-5 text-[.9rem] text-ivory/60">
                    <p>{{ auth()->user()->name }} · {{ ucfirst(auth()->user()->role) }}</p>
                    <a href="{{ route('home') }}" class="mt-2 inline-flex min-h-11 items-center hover:text-ivory" target="_blank" rel="noopener">View website ↗</a>
                    <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="inline-flex min-h-11 items-center hover:text-ivory">Sign out</button></form>
                </div>
            </nav>
        </aside>
        <main class="min-w-0 p-5 sm:p-8 lg:p-10">
            @if(session('ok'))<p class="mb-6 rounded-xl border border-gold/40 bg-white px-4 py-3 font-medium" role="status">{{ session('ok') }}</p>@endif
            @if($errors->any())<div class="mb-6 rounded-xl border border-rose/40 bg-white px-4 py-3 text-rose" role="alert">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>@endif
            @yield('content')
        </main>
    </div>
</body>
</html>
