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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->text('description')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('cascade');
            $table->date('date_of_birth')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('building')->nullable();
            $table->string('phone')->nullable();
            $table->string('gender')->nullable();
            $table->string('major')->nullable();
            $table->string('university')->nullable();
            $table->string('occupation')->nullable();
            $table->string('weight')->nullable();
            $table->string('height')->nullable();
            $table->string('shoes')->nullable();
            $table->string('pants')->nullable();
            $table->string('shirt')->nullable();
            $table->boolean('is_default')->default(true);
 
            $table->string('video')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
