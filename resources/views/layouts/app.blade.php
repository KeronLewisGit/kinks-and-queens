@php
    use App\Models\Setting;
    $title = trim(($title ?? '') ? $title.' · Kinks & Queens' : 'Kinks & Queens · Premium textured hair extensions in Trinidad & Tobago');
    $description = $description ?? 'Premium 100% human textured hair: Brazilian Kinky Curly, Mongolian Afro Coily and Brazilian Textured Straight bundles, ponytails and custom clip-ins. Pre-order in Trinidad & Tobago.';
    $ogImage = asset($ogImage ?? 'assets/img/kinky-curly-portrait.webp');
    $ig = Setting::instagramUrl();
    $nav = [
        ['Textures', route('textures.index'), 'textures*'],
        ['Clip-ins', route('clipins'), 'clip-ins'],
        ['Queen Cam', route('gallery'), 'queen-cam'],
        ['Care', route('care'), 'care'],
        ['FAQ', route('faq'), 'faq'],
    ];
@endphp
<!DOCTYPE html>
<html lang="en-TT">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="theme-color" content="#f8f2e9">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Kinks & Queens">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    {{-- Decide on the splash before first paint: once per session, never with reduced motion. --}}
    <script>
        (function () {
            try {
                var d = document.documentElement;
                if (sessionStorage.getItem('kq-splash') || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                sessionStorage.setItem('kq-splash', '1');
                d.classList.add('is-splash');
                var end = function () { d.classList.remove('is-splash'); };
                setTimeout(end, 2700);
                addEventListener('pointerdown', function skip(e) {
                    if (e.target.closest && e.target.closest('.splash')) { end(); removeEventListener('pointerdown', skip); }
                });
            } catch (e) {}
        })();
    </script>
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Store',
        'name' => 'Kinks & Queens',
        'slogan' => 'Where Texture Reigns',
        'description' => 'Premium textured hair extensions: bundles, ponytails and custom clip-ins. Pre-order only.',
        'url' => route('home'),
        'image' => asset('assets/img/kinky-curly-portrait.webp'),
        'areaServed' => 'Trinidad and Tobago',
        'priceRange' => 'TT$1,680–TT$4,745',
        'sameAs' => [$ig, 'https://www.tiktok.com/@'.Setting::get('tiktok_handle')],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
</head>
<body class="min-h-screen" x-data="{ menu: false }" @keydown.escape.window="menu = false">
    <svg width="0" height="0" class="absolute" aria-hidden="true" focusable="false">
        <defs>
            <linearGradient id="kq-gold" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0" stop-color="#e6c77f"/><stop offset=".55" stop-color="#b58a3c"/><stop offset="1" stop-color="#8c6421"/>
            </linearGradient>
        </defs>
    </svg>
    @include('partials.splash')
    <a href="#main" class="fixed left-4 top-3 z-[60] -translate-y-24 rounded-full bg-ink px-5 py-3 font-semibold text-ivory focus:translate-y-0">Skip to content</a>

    @if($note = Setting::get('announcement'))
        <p class="announce bg-espresso px-4 py-2 text-center text-[.85rem] font-medium text-gold-light">
            <x-crown class="mr-1.5 -mt-0.5 text-gold"/>{{ $note }}
        </p>
    @endif

    <header class="site-header sticky top-0 z-40 border-b border-transparent">
        <div class="wrap flex h-[76px] items-center justify-between gap-6">
            <a href="{{ route('home') }}" aria-label="Kinks & Queens home" class="shrink-0"><x-logo/></a>
            <nav aria-label="Main" class="hidden lg:block">
                <ul class="flex items-center gap-7">
                    @foreach($nav as [$label, $href, $pattern])
                        <li><a href="{{ $href }}" class="nav-link" @if(request()->is($pattern)) aria-current="page" @endif>{{ $label }}</a></li>
                    @endforeach
                </ul>
            </nav>
            <div class="flex items-center gap-2">
                <a href="{{ route('track') }}" class="hidden min-h-11 items-center px-3 text-[.95rem] font-medium text-ink/85 hover:text-ink sm:inline-flex">Track order</a>
                <a href="{{ route('preorder') }}" class="btn btn-primary hidden !min-h-11 !px-5 sm:inline-flex">Pre-order</a>
                <button type="button" class="inline-flex size-12 items-center justify-center rounded-full border border-ink/15 lg:hidden" @click="menu = true" aria-label="Open menu" :aria-expanded="menu.toString()" aria-controls="mobile-menu">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
                </button>
            </div>
        </div>
    </header>

    {{-- Phone menu --}}
    <div id="mobile-menu" x-cloak x-show="menu" class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true" aria-label="Menu">
        <div class="absolute inset-0 bg-espresso/60" @click="menu = false" x-show="menu" x-transition.opacity></div>
        <div class="absolute inset-y-0 right-0 flex w-[min(88vw,380px)] flex-col overflow-y-auto bg-ivory px-6 pb-8 pt-5" x-show="menu"
             x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-x-full" x-transition:leave="transition duration-200" x-transition:leave-end="translate-x-full"
             x-trap.noscroll="menu">
            <div class="flex items-center justify-between">
                <x-logo compact/>
                <button type="button" class="inline-flex size-12 items-center justify-center rounded-full border border-ink/15" @click="menu = false" aria-label="Close menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                </button>
            </div>
            <ul class="mt-8 grid gap-1 font-display text-[1.7rem]">
                <li><a href="{{ route('home') }}" class="block py-2">Home</a></li>
                @foreach($nav as [$label, $href])
                    <li><a href="{{ $href }}" class="block py-2">{{ $label }}</a></li>
                @endforeach
                <li><a href="{{ route('match') }}" class="block py-2">Texture Match</a></li>
                <li><a href="{{ route('track') }}" class="block py-2">Track my order</a></li>
            </ul>
            <a href="{{ route('preorder') }}" class="btn btn-primary mt-8">Start a pre-order</a>
            <a href="{{ Setting::instagramDm() }}" class="btn btn-ghost mt-3" rel="noopener">DM us on Instagram</a>
        </div>
    </div>

    <main id="main" tabindex="-1" @class(['outline-none', 'pb-24' => ! ($flushFooter ?? false)])>
        @yield('content')
    </main>

    <footer class="relative bg-espresso text-ivory/85">
        <div class="footer-rule" aria-hidden="true"></div>
        <div class="absolute inset-0 overflow-hidden" aria-hidden="true"><div class="watermark absolute -bottom-16 right-0 text-[18rem] opacity-60">KQ</div></div>
        <div class="wrap relative grid gap-12 pb-12 pt-16 lg:grid-cols-[1.3fr_1fr_1fr_1fr] lg:pt-20">
            <div>
                <x-logo dark/>
                <p class="mt-8 font-display text-[2rem] italic leading-tight text-ivory">More than hair,<br><span class="script not-italic text-[1.6em] text-gold-light">it’s confidence.</span></p>
                <p class="mt-5 max-w-[38ch] text-[.98rem] text-ivory/75">Empowering you to love the texture you were born with. Premium textured extensions, made to order for queens in Trinidad &amp; Tobago.</p>
            </div>
            <div>
                <h2 class="caps text-gold-light">Shop</h2>
                <ul class="mt-4 grid gap-1">
                    @foreach(\App\Models\Texture::active()->get() as $t)
                        <li><a class="inline-flex min-h-11 items-center hover:text-gold-light" href="{{ route('textures.show', $t) }}">{{ $t->name }}</a></li>
                    @endforeach
                    <li><a class="inline-flex min-h-11 items-center hover:text-gold-light" href="{{ route('clipins') }}">Custom clip-ins</a></li>
                </ul>
            </div>
            <div>
                <h2 class="caps text-gold-light">Help</h2>
                <ul class="mt-4 grid gap-1">
                    <li><a class="inline-flex min-h-11 items-center hover:text-gold-light" href="{{ route('match') }}">Texture Match quiz</a></li>
                    <li><a class="inline-flex min-h-11 items-center hover:text-gold-light" href="{{ route('track') }}">Track my order</a></li>
                    <li><a class="inline-flex min-h-11 items-center hover:text-gold-light" href="{{ route('care') }}">Care guide</a></li>
                    <li><a class="inline-flex min-h-11 items-center hover:text-gold-light" href="{{ route('faq') }}">FAQ</a></li>
                    <li><a class="inline-flex min-h-11 items-center hover:text-gold-light" href="{{ route('story') }}">Our story</a></li>
                </ul>
            </div>
            <div>
                <h2 class="caps text-gold-light">Talk to us</h2>
                <ul class="mt-4 grid gap-1">
                    <li><a class="inline-flex min-h-11 items-center hover:text-gold-light" href="{{ Setting::instagramDm() }}" rel="noopener">DM on Instagram</a></li>
                    @if($wa = Setting::whatsappLink())
                        <li><a class="inline-flex min-h-11 items-center hover:text-gold-light" href="{{ $wa }}" rel="noopener">WhatsApp</a></li>
                    @endif
                    <li><a class="inline-flex min-h-11 items-center hover:text-gold-light" href="{{ $ig }}" rel="noopener">{{ '@'.Setting::get('instagram_handle') }}</a></li>
                    <li><a class="inline-flex min-h-11 items-center hover:text-gold-light" href="{{ 'https://www.tiktok.com/@'.Setting::get('tiktok_handle') }}" rel="noopener">TikTok</a></li>
                </ul>
            </div>
        </div>
        <div class="wrap relative flex flex-col gap-3 border-t border-ivory/10 pb-24 pt-6 text-[.88rem] sm:pb-6 text-ivory/65 sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ date('Y') }} Kinks &amp; Queens. Pre-order only. Prices in Trinidad &amp; Tobago dollars.</p>
            <p class="flex flex-wrap gap-x-5 gap-y-1">
                @if($fn = Setting::get('footer_note'))<span class="text-gold-light">{{ $fn }}</span>@endif
                <a href="{{ route('privacy') }}" class="hover:text-ivory">Privacy</a>
            </p>
        </div>
    </footer>

    {{-- Phone: keep the main action in reach. --}}
    @unless(request()->routeIs('preorder*', 'track*', 'clipins'))
        <div class="fixed inset-x-3 bottom-3 z-30 sm:hidden">
            <a href="{{ route('preorder') }}" class="btn btn-gold w-full shadow-xl"><x-crown class="-mt-0.5"/> Start a pre-order</a>
        </div>
    @endunless
</body>
</html>
