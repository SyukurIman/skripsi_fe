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
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->change();
            $table->softDeletes();
        });

        Schema::table('dokters', function (Blueprint $table) {
            $table->uuid('id')->primary()->change();
            $table->softDeletes();

            $table->uuid('id_user')->change();
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('kategori__layanans', function (Blueprint $table) {
            $table->uuid('id')->primary()->change();
            $table->softDeletes();
        });

        Schema::table('pakets', function (Blueprint $table) {
            $table->uuid('id')->primary()->change();
            $table->softDeletes();

            $table->uuid('id_kategori_layanan')->change();
            $table->foreign('id_kategori_layanan')->references('id')->on('kategori__layanans')->onDelete('cascade');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('id')->primary()->change();
            $table->softDeletes();

            $table->uuid('id_paket')->change();
            $table->foreign('id_paket')->references('id')->on('pakets')->onDelete('cascade');

            $table->uuid('id_user')->change();
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('detail__pakets', function (Blueprint $table) {
            $table->uuid('id')->primary()->change();
            $table->softDeletes();

            $table->uuid('id_paket')->change();
            $table->foreign('id_paket')->references('id')->on('pakets')->onDelete('cascade');
        });

        Schema::table('sesis', function (Blueprint $table) {
            $table->uuid('id')->primary()->change();
            $table->softDeletes();

            $table->uuid('id_kategori_layanan')->change();
            $table->foreign('id_kategori_layanan')->references('id')->on('kategori__layanans')->onDelete('cascade');

            $table->uuid('id_dokter')->change();
            $table->foreign('id_dokter')->references('id')->on('dokters')->onDelete('cascade');
        });

        Schema::table('sesi__users', function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->softDeletes();

            $table->uuid('id_user')->change();
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');

            $table->uuid('id_order')->change();
            $table->foreign('id_order')->references('id')->on('orders')->onDelete('cascade');
        });

        Schema::table('key__links', function (Blueprint $table) {
            $table->uuid('id')->primary()->change();
            $table->softDeletes();

            $table->uuid('id_user')->change();
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');

            $table->uuid('id_sesi')->change();
            $table->foreign('id_sesi')->references('id')->on('sesis')->onDelete('cascade');
        });

        Schema::table('artikels', function (Blueprint $table) {
            $table->uuid('id')->primary()->change();
            $table->softDeletes();
        });

        Schema::table('notifikasis', function (Blueprint $table) {
            $table->uuid('id')->primary()->change();
            $table->softDeletes();

            $table->uuid('user_id')->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('dokters');
        Schema::dropIfExists('kategori__layanans');
        Schema::dropIfExists('pakets');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('detail__pakets');
        Schema::dropIfExists('sesis');
        Schema::dropIfExists('sesi__users');
        Schema::dropIfExists('key__links');
        Schema::dropIfExists('artikels');
        Schema::dropIfExists('notifikasis');
    }
};
