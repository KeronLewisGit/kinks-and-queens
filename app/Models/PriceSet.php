<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceSet extends Model
{
    protected $fillable = ['texture_id', 'lengths', 'bundle_count', 'price', 'sort', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'price' => 'integer', 'bundle_count' => 'integer'];
    }

    public function texture(): BelongsTo
    {
        return $this->belongsTo(Texture::class);
    }

    /** @return list<int> */
    public function lengthList(): array
    {
        return array_map('intval', explode(',', $this->lengths));
    }

    public function isUniform(): bool
    {
        return count(array_unique($this->lengthList())) === 1;
    }

    public function label(): string
    {
        $l = $this->lengthList();

        return $this->isUniform()
            ? count($l).' × '.$l[0].'″'
            : implode('″ · ', $l).'″';
    }

    public function longest(): int
    {
        return max($this->lengthList());
    }
}
