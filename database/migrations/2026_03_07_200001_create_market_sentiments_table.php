<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_sentiments', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();

            // Composite Fear & Greed Index (0-100)
            $table->unsignedTinyInteger('fear_greed_index');

            // 4 component scores (0-100 each)
            $table->decimal('price_trend_score', 5, 2)->comment('XAU/USD SMA trend 40%');
            $table->decimal('domestic_consensus_score', 5, 2)->comment('8 brands agreement 25%');
            $table->decimal('momentum_score', 5, 2)->comment('Rate of change 20%');
            $table->decimal('spread_score', 5, 2)->comment('Buy-sell spread tightness 15%');

            // Sentiment breakdown
            $table->decimal('buy_percent', 5, 1);
            $table->decimal('neutral_percent', 5, 1);
            $table->decimal('sell_percent', 5, 1);

            // Trend
            $table->string('trend_label', 30);
            $table->string('trend_direction', 10); // up, neutral, down

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_sentiments');
    }
};
