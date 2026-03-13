<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('world_prices', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 20)->index();         // XAU/USD, XAU/EUR, XAG/USD, PLATINUM, PALLADIUM, DXY
            $table->string('name', 80);
            $table->decimal('price', 15, 4);
            $table->decimal('change_percent', 8, 4)->default(0);
            $table->decimal('change_amount', 15, 4)->default(0);
            $table->string('currency', 5)->default('USD');
            $table->timestamps();

            $table->index(['symbol', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('world_prices');
    }
};
