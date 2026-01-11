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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table-> string('project_name' , 255);
            $table->string('project_description', 500);
            $table->string('project_image')->nullable();
            $table->string('project_live_url' , 255)->nullable();
            $table->string('project_source_code' , 255)->nullable();
            $table->string('project_technologies_used' , 255)->nullable();
            $table->string('project_type' , 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
