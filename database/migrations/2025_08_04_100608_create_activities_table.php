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
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('result_id');
            $table->longText('description');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->uuid('responsible_user_id')->nullable();
            $table->string('status', 50);
            $table->text('justification')->nullable();
            $table->boolean('is_milestone')->default(false);
            $table->integer('progress_percentage')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('result_id');
            $table->index('responsible_user_id');

            $table->foreign('result_id')->references('id')->on('results')->onDelete('cascade');
            $table->foreign('responsible_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
