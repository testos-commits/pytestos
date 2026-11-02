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
            $table->string('gender')->nullable();
            $table->integer('min_age')->nullable();
            $table->integer('max_age')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditions', function (Blueprint $table) {
            $table->dropColumn('gender');
            $table->dropColumn('min_age');
            $table->dropColumn('max_age');
        });
    }
};
