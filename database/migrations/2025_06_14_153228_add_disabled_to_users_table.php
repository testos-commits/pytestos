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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('disabled')->default(false)->after('is_partner')->comment('Indicates if the user is disabled');
            $table->timestamp('disabled_at')->nullable()->after('disabled')->comment('Timestamp when the user was disabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['disabled', 'disabled_at']);
    
        });
    }
};
