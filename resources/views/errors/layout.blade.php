{{-- Error pages never touch the database, so they still render when it is the thing that failed. --}}
<!DOCTYPE html>
<html lang="en-TT">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title') · Kinks & Queens</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen">
    @include('partials.gold-gradient')
    <main class="wrap flex min-h-screen flex-col items-start justify-center gap-6 py-16">
        <a href="{{ url('/') }}" aria-label="Kinks & Queens home"><x-logo/></a>
        <p class="caps mt-6 flex items-center gap-2 text-gold-text"><x-crown class="text-gold"/> @yield('code')</p>
        <h1 class="max-w-[18ch] text-5xl leading-[1.05] sm:text-6xl">@yield('heading')</h1>
        <p class="max-w-[52ch] text-lg text-cocoa">@yield('message')</p>
        <div class="mt-2 grid w-full gap-3 sm:flex sm:w-auto">
            @hasSection('actions')
                @yield('actions')
            @else
                <a href="{{ url('/') }}" class="btn btn-primary">Back to the home page</a>
                <a href="{{ url('/textures') }}" class="btn btn-ghost">See the textures</a>
            @endif
        </div>
    </main>
</body>
</html>
