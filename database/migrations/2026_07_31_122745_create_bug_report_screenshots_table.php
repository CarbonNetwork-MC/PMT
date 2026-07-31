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
        Schema::create('bug_report_screenshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bug_report_id');
            $table->string('screenshot_path', 255);
            $table->timestamps();

            $table->foreign('bug_report_id')->references('id')->on('bug_reports')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bug_report_screenshots');
    }
};
