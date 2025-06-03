<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Pastikan kolomnya ada sebelum menambahkan constraint
            if (Schema::hasColumn('users', 'subdis_id')) {
                $table->foreign('subdis_id')
                    ->references('id')
                    ->on('subdis') // Merujuk ke tabel 'subdis'
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hati-hati saat menghapus foreign key, pastikan nama constraint-nya benar.
            // Laravel biasanya menamakannya: nama_tabel_nama_kolom_foreign
            // Contoh: 'users_subdis_id_foreign'
            // Atau, Anda bisa menggunakan array kolom jika menggunakan versi Laravel yang lebih baru:
            if (DB::getDriverName() !== 'sqlite') { // Penanganan khusus untuk non-sqlite
                // Cek apakah foreign key ada sebelum mencoba menghapusnya
                // Cara mendapatkan nama foreign key bisa bervariasi tergantung database & versi Laravel
                // Jika menggunakan $table->dropForeign(['subdis_id']); dan gagal,
                // Anda mungkin perlu mencari nama constraint secara manual di database Anda
                // atau dari pesan error sebelumnya.
                // Untuk sekarang, cara paling umum:
                $foreignKeys = Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys('users');
                $constraintName = null;
                foreach ($foreignKeys as $foreignKey) {
                    if (in_array('subdis_id', $foreignKey->getLocalColumns())) {
                        $constraintName = $foreignKey->getName();
                        break;
                    }
                }
                if ($constraintName) {
                    $table->dropForeign($constraintName);
                }
            }
            // Jika Anda tahu nama constraint-nya secara pasti (misal dari error: users_subdis_id_foreign)
            // $table->dropForeign('users_subdis_id_foreign');
            // Atau jika Anda hanya ingin menghapus berdasarkan nama kolom (Laravel akan mencoba menebak nama constraint)
            // $table->dropForeign(['subdis_id']);
        });
    }
};
