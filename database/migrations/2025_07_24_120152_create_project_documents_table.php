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
        Schema::create('project_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->uuid('uploaded_by_user_id');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');

            $table->timestamps();
            $table->softDeletes();

            $table->index('project_id');
            $table->index('uploaded_by_user_id');


            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('uploaded_by_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_documents');
    }
};
