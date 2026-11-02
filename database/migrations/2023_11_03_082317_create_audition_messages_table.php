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
        Schema::create('audition_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audition_id')->constrained('auditions')->onDelete('cascade');
            $table->text('message');
            $table->text('message_ar')->nullable();
            $table->boolean('for_applicants')->default(false)->nullable();
            $table->boolean('for_shortlisted')->default(false)->nullable();
            $table->boolean('for_accepted')->default(false)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audition_messages');
    }
};
