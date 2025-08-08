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
        Schema::create('specific_objectives', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('logical_framework_id');
            $table->longText('description');
            $table->longText('indicators')->nullable(); // Text field for indicators from NewVision.txt
            $table->longText('verification_sources')->nullable();
            $table->longText('assumptions')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('logical_framework_id');

            $table->foreign('logical_framework_id')->references('id')->on('logical_frameworks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specific_objectives');
    }
};
