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
        Schema::table('sesi__users', function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->string('id_user');
            $table->string('id_order');
            $table->date('batas_waktu');
            $table->integer('sesi_terpakai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
