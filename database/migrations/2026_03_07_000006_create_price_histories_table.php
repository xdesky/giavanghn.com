<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_histories', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 30)->index();           // sjc, v24k, v18k, xau_usd, xag_usd
            $table->decimal('open', 15, 4)->nullable();
            $table->decimal('high', 15, 4)->nullable();
            $table->decimal('low', 15, 4)->nullable();
            $table->decimal('close', 15, 4);
            $table->bigInteger('volume')->default(0);
            $table->string('period', 10)->default('1h');     // 1h, 4h, 1d, 1w
            $table->dateTime('period_at');
            $table->timestamps();

            $table->unique(['symbol', 'period', 'period_at']);
            $table->index(['symbol', 'period', 'period_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_histories');
    }
};
