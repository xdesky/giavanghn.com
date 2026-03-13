<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doji_gold_prices', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('karat')->default('9999');
            $table->string('category')->default('trong_nuoc');
            $table->bigInteger('buy_price')->default(0);
            $table->bigInteger('sell_price')->default(0);
            $table->string('currency', 10)->default('VND');
            $table->decimal('change_percent', 10, 4)->default(0);
            $table->timestamps();

            $table->index(['brand', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doji_gold_prices');
    }
};
