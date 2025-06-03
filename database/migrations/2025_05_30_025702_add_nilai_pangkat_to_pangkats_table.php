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
        Schema::table('pangkats', function (Blueprint $table) {
            // Tambahkan kolom nilai_pangkat setelah kolom 'name'
            // Anda bisa menyesuaikan tipe data jika perlu (misal: decimal)
            // dan apakah harus unik, boleh null, atau nilai default.
            // Di sini kita buat integer, boleh null, dengan default 0.
            $table->integer('nilai_pangkat')->nullable()->default(0)->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pangkats', function (Blueprint $table) {
            $table->dropColumn('nilai_pangkat');
        });
    }
};
