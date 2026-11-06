@extends('layouts.admin', ['title' => 'Photos'])

@section('content')
<h1 class="text-4xl">Queen Cam photos</h1>
<p class="mt-2 text-cocoa">These appear in the Queen Cam gallery, on the home page (first seven) and on each texture page.</p>

<form method="POST" action="{{ route('admin.photos.store') }}" enctype="multipart/form-data" class="card mt-8 grid gap-4 bg-white p-6 md:grid-cols-2">
    @csrf
    <h2 class="text-2xl md:col-span-2">Add a photo</h2>
    <label class="field"><span>Photo (JPG, PNG or WebP, at least 500 px)</span><input class="input" type="file" name="photo" accept="image/jpeg,image/png,image/webp" required></label>
    <label class="field"><span>Describe it (for screen readers)</span><input class="input" name="alt" required maxlength="160" placeholder="Afro Coily ponytail, side view"></label>
    <label class="field"><span>Caption <span class="font-normal text-mocha">(optional)</span></span><input class="input" name="caption" maxlength="120"></label>
    <div class="grid grid-cols-2 gap-3">
        <label class="field"><span>Label</span><select class="input" name="label"><option value="">None</option>@foreach($labels as $l)<option>{{ $l }}</option>@endforeach</select></label>
        <label class="field"><span>Texture</span><select class="input" name="texture_id"><option value="">None</option>@foreach($textures as $t)<option value="{{ $t->id }}">{{ $t->pattern }}</option>@endforeach</select></label>
    </div>
    <div class="md:col-span-2"><button class="btn btn-primary">Upload</button></div>
</form>

<div class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
    @foreach($photos as $p)
        <div class="card overflow-hidden bg-white">
            <img src="{{ $p->url() }}" alt="{{ $p->alt }}" class="aspect-[4/3] w-full object-cover @unless($p->is_visible) opacity-40 @endunless" loading="lazy">
            <form method="POST" action="{{ route('admin.photos.update', $p) }}" class="grid gap-3 p-4">
                @csrf @method('PATCH')
                <label class="field"><span>Description</span><input class="input !min-h-10 !py-2" name="alt" value="{{ $p->alt }}" required></label>
                <label class="field"><span>Caption</span><input class="input !min-h-10 !py-2" name="caption" value="{{ $p->caption }}"></label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="field"><span>Label</span><select class="input !min-h-10 !px-2 !py-2" name="label"><option value="">None</option>@foreach($labels as $l)<option @selected($p->label === $l)>{{ $l }}</option>@endforeach</select></label>
                    <label class="field"><span>Texture</span><select class="input !min-h-10 !px-2 !py-2" name="texture_id"><option value="">None</option>@foreach($textures as $t)<option value="{{ $t->id }}" @selected($p->texture_id === $t->id)>{{ $t->pattern }}</option>@endforeach</select></label>
                    <label class="field"><span>Order</span><input class="input !min-h-10 !px-2 !py-2" type="number" name="sort" value="{{ $p->sort }}"></label>
                </div>
                <label class="flex min-h-11 items-center gap-2"><input type="checkbox" name="is_visible" value="1" class="size-5 accent-[#2b1a11]" @checked($p->is_visible)> Show on the website</label>
                <div class="flex items-center justify-between">
                    <button class="btn btn-ghost !min-h-10 !px-4">Save</button>
                </div>
            </form>
            @can('owner')
                <form method="POST" action="{{ route('admin.photos.destroy', $p) }}" class="px-4 pb-4" onsubmit="return confirm('Remove this photo?')">@csrf @method('DELETE')<button class="inline-flex min-h-11 items-center text-[.9rem] font-semibold text-rose underline">Remove photo</button></form>
            @endcan
        </div>
    @endforeach
</div>
@endsection
