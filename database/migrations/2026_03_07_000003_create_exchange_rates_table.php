<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('pair', 15)->index();           // USD/VND, EUR/VND, etc.
            $table->decimal('rate', 15, 4);
            $table->decimal('change_percent', 8, 4)->default(0);
            $table->string('source', 50)->default('vcb');  // vcb, sbv, vietcombank
            $table->timestamps();

            $table->index(['pair', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
