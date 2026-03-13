<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_stats', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('sjc_spread', 30)->nullable();           // Chenh lech Mua-Ban SJC
            $table->string('trading_volume', 30)->nullable();       // Khoi luong GD trong nuoc
            $table->string('volatility_24h', 30)->nullable();       // Bien dong 24h
            $table->string('volatility_trend', 10)->default('up');
            $table->string('usd_vnd_rate', 30)->nullable();         // Ty gia USD/VND
            $table->string('usd_vnd_change', 30)->nullable();
            $table->string('usd_vnd_trend', 10)->default('up');
            $table->string('dxy_value', 30)->nullable();            // Chi so DXY
            $table->string('dxy_change', 30)->nullable();
            $table->string('dxy_trend', 10)->default('down');
            $table->string('cpi_value', 30)->nullable();            // Lam phat My (CPI)
            $table->string('cpi_period', 50)->nullable();
            $table->string('cpi_delta', 50)->nullable();
            $table->timestamps();

            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_stats');
    }
};
