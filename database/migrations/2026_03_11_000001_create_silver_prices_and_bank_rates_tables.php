<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('silver_prices', function (Blueprint $table) {
            $table->id();
            $table->string('source', 50)->index();
            $table->string('brand', 100);
            $table->bigInteger('buy_price')->default(0);
            $table->bigInteger('sell_price')->default(0);
            $table->string('currency', 5)->default('VND');
            $table->decimal('change_percent', 8, 4)->default(0);
            $table->timestamps();

            $table->index(['source', 'brand', 'created_at']);
        });

        Schema::create('bank_rates', function (Blueprint $table) {
            $table->id();
            $table->string('bank', 20)->index();
            $table->string('currency', 5)->default('USD');
            $table->decimal('buy_rate', 15, 2)->default(0);
            $table->decimal('sell_rate', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['bank', 'currency', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_rates');
        Schema::dropIfExists('silver_prices');
    }
};
