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
        Schema::create('sprints', function (Blueprint $table) {
            $table->uuid()->primary()->unique();
            $table->char('project_uuid', 36);
            $table->string('name');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->enum('status', ['planned', 'active', 'completed'])->default('planned');
            $table->boolean('is_archived')->default(false);
            $table->timestamp('archived_at')->nullable();
            $table->char('archived_by', 36)->nullable();
            $table->timestamps();

            $table->foreign('project_uuid')->references('uuid')->on('projects')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('archived_by')->references('uuid')->on('users')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sprints');
    }
};
