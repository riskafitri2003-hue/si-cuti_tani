<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE saldo_cutis CHANGE COLUMN `jenis_cuti.id` `jenis_cuti` TINYINT(3) UNSIGNED NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE saldo_cutis CHANGE COLUMN `jenis_cuti` `jenis_cuti.id` TINYINT(3) UNSIGNED NOT NULL');
    }
};
