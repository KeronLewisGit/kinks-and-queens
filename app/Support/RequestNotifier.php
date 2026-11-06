<?php

namespace App\Support;

use App\Mail\NewRequest;
use App\Models\PreOrder;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/** Emails the owner when a request arrives. A mail failure never loses the request. */
class RequestNotifier
{
    public function send(PreOrder $order): void
    {
        $to = Setting::get('notify_email');
        if (! $to || ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return;
        }
        try {
            Mail::to($to)->send(new NewRequest($order));
        } catch (Throwable $e) {
            Log::warning('New request email failed', ['reference' => $order->reference, 'error' => $e->getMessage()]);
        }
    }
}
