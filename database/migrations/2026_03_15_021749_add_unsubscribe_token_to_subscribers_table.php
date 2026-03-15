<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->string('unsubscribe_token', 64)->nullable()->unique()->after('active');
            $table->timestamp('last_notified_at')->nullable()->after('unsubscribe_token');
        });

        // Generate tokens for existing rows
        DB::table('subscribers')->whereNull('unsubscribe_token')->eachById(function ($row) {
            DB::table('subscribers')->where('id', $row->id)->update([
                'unsubscribe_token' => bin2hex(random_bytes(32)),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropColumn(['unsubscribe_token', 'last_notified_at']);
        });
    }
};
