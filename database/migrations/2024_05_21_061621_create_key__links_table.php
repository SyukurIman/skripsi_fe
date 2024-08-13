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
        Schema::create('key__links', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('id_sesi');
            $table->string('id_user');
            $table->string('id_dokter');
            $table->string('number_key');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('key__links');
    }
};
