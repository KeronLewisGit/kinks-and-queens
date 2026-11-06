@props(['dark' => false, 'compact' => false])
{{-- Typographic redraw of the KQ monogram: the Q's tail sweeps under the K, as on the brand's flyers. --}}
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-3']) }}>
    <svg viewBox="0 0 64 56" class="h-11 w-auto shrink-0" aria-hidden="true" focusable="false">
        <text x="2" y="38" font-family="'Playfair Display Variable', Georgia, serif" font-size="40" font-weight="500" fill="url(#kq-gold)">K</text>
        <text x="22" y="47" font-family="'Playfair Display Variable', Georgia, serif" font-size="40" font-weight="500" fill="url(#kq-gold)">Q</text>
    </svg>
    @unless($compact)
        <span class="grid leading-none">
            <span class="font-display text-[1.05rem] font-semibold uppercase tracking-[.22em] {{ $dark ? 'text-ivory' : 'text-ink' }}">Kinks <span class="text-gold">&amp;</span> Queens</span>
            <span class="mt-1.5 text-[.62rem] font-semibold uppercase tracking-[.34em] {{ $dark ? 'text-gold-light' : 'text-gold-text' }}">Where texture reigns</span>
        </span>
    @endunless
</span>
