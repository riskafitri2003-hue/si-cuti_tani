<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop foreign keys first
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
        });

        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
            $table->dropForeign(['jenis_cuti_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
        });

        // 2. pegawais: remove auto_increment, drop unique index on nip, drop pk, drop id, make nip PK
        DB::statement('ALTER TABLE pegawais MODIFY COLUMN id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE pegawais DROP INDEX pegawais_nip_unique');
        DB::statement('ALTER TABLE pegawais DROP PRIMARY KEY');
        DB::statement('ALTER TABLE pegawais DROP COLUMN id');
        DB::statement('ALTER TABLE pegawais ADD PRIMARY KEY (nip)');

        // 3. jenis_cutis: remove auto_increment, then rename id -> jenis_cuti_id, re-add AI
        DB::statement('ALTER TABLE jenis_cutis MODIFY COLUMN id BIGINT UNSIGNED NOT NULL');
        Schema::table('jenis_cutis', function (Blueprint $table) {
            $table->renameColumn('id', 'jenis_cuti_id');
        });
        DB::statement('ALTER TABLE jenis_cutis MODIFY COLUMN jenis_cuti_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');

        // 4. saldo_cutis: remove AI, rename id -> kode_cuti, re-add AI, change pegawai_id
        DB::statement('ALTER TABLE saldo_cutis MODIFY COLUMN id BIGINT UNSIGNED NOT NULL');
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->renameColumn('id', 'kode_cuti');
        });
        DB::statement('ALTER TABLE saldo_cutis MODIFY COLUMN kode_cuti BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->string('pegawai_id')->change();
            $table->foreign('pegawai_id')->references('nip')->on('pegawais')->cascadeOnDelete();
        });

        // 5. pengajuan_cutis: remove AI, rename id -> pengajuan_cuti_id, re-add AI, change pegawai_id
        DB::statement('ALTER TABLE pengajuan_cutis MODIFY COLUMN id BIGINT UNSIGNED NOT NULL');
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->renameColumn('id', 'pengajuan_cuti_id');
        });
        DB::statement('ALTER TABLE pengajuan_cutis MODIFY COLUMN pengajuan_cuti_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->string('pegawai_id')->change();
            $table->foreign('pegawai_id')->references('nip')->on('pegawais')->cascadeOnDelete();
            $table->foreign('jenis_cuti_id')->references('jenis_cuti_id')->on('jenis_cutis');
        });

        // 6. users: remove AI, rename id -> user_id, re-add AI, change pegawai_id
        DB::statement('ALTER TABLE users MODIFY COLUMN id BIGINT UNSIGNED NOT NULL');
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('id', 'user_id');
        });
        DB::statement('ALTER TABLE users MODIFY COLUMN user_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        Schema::table('users', function (Blueprint $table) {
            $table->string('pegawai_id')->nullable()->change();
            $table->foreign('pegawai_id')->references('nip')->on('pegawais')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
            $table->dropColumn('user_id');
            $table->id();
        });

        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
            $table->dropForeign(['jenis_cuti_id']);
            $table->dropColumn('pengajuan_cuti_id');
            $table->id();
        });

        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
            $table->dropColumn('kode_cuti');
            $table->id();
        });

        Schema::table('jenis_cutis', function (Blueprint $table) {
            $table->renameColumn('jenis_cuti_id', 'id');
        });

        DB::statement('ALTER TABLE pegawais DROP PRIMARY KEY');
        DB::statement('ALTER TABLE pegawais ADD COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');

        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->unsignedBigInteger('pegawai_id')->change();
            $table->foreign('pegawai_id')->references('id')->on('pegawais')->cascadeOnDelete();
        });

        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->unsignedBigInteger('pegawai_id')->change();
            $table->foreign('pegawai_id')->references('id')->on('pegawais')->cascadeOnDelete();
            $table->foreign('jenis_cuti_id')->references('id')->on('jenis_cutis');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('pegawai_id')->nullable()->change();
            $table->foreign('pegawai_id')->references('id')->on('pegawais')->nullOnDelete();
        });
    }
};
