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
            $table->unsignedBigInteger('column_id');
            $table->integer('task_index')->default(0);
            $table->timestamp('deadline')->nullable();
            $table->decimal('estimated_time', 8, 2)->nullable();
            $table->decimal('actual_time', 8, 2)->nullable();
            $table->timestamps();

            $table->foreign('card_id')->references('id')->on('cards')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('column_id')->references('id')->on('project_columns')->onUpdate('cascade')->onDelete('cascade');
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
