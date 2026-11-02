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
        Schema::create('auditions_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audition_id')->constrained('auditions')->onDelete('cascade');
            $table->foreignId('user_profile_id')->constrained('user_profiles')->onDelete('cascade');
            $table->boolean('is_shortlisted')->default(false)->nullable();
            $table->boolean('is_accepted')->default(false)->nullable();
            $table->boolean('in_selection')->default(false)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditions_applications');
    }
};
