<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // who restocked
            $table->integer('quantity_added');          // how many were added
            $table->integer('quantity_before');         // stock before restock
            $table->integer('quantity_after');          // stock after restock
            $table->string('supplier')->nullable();     // who supplied it
            $table->decimal('unit_price', 10, 2)->nullable(); // price per unit
            $table->date('arrival_date');               // what date it arrived
            $table->time('arrival_time')->nullable();   // what time it arrived
            $table->string('remarks')->nullable();      // any notes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
