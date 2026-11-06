import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';

document.documentElement.classList.add('js');
const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const money = (n) => 'TT$' + Number(n).toLocaleString('en-TT');

/* Reveal on scroll. Without JS or with reduced motion everything is simply visible. */
function reveal() {
    const els = document.querySelectorAll('.reveal, .coil.draw');
    if (reduced || !('IntersectionObserver' in window)) {
        els.forEach((el) => el.classList.add('is-in'));
        return;
    }
    const io = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
            if (e.isIntersecting) {
                e.target.classList.add('is-in');
                io.unobserve(e.target);
            }
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });
    els.forEach((el) => io.observe(el));
}

/* Header picks up a background once the page scrolls. */
function stickyHeader() {
    const header = document.querySelector('.site-header');
    if (!header) return;
    const set = () => header.classList.toggle('is-stuck', window.scrollY > 8);
    set();
    window.addEventListener('scroll', set, { passive: true });
}

/* Hero: swap texture photo, name and price. */
Alpine.data('textureSwitcher', (items) => ({
    items,
    i: 0,
    timer: null,
    get current() { return this.items[this.i]; },
    pick(i) { this.i = i; this.stop(); },
    start() {
        if (reduced || this.items.length < 2) return;
        this.timer = setInterval(() => { this.i = (this.i + 1) % this.items.length; }, 5200);
    },
    stop() { clearInterval(this.timer); this.timer = null; },
    money,
}));

/* Length ruler on texture pages: where each length falls, and what a set costs. */
Alpine.data('lengthRuler', (sets, lengths, start) => ({
    sets,
    lengths,
    len: start,
    // Approximate fall on a 5′5″ frame, measured stretched from the crown (% of figure height).
    fall: { 10: 26, 12: 30, 14: 34, 16: 38, 18: 43, 20: 48, 22: 53, 24: 58, 26: 64, 28: 70, 30: 75 },
    get y() { return this.fall[this.len] ?? 50; },
    get three() { return this.sets.find((s) => s.count === 3 && s.uniform && s.lengths[0] === this.len); },
    get four() { return this.sets.find((s) => s.count === 4 && s.uniform && s.lengths[0] === this.len); },
    get two() { return this.sets.find((s) => s.count === 2 && s.uniform && s.lengths[0] === this.len); },
    get where() {
        const map = { 10: 'the chin', 12: 'just above the shoulders', 14: 'the shoulders', 16: 'the collarbone', 18: 'below the collarbone', 20: 'mid-chest', 22: 'the bra strap', 24: 'mid-back', 26: 'the waist', 28: 'past the waist' };
        return map[this.len] ?? '';
    },
    // Hair silhouette: frames the face, then falls to the chosen length with a curly, scalloped edge.
    get hairPath() {
        const end = this.y * 4;
        const half = Math.min(64, 36 + (end - 80) * 0.14);
        const l = 100 - half, r = 100 + half;
        const bumps = 7, w = (r - l) / bumps;
        let edge = '';
        for (let k = 0; k < bumps; k++) {
            const x1 = r - k * w, x2 = r - (k + 1) * w;
            edge += ` Q ${((x1 + x2) / 2).toFixed(1)} ${(end + 9).toFixed(1)} ${x2.toFixed(1)} ${end.toFixed(1)}`;
        }
        return `M70 46 C74 14 126 14 130 46 C${r + 2} ${70} ${r + 4} ${end - 40} ${r} ${end}${edge} C${l - 4} ${end - 40} ${l - 2} 70 70 46 Z`;
    },
    step(d) {
        const i = this.lengths.indexOf(this.len) + d;
        if (i >= 0 && i < this.lengths.length) this.len = this.lengths[i];
    },
    money,
}));

/* Pre-order builder: progressive enhancement over a normal form. */
Alpine.data('preorder', (catalogue, prefill) => ({
    catalogue,
    texture: prefill.texture ? Number(prefill.texture) : null,
    format: prefill.format,
    set: prefill.set ? Number(prefill.set) : null,
    length: prefill.length ? Number(prefill.length) : null,
    count: null,
    init() {
        const s = this.chosenSet;
        if (s) this.count = s.count;
        this.$watch('texture', () => {
            if (this.t && !this.t.formats.includes(this.format)) this.format = this.t.formats[0];
            if (!this.chosenSet) this.set = null;
            this.count = this.counts.includes(this.count) ? this.count : this.counts[0] ?? null;
        });
        this.$watch('count', () => { if (this.chosenSet && this.chosenSet.count !== this.count) this.set = null; });
        if (this.t && !this.count) this.count = this.counts[0] ?? null;
    },
    get t() { return this.catalogue.find((x) => x.id === this.texture) ?? null; },
    get counts() { return this.t ? [...new Set(this.t.sets.map((s) => s.count))].sort((a, b) => (a === 3 ? -1 : b === 3 ? 1 : a - b)) : []; },
    get visibleSets() { return this.t ? this.t.sets.filter((s) => s.count === this.count) : []; },
    get chosenSet() { return this.t?.sets.find((s) => s.id === this.set) ?? null; },
    offers(f) { return this.t ? this.t.formats.includes(f) : true; },
    get summary() {
        if (!this.t) return null;
        const f = { bundles: 'Bundles', ponytail: 'Drawstring ponytail', 'clip-ins': 'Custom clip-ins' }[this.format] ?? '';
        let detail = '';
        if (this.format === 'bundles' && this.chosenSet) detail = this.chosenSet.label;
        if (this.format !== 'bundles' && this.length) detail = this.length + '″';
        return { name: this.t.name, format: f, detail, price: this.format === 'bundles' && this.chosenSet ? money(this.chosenSet.price) : (this.format && this.format !== 'bundles' ? 'Quoted when we confirm' : '') };
    },
    get ready() { return this.t && this.format && (this.format === 'bundles' ? !!this.chosenSet : !!this.length); },
    money,
}));

/* Texture Match quiz. */
Alpine.data('quiz', (questions, catalogue) => ({
    questions,
    catalogue,
    step: 0,
    answers: {},
    choose(key, value) {
        this.answers[key] = value;
        if (this.step < this.questions.length) this.step++;
        this.$nextTick(() => this.$refs.panel?.focus());
    },
    back() { if (this.step > 0) this.step--; this.$nextTick(() => this.$refs.panel?.focus()); },
    restart() { this.answers = {}; this.step = 0; this.$nextTick(() => this.$refs.panel?.focus()); },
    get result() {
        const slugOf = { kc: 'brazilian-kinky-curly', ac: 'mongolian-afro-coily', ts: 'brazilian-textured-straight' };
        // Your own hair decides the texture; the look only breaks a tie.
        const code = this.answers.hair ?? this.answers.look ?? 'kc';
        const t = this.catalogue.find((x) => x.slug === slugOf[code]) ?? this.catalogue[0];
        let format = this.answers.wear ?? 'bundles';
        let note = '';
        if (!t.formats.includes(format)) {
            note = format === 'clip-ins'
                ? `We make clip-ins from ${t.name} bundles with our custom clip-in service.`
                : `${t.name} is not offered as a ${format} yet, so we suggest bundles.`;
            format = format === 'clip-ins' ? 'clip-ins' : 'bundles';
        }
        const lookMismatch = this.answers.look && this.answers.look !== code;
        const set = t.sets.find((s) => s.count === 3 && s.uniform && s.lengths[0] === 18) ?? t.sets[0];
        return { t, format, note, lookMismatch, set };
    },
    money,
}));

/* Lightbox for the Queen Cam gallery. */
Alpine.data('lightbox', () => ({
    open: false,
    src: '', alt: '', caption: '',
    show(el) {
        this.src = el.dataset.full; this.alt = el.dataset.alt; this.caption = el.dataset.caption;
        this.open = true;
        this.$nextTick(() => this.$refs.close.focus());
        this.opener = el;
    },
    close() { this.open = false; this.opener?.focus(); },
}));

Alpine.data('copy', (text) => ({
    done: false,
    async go() {
        try { await navigator.clipboard.writeText(text); this.done = true; setTimeout(() => (this.done = false), 2500); } catch (e) { /* clipboard blocked: text stays visible to copy by hand */ }
    },
}));

Alpine.plugin(collapse);
Alpine.plugin(focus);
window.Alpine = Alpine;
Alpine.start();

reveal();
stickyHeader();
