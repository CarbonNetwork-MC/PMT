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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_id');
            $table->text('description');
            $table->enum('status', ['todo', 'doing', 'done'])->default('todo');
            $table->integer('task_index')->default(0);
            $table->timestamp('deadline')->nullable();
            $table->integer('estimated_time')->nullable();
            $table->integer('actual_time')->nullable();
            $table->timestamps();

            $table->foreign('card_id')->references('id')->on('cards')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
