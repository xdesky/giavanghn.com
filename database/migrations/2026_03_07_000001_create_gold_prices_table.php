<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gold_prices', function (Blueprint $table) {
            $table->id();
            $table->string('source', 50)->index();        // sjc, doji, pnj, btmc, phuquy, mihong, world
            $table->string('brand', 80)->index();          // SJC 1L, DOJI 9999, XAU/USD ...
            $table->string('karat', 10)->nullable();       // 9999, 24K, 18K, etc.
            $table->string('region', 30)->nullable();      // ha_noi, tp_hcm, da_nang, world
            $table->bigInteger('buy_price')->default(0);   // VND or USD cents
            $table->bigInteger('sell_price')->default(0);
            $table->string('currency', 5)->default('VND'); // VND or USD
            $table->decimal('change_percent', 8, 4)->default(0);
            $table->timestamps();

            $table->index(['source', 'brand', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gold_prices');
    }
};
