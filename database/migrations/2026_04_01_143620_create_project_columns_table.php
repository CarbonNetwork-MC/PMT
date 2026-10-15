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
        Schema::create('project_columns', function (Blueprint $table) {
            $table->id();
            $table->char('project_uuid', 36);
            $table->string('name');
            $table->integer('position');
            $table->enum('column_type', ['todo', 'doing', 'done'])->default('todo');
            $table->unsignedBigInteger('color_id')->nullable();
            $table->timestamps();

            $table->foreign('project_uuid')->references('uuid')->on('projects')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('color_id')->references('id')->on('column_colors')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_columns');
    }
};
