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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->char('sprint_uuid', 36);
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('column_id');
            $table->enum('approval_status', ['None', 'Approved', 'Needs Work', 'Rejected'])->default('None');
            $table->integer('card_index')->default(0);
            $table->timestamp('deadline')->nullable();
            $table->timestamps();

            $table->foreign('sprint_uuid')->references('uuid')->on('sprints')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('column_id')->references('id')->on('project_columns')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
