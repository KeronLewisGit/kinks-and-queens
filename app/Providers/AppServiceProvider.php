<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('owner', fn (User $user) => $user->isOwner());

        $tooMany = fn (string $field, string $message) => fn (Request $r) => back()
            ->withInput($r->except('_token'))
            ->withErrors([$field => $message]);

        RateLimiter::for('requests', function (Request $r) use ($tooMany) {
            $respond = $tooMany('form', 'We have received a lot of requests from this connection. Please wait a little while and try again, or DM us on Instagram.');
            $limits = [Limit::perHour(40)->by('ip:'.$r->ip())->response($respond)];
            $phone = preg_replace('/\D+/', '', (string) $r->input('phone'));
            if (strlen($phone) >= 7) {
                $limits[] = Limit::perHour(6)->by('phone:'.substr($phone, -7))->response($respond);
            }

            return $limits;
        });
        RateLimiter::for('tracking', fn (Request $r) => Limit::perMinute(10)->by($r->ip())
            ->response($tooMany('reference', 'Too many tries. Please wait a minute and try again.')));
        RateLimiter::for('login', fn (Request $r) => Limit::perMinute(5)->by(strtolower((string) $r->input('email')).'|'.$r->ip())
            ->response(fn () => back()->withInput($r->only('email'))->withErrors(['email' => 'Too many sign-in attempts. Please wait a minute and try again.'])));
    }
}
