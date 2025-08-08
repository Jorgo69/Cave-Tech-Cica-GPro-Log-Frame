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
        Schema::create('progress_trackers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('activity_id')->nullable();
            $table->uuid('project_id')->nullable();
            $table->date('date');
            $table->integer('progress_percentage');
            $table->text('status_update')->nullable();
            $table->text('justification')->nullable();
            $table->uuid('updated_by_user_id');
            $table->integer('performance_score')->nullable();
            $table->text('evaluation_comment')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('activity_id');
            $table->index('project_id');
            $table->index('updated_by_user_id');

            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('set null');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            $table->foreign('updated_by_user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_trackers');
    }
};
