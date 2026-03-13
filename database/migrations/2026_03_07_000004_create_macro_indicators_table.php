<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('macro_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('indicator', 50)->index();     // fed_rate, cpi, dxy, us10y, vix, etc.
            $table->string('name', 100);
            $table->string('value', 50);
            $table->string('impact', 255)->nullable();      // Descriptive impact on gold
            $table->string('signal', 20)->default('neutral'); // positive, negative, neutral
            $table->string('source', 80)->nullable();
            $table->timestamps();

            $table->index(['indicator', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('macro_indicators');
    }
};
