<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GalleryPhoto extends Model
{
    protected $fillable = ['path', 'alt', 'caption', 'label', 'texture_id', 'sort', 'is_visible'];

    protected function casts(): array
    {
        return ['is_visible' => 'boolean'];
    }

    public function texture(): BelongsTo
    {
        return $this->belongsTo(Texture::class);
    }

    /** Bundled photos live in public/assets; uploads live in public/uploads (the public disk). */
    public function url(): string
    {
        return Str::startsWith($this->path, 'assets/') ? asset($this->path) : asset('uploads/'.$this->path);
    }
}
