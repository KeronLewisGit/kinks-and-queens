<?php

namespace App\View\Components;

use Illuminate\View\Component;

/**
 * A gold line drawn in the curl pattern of a texture:
 *  wave = Textured Straight, curl = Kinky Curly (4A–4B), coil = Afro Coily (4C).
 * It is a trochoid, so tighter textures loop more tightly, like the real hair.
 */
class Coil extends Component
{
    public string $path;

    public int $height;

    public function __construct(public string $type = 'curl', public int $width = 220, public bool $draw = true, public bool $dark = false)
    {
        [$r, $d, $step] = match ($type) {
            'wave' => [9.0, 7.0, 0.12],
            'coil' => [2.6, 8.5, 0.09],
            default => [4.6, 10.0, 0.1],
        };
        $this->height = (int) ceil(2 * $d + 6);
        $mid = $this->height / 2;
        $points = [];
        $t = 0.0;
        $x0 = null;
        while (true) {
            $x = $r * $t - $d * sin($t);
            $x0 ??= $x;
            $x -= $x0;
            if ($x > $width - 3) {
                break;
            }
            $points[] = sprintf('%.1f %.1f', $x + 3, $mid + $d * cos($t + M_PI / 2) * -1);
            $t += $step;
            if ($t > 400) {
                break;
            }
        }
        $this->path = 'M'.implode(' L', $points);
    }

    public function render()
    {
        return <<<'blade'
<svg {{ $attributes->merge(['class' => 'coil'.($draw ? ' draw' : '').($dark ? ' on-dark' : '')]) }} width="{{ $width }}" height="{{ $height }}" viewBox="0 0 {{ $width }} {{ $height }}" aria-hidden="true" focusable="false"><path d="{{ $path }}" pathLength="1"/></svg>
blade;
    }
}
