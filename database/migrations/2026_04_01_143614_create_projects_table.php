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
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid()->primary()->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->char('owner_uuid', 36)->nullable();
            $table->boolean('is_archived')->default(false);
            $table->char('archived_by', 36)->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();

            $table->foreign('owner_uuid')->references('uuid')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('archived_by')->references('uuid')->on('users')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
