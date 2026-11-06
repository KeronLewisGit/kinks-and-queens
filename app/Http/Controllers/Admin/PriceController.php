<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceSet;
use App\Models\Texture;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PriceController extends Controller
{
    public function index(): View
    {
        return view('admin.prices.index', ['textures' => Texture::orderBy('sort')->withCount('priceSets')->with('priceSets')->get()]);
    }

    public function edit(Texture $texture): View
    {
        $texture->load('priceSets');

        return view('admin.prices.edit', ['texture' => $texture]);
    }

    public function update(Request $request, Texture $texture): RedirectResponse
    {
        $data = $request->validate([
            'tagline' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:1200'],
            'best_for' => ['required', 'string', 'max:120'],
            'is_active' => ['boolean'],
            'formats' => ['array'],
            'formats.*' => [Rule::in(['bundles', 'ponytail', 'clip-ins'])],
            'sets' => ['array'],
            'sets.*.price' => ['required', 'integer', 'min:1', 'max:100000'],
            'sets.*.is_active' => ['boolean'],
        ]);

        $texture->update([
            'tagline' => $data['tagline'],
            'description' => $data['description'],
            'best_for' => $data['best_for'],
            'is_active' => $request->boolean('is_active'),
            'formats' => array_values($data['formats'] ?? ['bundles']),
        ]);

        foreach ($data['sets'] ?? [] as $id => $row) {
            PriceSet::where('texture_id', $texture->id)->whereKey($id)->update([
                'price' => $row['price'],
                'is_active' => (bool) ($row['is_active'] ?? false),
            ]);
        }

        return back()->with('ok', $texture->name.' saved. The website and pre-order form use these prices straight away.');
    }

    public function add(Request $request, Texture $texture): RedirectResponse
    {
        $data = $request->validate([
            'lengths' => ['required', 'string', 'regex:/^\s*\d{2}(\s*,\s*\d{2}){1,4}\s*$/'],
            'price' => ['required', 'integer', 'min:1', 'max:100000'],
        ], ['lengths.regex' => 'Write lengths in inches separated by commas, for example 16,18,20.']);

        $lengths = collect(explode(',', $data['lengths']))->map(fn ($l) => (int) trim($l));
        if ($lengths->contains(fn ($l) => $l < 8 || $l > 40)) {
            return back()->withErrors(['lengths' => 'Lengths must be between 8″ and 40″.']);
        }
        $key = $lengths->implode(',');
        if ($texture->priceSets()->where('lengths', $key)->exists()) {
            return back()->withErrors(['lengths' => 'That set is already on the list. Change its price above.']);
        }

        $texture->priceSets()->create(['lengths' => $key, 'bundle_count' => $lengths->count(), 'price' => $data['price'], 'sort' => 99]);

        return back()->with('ok', 'Added '.$key.'.');
    }
}
