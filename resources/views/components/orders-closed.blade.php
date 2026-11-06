@props(['what' => 'pre-orders'])
<div class="card mx-auto max-w-[640px] p-8 text-center">
    <h2 class="text-3xl">We’re not taking new {{ $what }} right now</h2>
    <p class="mt-3 text-cocoa">Follow us on Instagram to hear when orders open again, or send us a DM.</p>
    <a class="btn btn-primary mt-6" href="{{ \App\Models\Setting::instagramDm() }}" rel="noopener">DM us on Instagram</a>
</div>
