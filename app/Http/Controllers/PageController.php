<?php

namespace App\Http\Controllers;

use App\Models\GalleryPhoto;
use App\Models\Texture;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $textures = Texture::active()->with('activePriceSets')->get();

        return view('pages.home', [
            'textures' => $textures,
            'photos' => GalleryPhoto::where('is_visible', true)->orderBy('sort')->take(7)->get(),
            'diary' => config('kq.diary'),
        ]);
    }

    public function match(): View
    {
        $textures = Texture::active()->with('activePriceSets')->get();

        return view('pages.match', [
            'quiz' => config('kq.quiz'),
            'textures' => $textures,
            'catalogue' => TextureController::catalogue($textures),
        ]);
    }

    public function gallery(Request $request): View
    {
        $textures = Texture::active()->get();
        $active = $textures->firstWhere('slug', $request->query('texture'));

        return view('pages.gallery', [
            'textures' => $textures,
            'active' => $active,
            'photos' => GalleryPhoto::with('texture')->where('is_visible', true)
                ->when($active, fn ($q) => $q->where('texture_id', $active->id))
                ->orderBy('sort')->get(),
        ]);
    }

    public function care(): View
    {
        return view('pages.care', ['care' => config('kq.care'), 'wash' => config('kq.wash')]);
    }

    public function faq(): View
    {
        return view('pages.faq', ['faqs' => config('kq.faqs')]);
    }

    public function story(): View
    {
        return view('pages.story', ['textures' => Texture::active()->get()]);
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }

    public function sitemap(): Response
    {
        $urls = collect(['home', 'textures.index', 'match', 'preorder', 'clipins', 'gallery', 'care', 'faq', 'story', 'track', 'privacy'])
            ->map(fn ($r) => route($r))
            ->merge(Texture::active()->get()->map(fn ($t) => route('textures.show', $t)));

        return response()->view('sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml');
    }
}
