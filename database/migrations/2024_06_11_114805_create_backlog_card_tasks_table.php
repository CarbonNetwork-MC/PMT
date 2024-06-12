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
        Schema::create('backlog_card_tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('backlog_card_id');
            $table->text('description');
            $table->enum('status', ['todo', 'doing', 'done'])->default('todo');
            $table->integer('task_index')->default(0);
            $table->char('backlog_id', 36);
            $table->char('assignee_id', 36)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backlog_card_tasks');
    }
};
