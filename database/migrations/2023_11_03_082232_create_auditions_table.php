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
        Schema::create('auditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->string('title');
            $table->string('title_ar');
            $table->text('description');
            $table->text('description_ar');
            $table->string('image');
            $table->string('image_ar')->nullable();
            $table->string('location')->nullable();
            $table->string('location_ar')->nullable();
            $table->date('filming_date')->nullable();
            $table->date('applications_deadline')->nullable();
            $table->integer('filming_budget')->nullable();
            $table->integer('usage_budget')->nullable();
            $table->string('currency')->nullable();
            $table->boolean('show_budget')->default(false);
            $table->string('duration')->nullable();
            $table->string('duration_ar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditions');
    }
};
