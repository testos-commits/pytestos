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
        Schema::table('profile_images', function (Blueprint $table) {
            $table->foreignId('casting_image_type_id')->nullable()->constrained('casting_image_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profile_images', function (Blueprint $table) {
            $table->dropColumn('casting_image_type_id');
        });
    }
};
