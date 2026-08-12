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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignID('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignID('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('request_type_id')->nullable()->constrained('request_types')->nullOnDelete(); // ✅ points to request_types
            $table->string('purpose');
            $table->string('status')->default('pending');
            $table->foreignID('current_responsibility_center_id')->nullable()->constrained('responsibility_centers')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
