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
        Schema::create('logical_frameworks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->longText('general_objective')->nullable();
            $table->longText('general_obj_indicators')->nullable();
            $table->longText('general_obj_verification_sources')->nullable();
            $table->longText('assumptions')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('project_id');

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logical_frameworks');
    }
};
