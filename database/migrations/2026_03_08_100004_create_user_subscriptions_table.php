<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Email notification preferences
            $table->boolean('email_price_alerts')->default(true);
            $table->boolean('email_daily_report')->default(true);
            $table->boolean('email_weekly_report')->default(true);
            $table->boolean('email_market_analysis')->default(true);
            
            // Push notification preferences
            $table->boolean('push_price_alerts')->default(true);
            $table->boolean('push_daily_report')->default(false);
            $table->boolean('push_major_events')->default(true);
            
            // Alert thresholds
            $table->decimal('price_alert_threshold', 10, 2)->nullable()->comment('Alert when price changes by this percentage');
            $table->string('preferred_brands')->nullable()->comment('JSON array of preferred brand IDs');
            
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
