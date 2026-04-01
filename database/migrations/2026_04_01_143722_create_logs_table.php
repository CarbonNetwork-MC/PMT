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
            $table->char('user_uuid', 36);
            $table->char('project_uuid', 36);
            $table->char('sprint_uuid', 36)->nullable();
            $table->char('backlog_uuid', 36)->nullable();
            $table->unsignedBigInteger('card_id')->nullable();
            $table->unsignedBigInteger('task_id')->nullable();
            $table->unsignedBigInteger('backlog_card_id')->nullable();
            $table->unsignedBigInteger('backlog_task_id')->nullable();
            $table->enum('action', ['create', 'update', 'delete']);
            $table->string('table');
            $table->json('data');
            $table->text('description')->nullable();
            $table->enum('environment', ['local', 'staging', 'production']);
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
