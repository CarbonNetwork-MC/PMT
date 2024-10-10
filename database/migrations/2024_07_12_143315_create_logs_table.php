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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->char('user_id', 36);
            $table->char('project_id', 36)->nullable();
            $table->char('sprint_id', 36)->nullable();
            $table->char('backlog_id', 36)->nullable();
            $table->char('card_id', 36)->nullable();
            $table->char('task_id', 36)->nullable();
            $table->enum('action', ['create', 'update', 'delete']);
            $table->string('table');
            $table->json('data')->nullable();
            $table->text('description')->nullable();
            $table->enum('environment', ['local', 'staging', 'production'])->default('local');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
