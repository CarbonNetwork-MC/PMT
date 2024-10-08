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
        Schema::create('backlog_tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('backlog_card_id');
            $table->text('description');
            $table->enum('status', ['todo', 'doing', 'done'])->default('todo');
            $table->integer('task_index')->default(0);
            $table->char('backlog_id', 36);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backlog_tasks');
    }
};
