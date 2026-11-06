<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** A delivery date has no time, and MySQL timestamps stop at 2038. */
    public function up(): void
    {
        Schema::table('pre_orders', function (Blueprint $table) {
            $table->date('expected_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pre_orders', function (Blueprint $table) {
            $table->timestamp('expected_at')->nullable()->change();
        });
    }
};
