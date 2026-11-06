{{-- First visit of a session only: the monogram inks itself in, the crown drops, the curl draws, then the curtain lifts.
     Pure CSS timeline driven by html.splash (set by the inline head script), so it can never get stuck. Tap to skip. --}}
<div class="splash" aria-hidden="true">
    <div class="splash-inner">
        <svg class="splash-mark" viewBox="0 0 120 112" focusable="false">
            <path class="splash-crown" d="M44 22h32l1.6-15-9.6 6.8L60 2l-8 11.8L42.4 7z"/>
            <text class="k" x="14" y="80">K</text>
            <text class="q" x="46" y="96">Q</text>
        </svg>
        <x-coil type="curl" :width="190" :draw="false" class="splash-coil mt-2"/>
        <p class="splash-word">Kinks <span>&amp;</span> Queens</p>
        <p class="splash-tag">Where texture reigns</p>
    </div>
</div>
