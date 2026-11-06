<?php

namespace App\Http\Controllers;

use App\Models\Texture;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class TextureController extends Controller
{
    public function index(): View
    {
        return view('textures.index', ['textures' => Texture::active()->with('activePriceSets')->get()]);
    }

    public function show(Texture $texture): View
    {
        abort_unless($texture->is_active, 404);
        $texture->load('activePriceSets', 'photos');

        return view('textures.show', [
            'texture' => $texture,
            'others' => Texture::active()->where('id', '!=', $texture->id)->with('activePriceSets')->get(),
            'groups' => self::groups($texture),
            'catalogue' => self::catalogue(collect([$texture])),
        ]);
    }

    /** Price sets grouped the way the flyers present them. */
    public static function groups(Texture $texture): Collection
    {
        return $texture->activePriceSets
            ->groupBy(fn ($s) => $s->bundle_count.'-'.($s->isUniform() ? 'same' : 'mixed'))
            ->sortKeys()
            ->map(fn ($sets, $key) => [
                'title' => self::groupTitle($key),
                'sets' => $sets->sortBy(fn ($s) => $s->longest())->values(),
            ])
            ->sortBy(fn ($g, $key) => ['3-same' => 1, '3-mixed' => 2, '4-same' => 3, '4-mixed' => 4, '2-same' => 5][$key] ?? 9);
    }

    /** "3-same" → "3 bundles, one length"; works for any bundle count the owner adds. */
    public static function groupTitle(string $key): string
    {
        [$count, $shape] = explode('-', $key) + [1 => 'same'];

        return $count.' bundles, '.($shape === 'same' ? 'one length' : 'mixed lengths');
    }

    /** Compact JSON for the pre-order builder, length ruler and quiz. */
    public static function catalogue(Collection $textures): array
    {
        return $textures->map(fn (Texture $t) => [
            'id' => $t->id,
            'slug' => $t->slug,
            'name' => $t->name,
            'pattern' => $t->pattern,
            'bestFor' => $t->best_for,
            'image' => asset($t->image),
            'url' => route('textures.show', $t),
            'formats' => $t->formats,
            'sets' => $t->activePriceSets->map(fn ($s) => [
                'id' => $s->id,
                'lengths' => $s->lengthList(),
                'label' => $s->label(),
                'count' => $s->bundle_count,
                'uniform' => $s->isUniform(),
                'price' => $s->price,
            ])->values(),
        ])->values()->all();
    }
}
