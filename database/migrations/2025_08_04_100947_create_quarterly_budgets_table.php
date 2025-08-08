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
        Schema::create('quarterly_budgets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('budget_id');
            $table->integer('year');
            $table->integer('quarter');
            $table->decimal('amount', 15, 2);

            $table->timestamps();
            $table->softDeletes();

            $table->index('budget_id');

            $table->foreign('budget_id')->references('id')->on('budgets')->onDelete('cascade');
            $table->unique(['budget_id', 'year', 'quarter']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quarterly_budgets');
    }
};
