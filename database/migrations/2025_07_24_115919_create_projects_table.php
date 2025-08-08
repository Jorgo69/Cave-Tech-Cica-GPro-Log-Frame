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
            $table->uuid('id')->primary();// Clé primaire UUID
            $table->uuid('creator_user_id');// Créateur du projet
             $table->uuid('project_type_id');
            $table->string('project_code')->unique();// Code unique du projet (ex: PRJ-001)

            $table->string('title');
            $table->string('short_title')->nullable();
            $table->longText('description')->nullable();
            $table->longText('general_objectives')->nullable();// ADDED for dynamic fields
            $table->longText('problem_analysis')->nullable();
            $table->longText('strategy')->nullable();
            $table->longText('justification')->nullable();
            $table->string('status')->default('draft');// 'draft', 'active', 'completed', 'on_hold', 'cancelled'

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->uuid('created_by_user_id')->nullable();// Pour l'audit, qui a créé l'entrée
            $table->uuid('updated_by_user_id')->nullable();// Pour l'audit, qui a mis à jour l'entrée

            $table->timestamps();// created_at, updated_at
            $table->softDeletes();// deleted_at

            // Index pour les clés étrangères et les colonnes fréquemment recherchées
            $table->index('creator_user_id');
            $table->index('project_type_id');
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');

            // Définition des clés étrangères
            $table->foreign('creator_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('project_type_id')->references('id')->on('project_types')->onDelete('restrict');
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
