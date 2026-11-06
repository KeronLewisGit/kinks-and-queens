<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PreOrder extends Model
{
    /**
     * Customer-facing journey. Labels and copy appear on the tracking page,
     * so they are written for the customer, not for staff.
     */
    public const CONTACT_VIA = ['instagram' => 'Instagram DM', 'whatsapp' => 'WhatsApp', 'phone' => 'Phone call'];

    public const STATUSES = [
        'new' => ['label' => 'Request received', 'copy' => 'We have your request and will message you to confirm the details.'],
        'confirmed' => ['label' => 'Confirmed', 'copy' => 'Texture, lengths and price are confirmed with you.'],
        'paid' => ['label' => 'Payment received', 'copy' => 'Thank you. Your hair is now being ordered.'],
        'ordered' => ['label' => 'On its way to us', 'copy' => 'Your hair has shipped to us. Most orders land within 14 business days.'],
        'ready' => ['label' => 'Ready for you', 'copy' => 'Your hair has arrived. We will message you to arrange collection or delivery.'],
        'completed' => ['label' => 'Completed', 'copy' => 'Enjoy your crown. Tag us in your Queen Cam.'],
        'cancelled' => ['label' => 'Cancelled', 'copy' => 'This request was cancelled. Message us if that is a surprise.'],
    ];

    public const FORMATS = [
        'bundles' => 'Bundles',
        'ponytail' => 'Drawstring ponytail',
        'clip-ins' => 'Custom clip-ins',
    ];

    protected $fillable = [
        'reference', 'kind', 'texture_id', 'format', 'price_set_id', 'lengths', 'bundle_count', 'quoted_price',
        'status', 'name', 'phone', 'instagram', 'email', 'area', 'contact_via', 'details', 'notes', 'staff_notes', 'expected_at',
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
            'expected_at' => 'date',
            'quoted_price' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (PreOrder $order) {
            $order->reference ??= static::newReference();
        });
    }

    public static function newReference(): string
    {
        do {
            // No 0/O or 1/I so it reads cleanly over WhatsApp.
            $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $ref = 'KQ-';
            for ($i = 0; $i < 5; $i++) {
                $ref .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
        } while (static::where('reference', $ref)->exists());

        return $ref;
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function texture(): BelongsTo
    {
        return $this->belongsTo(Texture::class);
    }

    public function priceSet(): BelongsTo
    {
        return $this->belongsTo(PriceSet::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(OrderEvent::class)->orderBy('created_at')->orderBy('id');
    }

    public function scopeOpen(Builder $q): Builder
    {
        return $q->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function contactViaLabel(): string
    {
        return self::CONTACT_VIA[$this->contact_via] ?? Str::headline((string) $this->contact_via);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status]['label'] ?? Str::headline($this->status);
    }

    public function isClipInService(): bool
    {
        return $this->kind === 'clip_in_service';
    }

    public function title(): string
    {
        if ($this->isClipInService()) {
            return 'Clip-in set from your bundles';
        }
        $format = self::FORMATS[$this->format] ?? 'Hair';

        return trim(($this->texture?->name ?? 'Textured hair').' · '.$format);
    }

    public function lengthsLabel(): ?string
    {
        if (! $this->lengths) {
            return null;
        }
        $l = explode(',', $this->lengths);

        return count(array_unique($l)) === 1 && count($l) > 1
            ? count($l).' × '.$l[0].'″'
            : implode('″ · ', $l).'″';
    }

    /** Customers prove it's their order with the last four digits, however they type the number. */
    public function phoneMatches(string $typed): bool
    {
        $stored = preg_replace('/\D+/', '', $this->phone);
        $given = preg_replace('/\D+/', '', $typed);

        return strlen($given) >= 4 && strlen($stored) >= 4 && substr($stored, -4) === substr($given, -4);
    }

    /** The message customers send us by WhatsApp or Instagram DM to open the conversation. */
    public function handoffMessage(): string
    {
        $lines = ['Hi Kinks & Queens! I just sent a pre-order request on your website.', 'Reference: '.$this->reference, $this->title()];
        if ($this->lengthsLabel()) {
            $lines[] = 'Lengths: '.$this->lengthsLabel();
        }
        if ($this->quoted_price) {
            $lines[] = 'List price: TT$'.number_format($this->quoted_price);
        }
        $lines[] = 'Name: '.$this->name;

        return implode("\n", $lines);
    }

    public function moveTo(string $status, ?string $note = null, ?User $by = null): void
    {
        $this->status = $status;
        if ($status === 'ordered' && ! $this->expected_at) {
            $this->expected_at = now()->addWeekdays(14);
        }
        $this->save();
        $this->events()->create(['status' => $status, 'note' => $note, 'user_id' => $by?->id]);
    }
}
