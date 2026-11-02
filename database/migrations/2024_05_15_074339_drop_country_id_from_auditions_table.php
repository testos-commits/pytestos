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
            $table->dropForeign('auditions_country_id_foreign');
            $table->dropColumn('country_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditions', function (Blueprint $table) {
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
        
        });
    }
};
