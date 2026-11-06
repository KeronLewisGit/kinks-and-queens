<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('textures', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');            // Brazilian Kinky Curly
            $table->string('origin', 40);      // Brazilian
            $table->string('pattern', 40);     // Kinky Curly
            $table->string('script_word', 30); // "Curly", shown in script on the flyers
            $table->string('best_for');        // 4A–4B natural hair
            $table->string('tagline');
            $table->text('description');
            $table->json('highlights')->nullable();
            $table->json('formats');           // bundles, ponytails, clip-ins
            $table->string('bundle_weight', 30)->default('98–100 g');
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('coil', 20)->default('curl'); // wave | curl | coil: drives the gold pattern line
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('price_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('texture_id')->constrained()->cascadeOnDelete();
            $table->string('lengths', 40);              // "16,18,20"
            $table->unsignedTinyInteger('bundle_count');
            $table->unsignedInteger('price');            // whole TT dollars
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['texture_id', 'lengths']);
        });

        Schema::create('pre_orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 12)->unique();
            $table->string('kind', 20);                  // hair | clip_in_service
            $table->foreignId('texture_id')->nullable()->constrained()->nullOnDelete();
            $table->string('format', 20)->nullable();    // bundles | ponytail | clip-ins
            $table->foreignId('price_set_id')->nullable()->constrained()->nullOnDelete();
            $table->string('lengths', 40)->nullable();
            $table->unsignedTinyInteger('bundle_count')->nullable();
            $table->unsignedInteger('quoted_price')->nullable(); // snapshot of the list price when requested
            $table->string('status', 20)->default('new');
            $table->string('name');
            $table->string('phone', 40);
            $table->string('instagram', 60)->nullable();
            $table->string('email')->nullable();
            $table->string('area', 80)->nullable();
            $table->string('contact_via', 20)->default('instagram'); // instagram | whatsapp | phone
            $table->json('details')->nullable();         // clip-in answers, ponytail length, etc.
            $table->text('notes')->nullable();
            $table->text('staff_notes')->nullable();
            $table->timestamp('expected_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });

        Schema::create('order_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pre_order_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20);
            $table->string('note')->nullable();          // shown to the customer on the tracking page
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('gallery_photos', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('alt');
            $table->string('caption')->nullable();
            $table->string('label', 30)->nullable();     // Queen Cam, Client Cam, Texture Shot
            $table->foreignId('texture_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('sort')->default(0); // signed: new uploads go to the front with min - 1
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('gallery_photos');
        Schema::dropIfExists('order_events');
        Schema::dropIfExists('pre_orders');
        Schema::dropIfExists('price_sets');
        Schema::dropIfExists('textures');
    }
};
