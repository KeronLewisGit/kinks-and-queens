@extends('layouts.admin', ['title' => 'Settings'])

@section('content')
<h1 class="text-4xl">Settings</h1>
<form method="POST" action="{{ route('admin.settings.update') }}" class="card mt-8 grid max-w-[760px] gap-5 bg-white p-6">
    @csrf @method('PUT')
    <label class="flex min-h-11 items-center gap-3 rounded-xl bg-cream/60 px-4 py-3 font-semibold"><input type="checkbox" name="accepting_orders" value="1" class="size-5 accent-[#2b1a11]" @checked($s('accepting_orders') === '1')> Taking new pre-orders on the website</label>
    <div class="grid gap-5 sm:grid-cols-2">
        <label class="field"><span>Instagram username</span><input class="input" name="instagram_handle" value="{{ old('instagram_handle', $s('instagram_handle')) }}" required></label>
        <label class="field"><span>TikTok username</span><input class="input" name="tiktok_handle" value="{{ old('tiktok_handle', $s('tiktok_handle')) }}"></label>
        <label class="field"><span>WhatsApp number</span><input class="input" name="whatsapp_number" value="{{ old('whatsapp_number', $s('whatsapp_number')) }}" placeholder="1 868 000 0000"><span class="hint">Leave empty to hide WhatsApp buttons. Customers are sent to Instagram DM instead.</span></label>
        <label class="field"><span>Email new requests to</span><input class="input" type="email" name="notify_email" value="{{ old('notify_email', $s('notify_email')) }}"><span class="hint">Needs the mailbox set up in .env.</span></label>
    </div>
    <label class="field"><span>Announcement strip</span><input class="input" name="announcement" value="{{ old('announcement', $s('announcement')) }}" maxlength="160"><span class="hint">The dark strip across the top of every page. Leave empty to hide it.</span></label>
    <label class="field"><span>Footer note</span><input class="input" name="footer_note" value="{{ old('footer_note', $s('footer_note')) }}" maxlength="80"></label>
    <button class="btn btn-primary justify-self-start">Save settings</button>
</form>
@endsection
