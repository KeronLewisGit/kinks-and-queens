<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryPhoto;
use App\Models\Texture;
use App\Support\GalleryImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class PhotoController extends Controller
{
    public const LABELS = ['Queen Cam', 'Client Cam', 'Texture Shot', 'Wear Test', 'Behind the scenes'];

    public function index(): View
    {
        return view('admin.photos.index', [
            'photos' => GalleryPhoto::with('texture')->orderBy('sort')->get(),
            'textures' => Texture::orderBy('sort')->get(),
            'labels' => self::LABELS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144', 'dimensions:min_width=500,min_height=500'],
            'alt' => ['required', 'string', 'max:160'],
            'caption' => ['nullable', 'string', 'max:120'],
            'label' => ['nullable', 'in:'.implode(',', self::LABELS)],
            'texture_id' => ['nullable', 'exists:textures,id'],
        ], ['alt.required' => 'Describe the photo for people using screen readers, for example “Afro Coily ponytail, side view”.']);

        try {
            $path = GalleryImage::store($request->file('photo'));
        } catch (Throwable) {
            return back()->withInput()->withErrors(['photo' => 'We could not read that photo. Try saving it as a JPG and upload it again.']);
        }

        GalleryPhoto::create([
            'path' => $path, 'alt' => $data['alt'], 'caption' => $data['caption'] ?? null, 'label' => $data['label'] ?? null,
            'texture_id' => $data['texture_id'] ?? null, 'sort' => (int) GalleryPhoto::min('sort') - 1,
        ]);

        return back()->with('ok', 'Photo added to the Queen Cam gallery.');
    }

    public function update(Request $request, GalleryPhoto $photo): RedirectResponse
    {
        $data = $request->validate([
            'alt' => ['required', 'string', 'max:160'],
            'caption' => ['nullable', 'string', 'max:120'],
            'label' => ['nullable', 'in:'.implode(',', self::LABELS)],
            'texture_id' => ['nullable', 'exists:textures,id'],
            'sort' => ['required', 'integer', 'min:-100000', 'max:100000'],
        ]);
        $photo->update($data + ['is_visible' => $request->boolean('is_visible')]);

        return back()->with('ok', 'Photo saved.');
    }

    public function destroy(GalleryPhoto $photo): RedirectResponse
    {
        if (! Str::startsWith($photo->path, 'assets/')) {
            Storage::disk('public')->delete($photo->path);
        }
        $photo->delete();

        return back()->with('ok', 'Photo removed.');
    }
}
