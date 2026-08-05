<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop FK saldo_cutis yang mengacu ke jenis_cutis
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->dropForeign('saldo_cutis_jenis_cuti_foreign');
        });

        // 2. Rename kolom di jenis_cutis: jenis_cuti.id -> kode
        DB::statement('ALTER TABLE jenis_cutis CHANGE COLUMN `jenis_cuti.id` `kode` TINYINT(3) UNSIGNED NOT NULL');

        // 3. Rename kolom di pengajuan_cutis hanya jika masih bernama jenis_cuti.id
        $colExist = DB::select("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pengajuan_cutis' AND COLUMN_NAME = 'jenis_cuti.id'");
        if ($colExist[0]->cnt > 0) {
            DB::statement('ALTER TABLE pengajuan_cutis CHANGE COLUMN `jenis_cuti.id` `kode_jenis_cuti` TINYINT(3) UNSIGNED NOT NULL');
        }

        // 4. Re-add FK
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->foreign('jenis_cuti')->references('kode')->on('jenis_cutis');
        });

        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->foreign('kode_jenis_cuti')->references('kode')->on('jenis_cutis');
        });
    }

    public function down(): void
    {
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->dropForeign(['jenis_cuti']);
        });

        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->dropForeign(['kode_jenis_cuti']);
        });

        DB::statement('ALTER TABLE jenis_cutis CHANGE COLUMN `kode` `jenis_cuti.id` TINYINT(3) UNSIGNED NOT NULL');

        $colExist = DB::select("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pengajuan_cutis' AND COLUMN_NAME = 'kode_jenis_cuti'");
        if ($colExist[0]->cnt > 0) {
            DB::statement('ALTER TABLE pengajuan_cutis CHANGE COLUMN `kode_jenis_cuti` `jenis_cuti.id` TINYINT(3) UNSIGNED NOT NULL');
        }

        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->foreign('jenis_cuti')->references('jenis_cuti.id')->on('jenis_cutis');
        });
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->foreign(['jenis_cuti.id'])->references(['jenis_cuti.id'])->on('jenis_cutis');
        });
    }
};
