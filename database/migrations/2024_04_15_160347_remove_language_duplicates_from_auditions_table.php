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
            $table->dropColumn(['title', 'title_ar', 'description', 'description_ar', 'image', 'image_ar', 'location', 'location_ar','duration', 'duration_ar']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditions', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('image')->nullable();
            $table->string('image_ar')->nullable();
            $table->string('location')->nullable();
            $table->string('location_ar')->nullable();
            $table->string('duration')->nullable();
            $table->string('duration_ar')->nullable();
        });
    }
};
