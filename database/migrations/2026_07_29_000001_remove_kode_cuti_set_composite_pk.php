<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Set NULL kode_jenis_cuti ke 1 (Cuti Tahunan)
        DB::statement('UPDATE saldo_cutis SET kode_jenis_cuti = 1 WHERE kode_jenis_cuti IS NULL');

        // 2. Drop FK kode_jenis_cuti (named after original column jenis_cuti)
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->dropForeign('saldo_cutis_jenis_cuti_foreign');
        });

        // 3. Remove auto_increment from saldo_cuti_id before dropping PK
        DB::statement('ALTER TABLE saldo_cutis MODIFY COLUMN saldo_cuti_id BIGINT UNSIGNED NOT NULL');

        // 4. Drop existing PK (saldo_cuti_id)
        DB::statement('ALTER TABLE saldo_cutis DROP PRIMARY KEY');

        // 5. Drop kolom saldo_cuti_id (kode cuti)
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->dropColumn('saldo_cuti_id');
        });

        // 6. Buat kode_jenis_cuti NOT NULL dan rename -> jenis_cuti
        DB::statement('ALTER TABLE saldo_cutis MODIFY COLUMN kode_jenis_cuti TINYINT UNSIGNED NOT NULL');
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->renameColumn('kode_jenis_cuti', 'jenis_cuti');
        });

        // 7. Set composite primary key (nip, jenis_cuti, label_tahun)
        DB::statement('ALTER TABLE saldo_cutis ADD PRIMARY KEY (nip, jenis_cuti, label_tahun)');

        // 8. Re-add FK jenis_cuti -> jenis_cutis.kode
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->foreign('jenis_cuti')->references('kode')->on('jenis_cutis');
        });
    }

    public function down(): void
    {
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->dropForeign('saldo_cutis_jenis_cuti_foreign');
        });

        DB::statement('ALTER TABLE saldo_cutis DROP PRIMARY KEY');

        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->renameColumn('jenis_cuti', 'kode_jenis_cuti');
        });

        DB::statement('ALTER TABLE saldo_cutis MODIFY COLUMN kode_jenis_cuti TINYINT UNSIGNED NULL');

        DB::statement('ALTER TABLE saldo_cutis ADD COLUMN saldo_cuti_id BIGINT UNSIGNED NOT NULL FIRST');
        DB::statement('ALTER TABLE saldo_cutis MODIFY COLUMN saldo_cuti_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE saldo_cutis ADD PRIMARY KEY (saldo_cuti_id)');

        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->foreign('kode_jenis_cuti')->references('kode')->on('jenis_cutis');
        });
    }
};
