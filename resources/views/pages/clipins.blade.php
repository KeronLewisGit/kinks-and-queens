@extends('layouts.app', ['flushFooter' => true, 'title' => 'Custom clip-ins from your bundles', 'description' => 'Bring your own bundles and Kinks & Queens will machine-sew them into a custom clip-in set of about 10–13 pieces.'])

@section('content')
<x-page-hero title="Custom clip-ins" script="repurpose your bundles" lead="Sometimes you don’t want a sew-in or a wig. Sometimes you want something quick, easy, versatile and removable." image="assets/img/clip-ins-afro-coily.webp" alt="Custom clip-in set sewn from Mongolian Afro Coily bundles" coil="coil">
    <a href="#request" class="btn btn-primary">Request a clip-in set</a>
</x-page-hero>

<section class="wrap py-20">
    <ol class="grid gap-5 md:grid-cols-3">
        @foreach([
            ['Bring your own bundles', 'Don’t leave old bundles to collect dust. Kinky, coily, curly or straight: give them new life.'],
            ['We make your set', 'Machine-sewn for strength, double or triple tracked, about 10–13 pieces depending on the hair you bring.'],
            ['Install, style, repeat', 'Clip in for a day or a week, take it out at night, and reuse it whenever you like.'],
        ] as $i => [$h, $p])
            <li class="reveal card p-7" style="--d:{{ $i * .08 }}s">
                <span class="flex size-12 items-center justify-center rounded-full bg-ink font-display text-[1.3rem] text-gold-light">{{ $i + 1 }}</span>
                <h2 class="mt-5 text-[1.6rem]">{{ $h }}</h2>
                <p class="mt-2 text-cocoa">{{ $p }}</p>
            </li>
        @endforeach
    </ol>
    <div class="mt-14 grid items-center gap-10 rounded-[2rem] bg-cream/70 p-6 sm:p-10 lg:grid-cols-2">
        <div class="frame"><img src="{{ asset('assets/img/straight-blend.webp') }}" alt="Textured Straight tracks blended with relaxed hair, back view" width="600" height="760" loading="lazy" class="aspect-[4/5] w-full object-cover"></div>
        <div>
            <h2 class="text-4xl">From bundles to a blend that belongs</h2>
            <p class="mt-4 text-cocoa">One client ordered three 18″ bundles of Mongolian Afro Coily and had two of them made into a full clip-in set. The result was a full, natural-looking blend that looked right at home in her hair. Because extensions shouldn’t just add hair; they should understand your texture.</p>
            <p class="mt-4 text-cocoa">Pricing depends on how much hair you bring and the fullness you want, so we quote each set when we confirm.</p>
        </div>
    </div>
</section>

<section id="request" class="scroll-mt-28 bg-cream/50 py-20">
    <div class="wrap max-w-[820px]">
        <h2 class="text-4xl sm:text-5xl">Request a clip-in set</h2>
        <p class="mt-3 text-cocoa">Tell us about the bundles you have. We’ll message you with a quote and drop-off details.</p>
        <form method="POST" action="{{ route('clipins.store') }}" class="mt-10 grid gap-10" novalidate>
            @csrf
            @if($errors->any())<div class="rounded-2xl border border-rose/40 bg-rose/5 p-5 font-semibold text-rose" role="alert">Please check the highlighted fields.</div>@endif
            <fieldset class="grid gap-5">
                <legend class="mb-4 font-display text-[1.7rem]">Your bundles</legend>
                <label class="field">
                    <span>Texture of your bundles</span>
                    <input class="input" name="texture" value="{{ old('texture') }}" list="clip-textures" placeholder="e.g. Brazilian Textured Straight" required maxlength="80" @error('texture') aria-invalid="true" @enderror>
                    <datalist id="clip-textures">
                        @foreach(\App\Models\Texture::active()->get() as $t)<option value="{{ $t->name }}">@endforeach
                        <option value="Body wave"><option value="Deep wave"><option value="Not sure">
                    </datalist>
                    @error('texture')<span class="err">{{ $message }}</span>@enderror
                </label>
                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="field">
                        <span>How many bundles?</span>
                        <select class="input" name="bundles" required>
                            @foreach(['1', '2', '3', '4', '5+'] as $b)<option value="{{ $b }}" @selected(old('bundles', '3') === $b)>{{ $b }}</option>@endforeach
                        </select>
                    </label>
                    <label class="field">
                        <span>Length <span class="font-normal text-mocha">(if you know it)</span></span>
                        <input class="input" name="length" value="{{ old('length') }}" placeholder="e.g. 18″" maxlength="40">
                    </label>
                </div>
                <div>
                    <p class="label">Fullness</p>
                    <div class="mt-2 grid gap-2 sm:grid-cols-3">
                        @foreach(['double' => ['Double tracked', 'Natural fullness'], 'triple' => ['Triple tracked', 'Extra volume'], 'advise' => ['Advise me', 'We’ll suggest one']] as $v => [$l, $s])
                            <label class="tile"><input type="radio" name="fullness" value="{{ $v }}" @checked(old('fullness', 'double') === $v)><span class="grid"><span class="font-semibold">{{ $l }}</span><span class="tile-sub">{{ $s }}</span></span></label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <p class="label">Your own hair</p>
                    <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-4">
                        @foreach(['Natural', 'Relaxed', 'Silk-pressed', 'Not sure'] as $h)
                            <label class="tile"><input type="radio" name="own_hair" value="{{ $h }}" @checked(old('own_hair') === $h)><span class="font-semibold">{{ $h }}</span></label>
                        @endforeach
                    </div>
                </div>
            </fieldset>
            <x-contact-fields notes-placeholder="The styles you want to wear, when you need the set…"/>
            <button class="btn btn-primary justify-self-start">Send clip-in request</button>
        </form>
    </div>
</section>
@endsection
