<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('war_members', function (Blueprint $table) {
            $table->integer('ff_score')->nullable()->after('war_score');
            $table->string('estimated_stats')->nullable()->after('ff_score');
        });
    }

    public function down(): void
    {
        Schema::table('war_members', function (Blueprint $table) {
            $table->dropColumn(['ff_score', 'estimated_stats']);
        });
    }
};
