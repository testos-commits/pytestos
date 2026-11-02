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
        Schema::create('app_states', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_maintenance')->default(false);
            $table->string('current_version')->nullable();
            $table->json('popup_title')->nullable();
            $table->json('popup_message')->nullable();
            $table->boolean('show_button')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_states');
    }
};
