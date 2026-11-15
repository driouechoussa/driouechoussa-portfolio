<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('image')->nullable()->after('accent');
            $table->string('live_url')->nullable()->after('client');
            $table->string('source_url')->nullable()->after('live_url');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['image', 'live_url', 'source_url']);
        });
    }
};
