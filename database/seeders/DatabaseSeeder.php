<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CatalogueSeeder::class);

        // Sample logins and requests exist only on a developer machine, never in production.
        if (app()->environment('local')) {
            $this->call(DemoSeeder::class);
        }
    }
}
