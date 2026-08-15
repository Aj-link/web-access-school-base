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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('subject_code');
            $table->string('subject_name')->nullable();
            $table->string('teacher');
            $table->string('section');
            $table->string('department');
            $table->string('year_level');
            $table->string('day_type');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room');
            $table->string('school_year')->default('2026-2027');
            $table->string('semester')->default('1st Semester');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
