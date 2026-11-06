<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Texture extends Model
{
    /** Where each length falls on the body; the length ruler uses it with and without JavaScript. */
    public const FALLS_TO = [
        10 => 'the chin', 12 => 'just above the shoulders', 14 => 'the shoulders', 16 => 'the collarbone',
        18 => 'below the collarbone', 20 => 'mid-chest', 22 => 'the bra strap', 24 => 'mid-back',
        26 => 'the waist', 28 => 'past the waist',
    ];

    protected $fillable = [
        'slug', 'name', 'origin', 'pattern', 'script_word', 'best_for', 'tagline', 'description',
        'highlights', 'formats', 'bundle_weight', 'image', 'image_alt', 'coil', 'sort', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'highlights' => 'array',
            'formats' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function priceSets(): HasMany
    {
        return $this->hasMany(PriceSet::class)->orderBy('bundle_count')->orderBy('sort');
    }

    public function activePriceSets(): HasMany
    {
        return $this->priceSets()->where('is_active', true);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(GalleryPhoto::class)->where('is_visible', true)->orderBy('sort');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort');
    }

    public function fromPrice(): ?int
    {
        return $this->activePriceSets->min('price');
    }

    /** The name without its script word: "Brazilian Kinky" + script "Curly", as on the flyers. */
    public function lead(): string
    {
        return trim(preg_replace('/\s*'.preg_quote($this->script_word, '/').'$/i', '', $this->name));
    }

    /** "Drawstring ponytails and custom clip-ins", for the made-to-order note. */
    public function madeToOrderLabel(): ?string
    {
        $map = ['ponytail' => 'Drawstring ponytails', 'clip-ins' => 'custom clip-ins'];
        $list = collect($this->formats)->filter(fn ($f) => isset($map[$f]))->map(fn ($f) => $map[$f])->values();

        return $list->isEmpty() ? null : ucfirst($list->join(', ', ' and '));
    }

    public function offers(string $format): bool
    {
        return in_array($format, $this->formats ?? [], true);
    }
}
