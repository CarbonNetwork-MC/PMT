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
            $table->unsignedBigInteger('backlog_card_id');
            $table->text('description');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->integer('task_index')->default(0);
            $table->timestamps();

            $table->foreign('backlog_card_id')->references('id')->on('backlog_cards')->onUpdate('cascade')->onDelete('cascade');
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
