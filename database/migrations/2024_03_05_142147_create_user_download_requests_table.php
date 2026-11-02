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
        Schema::create('user_download_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audition_id')->onDelete('cascade');
            $table->string('zip_path');
            $table->string('hash');
            $table->boolean('is_downloaded')->default(false);
            $table->foreignId('user_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_download_requests');
    }
};
