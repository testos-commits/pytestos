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
        Schema::table('auditions', function (Blueprint $table) {
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->json('image')->nullable();
            $table->json('location')->nullable();
            $table->json('duration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditions', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'image', 'location', 'duration']);
        });
    }
};
