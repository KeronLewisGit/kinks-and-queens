@props(['legend' => 'Your details', 'step' => null, 'notesPlaceholder' => null])
{{-- Shared by the pre-order and clip-in forms. --}}
<fieldset class="grid gap-5">
    <legend class="mb-4 flex items-baseline gap-3 font-display text-[1.7rem]">@if($step)<span class="text-gold-text">{{ $step }}</span>@endif {{ $legend }}</legend>
    <div class="grid gap-5 sm:grid-cols-2">
        <label class="field">
            <span>Your name</span>
            <input class="input" name="name" value="{{ old('name') }}" autocomplete="name" required maxlength="80" @error('name') aria-invalid="true" aria-describedby="e-name" @enderror>
            @error('name')<span id="e-name" class="err">{{ $message }}</span>@enderror
        </label>
        <label class="field">
            <span>Phone or WhatsApp</span>
            <input class="input" name="phone" type="tel" inputmode="tel" value="{{ old('phone') }}" autocomplete="tel" placeholder="868 000 0000" required maxlength="40" @error('phone') aria-invalid="true" aria-describedby="e-phone" @enderror>
            @error('phone')<span id="e-phone" class="err">{{ $message }}</span>@enderror
        </label>
    </div>
    <div>
        <p class="label">How should we reach you?</p>
        <div class="mt-2 grid gap-2 sm:grid-cols-3">
            @foreach(['instagram' => 'Instagram DM', 'whatsapp' => 'WhatsApp', 'phone' => 'Phone call'] as $v => $l)
                <label class="tile"><input type="radio" name="contact_via" value="{{ $v }}" @checked(old('contact_via', 'instagram') === $v)><span class="font-semibold">{{ $l }}</span></label>
            @endforeach
        </div>
        @error('contact_via')<span class="err">{{ $message }}</span>@enderror
    </div>
    <div class="grid gap-5 sm:grid-cols-2">
        <label class="field">
            <span>Instagram username <span class="font-normal text-mocha">(optional)</span></span>
            <input class="input" name="instagram" value="{{ old('instagram') }}" placeholder="@yourname" autocapitalize="none" maxlength="60" @error('instagram') aria-invalid="true" @enderror>
            @error('instagram')<span class="err">{{ $message }}</span>@enderror
        </label>
        <label class="field">
            <span>Email <span class="font-normal text-mocha">(optional)</span></span>
            <input class="input" name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="120" @error('email') aria-invalid="true" @enderror>
            @error('email')<span class="err">{{ $message }}</span>@enderror
        </label>
    </div>
    <label class="field">
        <span>Where are you? <span class="font-normal text-mocha">(helps us plan collection or delivery)</span></span>
        <input class="input" name="area" value="{{ old('area') }}" placeholder="e.g. Chaguanas" autocomplete="address-level2" maxlength="80">
    </label>
    <label class="field">
        <span>Anything else? <span class="font-normal text-mocha">(optional)</span></span>
        <textarea class="input min-h-28" name="notes" maxlength="1000" placeholder="{{ $notesPlaceholder ?? 'The look you want, your stylist, a date you need it by…' }}">{{ old('notes') }}</textarea>
    </label>
    <div class="hidden" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label></div>
    <label class="flex items-start gap-3">
        <input type="checkbox" name="consent" value="1" class="mt-1 size-5 shrink-0 accent-[#2b1a11]" @checked(old('consent')) required>
        <span class="text-[.98rem] text-cocoa">Kinks &amp; Queens may contact me about this request. My details are used only for this order. <a href="{{ route('privacy') }}" class="link">Privacy</a></span>
    </label>
    @error('consent')<span class="err">{{ $message }}</span>@enderror
</fieldset>
