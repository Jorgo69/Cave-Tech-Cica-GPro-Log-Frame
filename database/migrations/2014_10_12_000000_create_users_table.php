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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Clé primaire UUID
            $table->string('name');
            $table->string('email')->unique();
            
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('image')->nullable();
            $table->string('telephone', 50)->nullable();
            $table->string('sexe', 20)->nullable();
            $table->string('numero_identification', 100)->unique()->nullable();
            $table->string('pays', 100)->nullable();
            $table->string('ville', 100)->nullable();
            $table->uuid('role_id');
            $table->uuid('department_id');

            // Ajout des soft deletes
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
