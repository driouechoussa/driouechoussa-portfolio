<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The Services section was removed from the site.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('services');
    }

    public function down(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('icon', 60);
            $table->json('title');
            $table->json('description');
            $table->json('features');
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });
    }
};
