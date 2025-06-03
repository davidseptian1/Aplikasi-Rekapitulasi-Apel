# Rekap Apel

**Rekap Apel** adalah aplikasi web berbasis Laravel yang dirancang untuk membantu pencatatan dan pengelolaan data rekapitulasi apel harian. Aplikasi ini menyediakan fitur manajemen data pengguna, pangkat, jabatan, dan berbagai informasi penunjang lainnya dengan tampilan antarmuka yang modern dan responsif.

## Fitur Utama

-   ✅ **Manajemen Data Master**
    Kelola data seperti pangkat, jabatan, keterangan, dan subdis secara terpusat.

-   📅 **Rekapitulasi Apel Harian**
    Catat dan kelola kehadiran apel setiap hari secara efisien.

-   🔐 **Autentikasi & Manajemen Pengguna**
    Sistem login dan pengaturan akses pengguna yang aman.

-   🔔 **Notifikasi & Logging**
    Mendukung integrasi logging ke berbagai channel seperti Slack dan Papertrail.

-   📱 **Tampilan Responsif**
    Antarmuka modern berbasis CSS dan JavaScript yang kompatibel dengan berbagai perangkat.

---

## Struktur Direktori

| Direktori    | Deskripsi                                                                     |
| ------------ | ----------------------------------------------------------------------------- |
| `app/`       | Berisi logika utama aplikasi seperti Controller, Model, dan Service Provider. |
| `config/`    | File konfigurasi untuk berbagai komponen aplikasi.                            |
| `database/`  | Berisi migrasi, seeder, dan factory untuk pengelolaan database.               |
| `public/`    | Aset publik seperti `index.php`, CSS, dan JavaScript yang dikompilasi.        |
| `resources/` | View Blade, file SASS/CSS, dan JavaScript mentah.                             |
| `routes/`    | Definisi rute aplikasi.                                                       |
| `storage/`   | File cache, log, dan file buatan pengguna.                                    |
| `tests/`     | Pengujian unit dan fitur Laravel.                                             |
| `vendor/`    | Dependensi yang diinstal via Composer.                                        |

---

## Instalasi

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek secara lokal:

1. **Clone Repository**

    ```bash
    git clone <repository-url>
    cd app_rekapapel
    ```

2. **Instal Dependensi**

    ```bash
    composer install
    ```

3. **Salin File `.env` dan Atur Konfigurasi**

    ```bash
    cp .env.example .env
    ```

    Sesuaikan isi file `.env` dengan konfigurasi database dan lingkungan lokal Anda.

4. **Generate Application Key**

    ```bash
    php artisan key:generate
    ```

5. **Migrasi dan Seeder Database**

    - Jika pertama kali:
        ```bash
        php artisan migrate --seed
        ```
    - Jika ingin mengulang dari awal:
        ```bash
        php artisan migrate:fresh --seed
        ```

6. **Jalankan Server Lokal**
    ```bash
    php artisan serve
    ```

---
