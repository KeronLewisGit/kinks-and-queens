<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex, nofollow">
    <title>Sign in · Kinks &amp; Queens</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @include('partials.transition-guard')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid min-h-screen place-items-center bg-espresso p-5">
    @include('partials.gold-gradient')
    <main class="w-full max-w-[420px] rounded-[1.75rem] bg-ivory p-8">
        <x-logo/>
        <h1 class="mt-8 text-3xl">Sign in</h1>
        <form method="POST" action="{{ route('admin.login.attempt') }}" class="mt-6 grid gap-4">
            @csrf
            <label class="field"><span>Email</span><input class="input" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus @error('email') aria-invalid="true" aria-describedby="e-email" @enderror></label>
            @error('email')<p id="e-email" class="err">{{ $message }}</p>@enderror
            <label class="field"><span>Password</span><input class="input" type="password" name="password" autocomplete="current-password" required></label>
            <label class="flex items-center gap-2 text-cocoa"><input type="checkbox" name="remember" value="1" class="size-5 accent-[#2b1a11]"> Keep me signed in</label>
            <button class="btn btn-primary mt-2">Sign in</button>
        </form>
    </main>
</body>
</html>
