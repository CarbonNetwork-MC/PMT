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
        Schema::create('backlog_task_assignees', function (Blueprint $table) {
            $table->unsignedBigInteger('backlog_task_id');
            $table->char('user_uuid', 36);
            $table->timestamps();

            $table->primary(['backlog_task_id', 'user_uuid']);
            
            $table->foreign('backlog_task_id')->references('id')->on('backlog_tasks')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_uuid')->references('uuid')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backlog_task_assignees');
    }
};
