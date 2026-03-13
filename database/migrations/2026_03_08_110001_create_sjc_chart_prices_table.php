<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sjc_chart_prices', function (Blueprint $table) {
            $table->id();
            $table->date('price_date')->unique();
            $table->decimal('sell_million', 8, 2);
            $table->decimal('buy_million', 8, 2);
            $table->string('source', 20)->default('manual'); // manual|sync
            $table->timestamps();

            $table->index(['price_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sjc_chart_prices');
    }
};
