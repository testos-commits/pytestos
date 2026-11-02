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
        Schema::create('audition_messages_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audition_message_id')->constrained('audition_messages')->onDelete('cascade');
            $table->foreignId('user_profile_id')->constrained('user_profiles')->onDelete('cascade');
            $table->boolean('read')->default(true);
            $table->foreignId('audition_messages_option_id')->nullable()->constrained('audition_messages_options')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_audition_messages');
    }
};
