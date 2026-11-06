<?php

namespace App\Support;

use App\Models\PreOrder;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;

/**
 * Each request form carries a one-time token. A second submit with the same token (a double tap,
 * or Back then Send again) shows the request that was already made instead of creating a copy.
 */
class Submission
{
    /** @param  Closure(): PreOrder  $create */
    public static function once(Request $request, Closure $create): RedirectResponse
    {
        $token = (string) $request->input('submission');
        if (! Str::isUuid($token)) {
            return self::sent($request, $create());
        }

        $key = 'kq.submission.'.$token;
        if (! Cache::add($key, 'pending', now()->addDay())) {
            // The first submit may still be saving: wait briefly for its reference.
            for ($i = 0; $i < 20 && Cache::get($key) === 'pending'; $i++) {
                usleep(250_000);
            }
            $reference = Cache::get($key);
            $order = $reference && $reference !== 'pending' ? PreOrder::where('reference', $reference)->first() : null;

            return $order
                ? self::sent($request, $order)
                : redirect()->route('track')->with('notice', 'We already received this request. Enter your reference and phone number to see it.');
        }

        try {
            $order = $create();
        } catch (Throwable $e) {
            Cache::forget($key);
            throw $e;
        }
        Cache::put($key, $order->reference, now()->addDay());

        return self::sent($request, $order);
    }

    private static function sent(Request $request, PreOrder $order): RedirectResponse
    {
        $request->session()->put('kq.orders.'.$order->reference, true);

        return redirect()->route('preorder.sent', $order);
    }
}
