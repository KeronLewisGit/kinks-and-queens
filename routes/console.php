<?php

use Illuminate\Support\Facades\Artisan;

/*
 * Runs after every Composer install/update (see composer.json), which is how Hostinger's
 * Git deploy finishes. On the live site it applies new migrations and rebuilds the caches
 * so a push never serves stale routes, config or views. Anywhere else it does nothing.
 */
Artisan::command('app:deployed', function () {
    if (! file_exists(base_path('.env')) || ! app()->environment('production') || config('app.key') === null) {
        $this->line('Not a configured production install: skipping migrations and caches.');

        return 0;
    }
    $this->call('migrate', ['--force' => true]);
    $this->call('optimize:clear');
    $this->call('optimize');

    return 0;
})->purpose('Migrate and rebuild caches after a production deploy');
