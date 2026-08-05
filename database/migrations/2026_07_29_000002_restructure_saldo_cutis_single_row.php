<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 0. Drop FKs dulu
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->dropForeign('saldo_cutis_nip_foreign');
            $table->dropForeign('saldo_cutis_jenis_cuti_foreign');
        });

        // 1. Drop composite PK, buat label_tahun nullable
        DB::statement('ALTER TABLE saldo_cutis DROP PRIMARY KEY');
        DB::statement('ALTER TABLE saldo_cutis MODIFY COLUMN label_tahun VARCHAR(255) NULL');

        // 2. Tambah kolom baru
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->integer('saldo_n2')->default(0)->after('jenis_cuti');
            $table->integer('saldo_n1')->default(0)->after('saldo_n2');
            $table->integer('saldo_n')->default(0)->after('saldo_n1');
            $table->string('keterangan_n2')->nullable()->after('saldo_n');
            $table->string('keterangan_n1')->nullable()->after('keterangan_n2');
            $table->string('keterangan_n')->nullable()->after('keterangan_n1');
        });

        // 3. Migrasi: pivot 3 baris per nip jadi 1 baris
        DB::statement('
            INSERT INTO saldo_cutis (nip, jenis_cuti, saldo_n2, saldo_n1, saldo_n, keterangan_n2, keterangan_n1, keterangan_n, created_at, updated_at)
            SELECT
                p.nip,
                p.jenis_cuti,
                COALESCE(MAX(CASE WHEN p.label_tahun = \'N-2\' THEN p.saldo END), 0),
                COALESCE(MAX(CASE WHEN p.label_tahun = \'N-1\' THEN p.saldo END), 0),
                COALESCE(MAX(CASE WHEN p.label_tahun = \'N\' THEN p.saldo END), 0),
                MAX(CASE WHEN p.label_tahun = \'N-2\' THEN p.keterangan END),
                MAX(CASE WHEN p.label_tahun = \'N-1\' THEN p.keterangan END),
                MAX(CASE WHEN p.label_tahun = \'N\' THEN p.keterangan END),
                NOW(), NOW()
            FROM saldo_cutis p
            GROUP BY p.nip, p.jenis_cuti
        ');

        // 4. Hapus baris lama (yang masih punya label_tahun)
        DB::statement('DELETE FROM saldo_cutis WHERE label_tahun IS NOT NULL');

        // 5. Hapus kolom lama
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->dropColumn(['label_tahun', 'saldo', 'keterangan', 'tahun']);
        });

        // 6. Set PK = nip
        DB::statement('ALTER TABLE saldo_cutis ADD PRIMARY KEY (nip)');

        // 7. Kembalikan FK
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->foreign('nip')->references('nip')->on('pegawais')->cascadeOnDelete();
            $table->foreign('jenis_cuti')->references('kode')->on('jenis_cutis');
        });
    }

    public function down(): void
    {
        // 0. Drop FKs
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->dropForeign('saldo_cutis_nip_foreign');
            $table->dropForeign('saldo_cutis_jenis_cuti_foreign');
        });

        // 1. Drop PK nip
        DB::statement('ALTER TABLE saldo_cutis DROP PRIMARY KEY');

        // 2. Tambah kolom lama
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->string('label_tahun')->nullable()->after('jenis_cuti');
            $table->integer('saldo')->default(0)->after('label_tahun');
            $table->string('keterangan')->nullable()->after('saldo');
            $table->integer('tahun')->nullable()->after('keterangan');
        });

        // 3. Migrasi balik: 1 baris jadi 3 baris
        DB::statement("
            INSERT INTO saldo_cutis (nip, jenis_cuti, label_tahun, saldo, keterangan, created_at, updated_at)
            SELECT nip, jenis_cuti, 'N-2', saldo_n2, keterangan_n2, NOW(), NOW() FROM saldo_cutis
        ");
        DB::statement("
            INSERT INTO saldo_cutis (nip, jenis_cuti, label_tahun, saldo, keterangan, created_at, updated_at)
            SELECT nip, jenis_cuti, 'N-1', saldo_n1, keterangan_n1, NOW(), NOW() FROM saldo_cutis
        ");
        DB::statement("
            INSERT INTO saldo_cutis (nip, jenis_cuti, label_tahun, saldo, keterangan, created_at, updated_at)
            SELECT nip, jenis_cuti, 'N', saldo_n, keterangan_n, NOW(), NOW() FROM saldo_cutis
        ");

        // 4. Hapus baris baru (yang kolom barunya masih ada)
        DB::statement('DELETE FROM saldo_cutis WHERE label_tahun IS NULL');

        // 5. Hapus kolom baru
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->dropColumn(['saldo_n2', 'saldo_n1', 'saldo_n', 'keterangan_n2', 'keterangan_n1', 'keterangan_n']);
        });

        // 6. Kembalikan composite PK
        DB::statement('ALTER TABLE saldo_cutis MODIFY COLUMN label_tahun VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE saldo_cutis ADD PRIMARY KEY (nip, jenis_cuti, label_tahun)');

        // 7. Kembalikan FK
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->foreign('nip')->references('nip')->on('pegawais')->cascadeOnDelete();
            $table->foreign('jenis_cuti')->references('kode')->on('jenis_cutis');
        });
    }
};
