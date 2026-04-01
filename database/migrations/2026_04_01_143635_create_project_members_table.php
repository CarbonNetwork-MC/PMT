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
        Schema::create('project_members', function (Blueprint $table) {
            $table->char('project_uuid', 36);
            $table->char('user_uuid', 36);
            $table->unsignedBigInteger('project_role_id');
            $table->timestamps();

            $table->primary(['project_uuid', 'user_uuid']);

            $table->foreign('project_uuid')->references('uuid')->on('projects')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_uuid')->references('uuid')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('project_role_id')->references('id')->on('project_roles')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_members');
    }
};
