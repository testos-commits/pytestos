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
        Schema::table('casting_image_types', function (Blueprint $table) {
            $table->json('name')->nullable();
            $table->json('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('casting_image_types', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
        });
    }
};
