<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drafts are auditions that are saved but not yet visible to talents.
     * Existing auditions stay published (is_draft = false).
     */
    public function up(): void
    {
        Schema::table('auditions', function (Blueprint $table) {
            $table->boolean('is_draft')->default(false)->index()->after('is_terminated');
        });
    }

    public function down(): void
    {
        Schema::table('auditions', function (Blueprint $table) {
            $table->dropColumn('is_draft');
        });
    }
};
