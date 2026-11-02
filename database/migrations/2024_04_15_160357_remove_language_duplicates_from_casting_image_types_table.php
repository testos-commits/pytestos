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
            $table->dropColumn(['name', 'name_ar', 'description', 'description_ar']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('casting_image_types', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('name_ar')->nullable();
            $table->string('description')->nullable();
            $table->string('description_ar')->nullable();
        });
    }
};
