<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reward_referrals', function (Blueprint $table) {
            // Modify the columns to be nullable
            $table->dropForeign(['referrer_id']);
            $table->dropForeign(['referred_id']);
            
            // Modify the columns to be nullable
            $table->unsignedBigInteger('referrer_id')->nullable()->change();
            $table->unsignedBigInteger('referred_id')->nullable()->change();
            
            // Re-add the foreign key constraints with ON DELETE SET NULL
            $table->foreign('referrer_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('referred_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reward_referrals', function (Blueprint $table) {
            $table->dropForeign(['referrer_id']);
            $table->dropForeign(['referred_id']);

            // Revert the columns to NOT NULL
            $table->unsignedBigInteger('referrer_id')->nullable(false)->change();
            $table->unsignedBigInteger('referred_id')->nullable(false)->change();

            // Re-add the original foreign key constraints
            $table->foreign('referrer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('referred_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
