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
        Schema::create('backlog_card_assignees', function (Blueprint $table) {
            $table->unsignedBigInteger('backlog_card_id');
            $table->char('user_uuid', 36);
            $table->timestamps();

            $table->primary(['backlog_card_id', 'user_uuid']);

            $table->foreign('backlog_card_id')->references('id')->on('backlog_cards')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_uuid')->references('uuid')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backlog_card_assignees');
    }
};
