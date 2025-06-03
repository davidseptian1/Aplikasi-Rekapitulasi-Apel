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
            // Tambahkan kolom nrp setelah no_telpon, buat unik dan boleh null
            $table->string('nrp', 50)->nullable()->unique()->after('no_telpon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Pastikan nama constraint atau kolom benar untuk drop unique index jika ada
            // $table->dropUnique(['nrp']); // Atau Laravel akan menanganinya
            $table->dropColumn('nrp');
        });
    }
};
