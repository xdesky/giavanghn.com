<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 500);
            $table->string('slug', 550)->unique();
            $table->string('trigger_type', 20)->index(); // daily | change
            $table->date('analysis_date')->index();
            $table->string('price_signature', 64)->index();
            $table->integer('word_count')->default(0);
            $table->string('thumbnail_path', 500)->nullable();
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->json('meta')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['trigger_type', 'analysis_date', 'price_signature'], 'analysis_unique_trigger_date_sig');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_articles');
    }
};
