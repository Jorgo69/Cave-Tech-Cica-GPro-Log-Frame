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
        Schema::create('qualitative_evaluations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->uuid('activity_id')->nullable();
            $table->uuid('evaluator_id');
            $table->string('rating', 50)->nullable();
            $table->integer('score')->nullable();
            $table->longText('comments')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('project_id');
            $table->index('activity_id');
            $table->index('evaluator_id');

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('set null');
            $table->foreign('evaluator_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qualitative_evaluations');
    }
};
