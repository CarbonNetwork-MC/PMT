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
        Schema::create('backlog_cards', function (Blueprint $table) {
            $table->id();
            $table->char('backlog_id', 36);
            $table->string('name');
            $table->text('description')->nullable();
            $table->char('assignee_id', 36)->nullable();
            $table->integer('card_index')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backlog_cards');
    }
};
