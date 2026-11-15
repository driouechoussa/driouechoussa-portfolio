<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Portfolio content. Columns typed `json` hold localized values
 * shaped as { "en": ..., "fr": ..., "ar": ... }.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('category', 50)->index();
            $table->string('title');
            $table->string('preview', 30);
            $table->string('icon', 60);
            $table->string('accent', 9);
            $table->string('year', 4);
            $table->string('client')->nullable();
            $table->json('duration');
            $table->json('role');
            $table->json('stack');
            $table->json('description');
            $table->json('overview');
            $table->json('challenge');
            $table->json('solution');
            $table->json('results');
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('category', 50)->index();
            $table->string('image')->nullable();
            $table->json('title');
            $table->json('excerpt');
            $table->json('body');
            $table->unsignedSmallInteger('read_time');
            $table->date('published_at')->nullable()->index();
            $table->timestamps();
        });

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

        Schema::create('education', function (Blueprint $table) {
            $table->id();
            $table->json('school');
            $table->json('degree');
            $table->json('field');
            $table->json('description');
            $table->string('start_year', 4);
            $table->string('end_year', 4)->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->json('role');
            $table->string('company');
            $table->json('location');
            $table->json('description');
            $table->string('start_year', 4);
            $table->string('end_year', 4)->nullable(); // null = current position
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('skill_groups', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique(); // translation key, e.g. "frontend"
            $table->string('icon', 60);
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_group_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedTinyInteger('level'); // 0–100
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon', 60);
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tools');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('skill_groups');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('education');
        Schema::dropIfExists('services');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('projects');
    }
};
