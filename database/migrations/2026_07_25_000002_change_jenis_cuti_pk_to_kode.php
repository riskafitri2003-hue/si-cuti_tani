<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop FK on pengajuan_cutis.jenis_cuti_id
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->dropForeign(['jenis_cuti_id']);
        });

        // 2. pengajuan_cutis: rename jenis_cuti_id -> jenis_cuti, change type to match kode
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->renameColumn('jenis_cuti_id', 'jenis_cuti');
        });
        DB::statement('ALTER TABLE pengajuan_cutis MODIFY COLUMN jenis_cuti TINYINT UNSIGNED NOT NULL');

        // 3. saldo_cutis: add jenis_cuti column, rename sisa_hari -> saldo
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->unsignedTinyInteger('jenis_cuti')->nullable()->after('pegawai_id');
        });
        DB::statement('ALTER TABLE saldo_cutis CHANGE COLUMN sisa_hari saldo INT NOT NULL DEFAULT 0');

        // 4. jenis_cutis: drop auto_increment PK, drop jenis_cuti_id, make kode PK
        DB::statement('ALTER TABLE jenis_cutis MODIFY COLUMN jenis_cuti_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE jenis_cutis DROP PRIMARY KEY');
        DB::statement('ALTER TABLE jenis_cutis DROP COLUMN jenis_cuti_id');
        DB::statement('ALTER TABLE jenis_cutis ADD PRIMARY KEY (kode)');

        // 5. pengajuan_cutis: re-create FK -> jenis_cutis.kode
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->foreign('jenis_cuti')->references('kode')->on('jenis_cutis');
        });

        // 6. saldo_cutis: re-create FK -> pegawais.nip + add FK -> jenis_cutis.kode
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
            $table->foreign('pegawai_id')->references('nip')->on('pegawais')->cascadeOnDelete();
            $table->foreign('jenis_cuti')->references('kode')->on('jenis_cutis');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->dropForeign(['jenis_cuti']);
        });

        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->dropForeign(['jenis_cuti']);
        });

        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->renameColumn('jenis_cuti', 'jenis_cuti_id');
        });
        DB::statement('ALTER TABLE pengajuan_cutis MODIFY COLUMN jenis_cuti_id BIGINT UNSIGNED NOT NULL');

        DB::statement('ALTER TABLE saldo_cutis CHANGE COLUMN saldo sisa_hari INT NOT NULL DEFAULT 0');
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->dropColumn('jenis_cuti');
        });

        DB::statement('ALTER TABLE jenis_cutis ADD COLUMN jenis_cuti_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT FIRST');
        DB::statement('ALTER TABLE jenis_cutis DROP PRIMARY KEY');
        DB::statement('ALTER TABLE jenis_cutis ADD PRIMARY KEY (jenis_cuti_id)');

        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->foreign('jenis_cuti_id')->references('jenis_cuti_id')->on('jenis_cutis');
        });

        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
            $table->foreign('pegawai_id')->references('nip')->on('pegawais')->cascadeOnDelete();
        });
    }
};
