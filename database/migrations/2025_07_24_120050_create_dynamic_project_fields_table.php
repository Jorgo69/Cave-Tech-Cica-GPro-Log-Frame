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
        Schema::create('dynamic_project_fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_type_id');
            $table->string('field_name', 100);
            $table->text('question_text');
            $table->string('input_type', 50);
            $table->json('options')->nullable();
            $table->integer('order');
            $table->string('target_project_field', 100);
            $table->string('section', 100);
            $table->string('delimiter_start', 255)->unique();
            $table->string('delimiter_end', 255)->unique();
            $table->boolean('is_required')->default(false);
            $table->timestamps();

            $table->unique(['project_type_id', 'field_name']);
            $table->foreign('project_type_id')->references('id')->on('project_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_project_fields');
    }
};
