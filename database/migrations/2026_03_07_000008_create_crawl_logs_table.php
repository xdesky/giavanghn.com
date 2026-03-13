<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crawl_logs', function (Blueprint $table) {
            $table->id();
            $table->string('crawler', 50)->index();         // sjc, doji, pnj, world, news, exchange
            $table->string('status', 20)->default('success'); // success, failed
            $table->integer('records_count')->default(0);
            $table->text('error_message')->nullable();
            $table->integer('duration_ms')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crawl_logs');
    }
};
