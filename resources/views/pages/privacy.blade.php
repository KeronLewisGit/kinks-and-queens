@extends('layouts.app', ['title' => 'Privacy'])

@section('content')
<x-page-hero title="Privacy" lead="What we collect when you send a request, and what we do with it." image="assets/img/straight-blend.webp" alt="Brazilian Textured Straight blended with relaxed hair" coil="wave"/>
<section class="wrap py-16">
    <div class="prose-kq grid gap-5 text-[1.05rem] text-cocoa">
        <h2 class="text-3xl text-ink">What we collect</h2>
        <p>When you send a pre-order or clip-in request we keep the details you type in: your name, phone number, and optionally your Instagram username, email, area and notes, together with what you asked for.</p>
        <h2 class="mt-4 text-3xl text-ink">Why</h2>
        <p>Only to confirm and fulfil your request, to show you its progress on the tracking page and to contact you about it. We do not sell your details or add you to marketing lists.</p>
        <h2 class="mt-4 text-3xl text-ink">How long</h2>
        <p>We keep order records for as long as we need them for the order and for our accounts. Ask us at any time to see or delete your details by sending a DM on Instagram.</p>
        <h2 class="mt-4 text-3xl text-ink">Cookies</h2>
        <p>This site uses only the cookies it needs to work, such as keeping your form secure and remembering the requests you can track. There are no advertising or tracking cookies.</p>
    </div>
</section>
@endsection
