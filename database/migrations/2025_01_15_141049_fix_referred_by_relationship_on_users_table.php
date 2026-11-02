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
        Schema::table('users', function (Blueprint $table) {
            // Modify the columns to be nullable
            $table->dropForeign(['referred_by']);

            
            // Modify the columns to be nullable
            $table->unsignedBigInteger('referred_by')->nullable()->change();
            
            // Re-add the foreign key constraints with ON DELETE SET NULL
            $table->foreign('referred_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);


            // Revert the columns to NOT NULL
            $table->unsignedBigInteger('referred_by')->nullable(false)->change();

            // Re-add the original foreign key constraints
            $table->foreign('referred_by')->references('id')->on('users');
        });
    }
};
