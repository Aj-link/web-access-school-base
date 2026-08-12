<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('request_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_name');
            $table->timestamps();
        });

        DB::table('request_types')->insert([
            ['id' => 1, 'type_name' => 'Facility Reservation', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'type_name' => 'Material Request', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_types');
    }
};
