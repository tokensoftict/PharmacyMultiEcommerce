<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seasonal_campaigns', function (Blueprint $table) {
            $table->id();

            // ── General ──────────────────────────────────────────────────────
            $table->boolean('enabled')->default(false);
            $table->string('theme', 40)->default('christmas');            // christmas | new_year | custom
            $table->string('campaign_name');
            $table->text('campaign_description')->nullable();
            $table->date('start_date')->nullable();                        // inclusive, 00:00 in `timezone`
            $table->date('end_date')->nullable();                          // inclusive, through 23:59:59 in `timezone`
            $table->string('timezone', 64)->default('Africa/Lagos');
            $table->integer('priority')->default(0);                       // highest wins when campaigns overlap

            // ── Logo ─────────────────────────────────────────────────────────
            $table->boolean('show_santa_hat')->default(false);
            $table->string('logo_decoration', 40)->nullable();             // e.g. santa_hat | gold_crown | null

            // ── Snow ─────────────────────────────────────────────────────────
            $table->boolean('show_snowfall')->default(false);
            $table->string('snowfall_intensity', 10)->default('medium');   // low | medium | high
            $table->string('snowfall_speed', 10)->default('normal');       // slow | normal | fast
            $table->decimal('snowfall_opacity', 3, 2)->default(0.50);

            // ── Decorations ──────────────────────────────────────────────────
            $table->boolean('show_decorations')->default(false);
            $table->boolean('show_christmas_lights')->default(false);
            $table->boolean('show_sparkles')->default(false);
            $table->boolean('show_ornaments')->default(false);
            $table->string('decoration_intensity', 10)->default('medium'); // low | medium | high
            $table->string('lights_intensity', 10)->default('medium');     // low | medium | high (blink strength)

            // ── Greeting ─────────────────────────────────────────────────────
            $table->boolean('show_greeting')->default(false);
            $table->string('greeting_message')->nullable();
            $table->string('greeting_subtitle')->nullable();
            $table->string('greeting_alignment', 10)->default('center');   // left | center | right
            $table->string('greeting_text_style', 10)->default('normal');  // normal | bold | italic | script
            $table->string('greeting_background_color', 9)->nullable();
            $table->string('greeting_text_color', 9)->nullable();

            // ── Promotional banner ───────────────────────────────────────────
            $table->boolean('show_seasonal_banner')->default(false);
            $table->string('banner_image')->nullable();
            $table->string('banner_title')->nullable();
            $table->string('banner_subtitle')->nullable();
            $table->string('banner_link')->nullable();

            // ── New Year ─────────────────────────────────────────────────────
            $table->boolean('show_gold_accents')->default(false);
            $table->boolean('show_fireworks')->default(false);
            $table->boolean('show_confetti')->default(false);
            $table->boolean('show_gold_particles')->default(false);
            $table->boolean('show_countdown')->default(false);
            $table->dateTime('countdown_target_date')->nullable();         // wall-clock in `timezone`
            $table->string('countdown_message')->nullable();
            $table->string('fireworks_intensity', 10)->default('low');     // low | medium | high
            $table->string('celebration_message')->nullable();

            // ── Visual ───────────────────────────────────────────────────────
            $table->string('primary_festive_color', 9)->nullable();
            $table->string('secondary_festive_color', 9)->nullable();
            $table->decimal('background_effect_opacity', 3, 2)->default(1.00);

            // ── Extensibility: free-form flags for future effects ────────────
            $table->json('extra_effects')->nullable();

            $table->timestamps();

            $table->index(['enabled', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seasonal_campaigns');
    }
};

