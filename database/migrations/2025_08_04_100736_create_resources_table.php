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
        Schema::create('resources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('activity_id');
            $table->string('name');
            $table->string('type', 50);
            $table->integer('quantity');
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->decimal('total_cost', 15, 2)->nullable();
            $table->string('category', 100)->nullable();
            $table->uuid('responsible_user_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('activity_id');
            $table->index('responsible_user_id');

            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
            $table->foreign('responsible_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
