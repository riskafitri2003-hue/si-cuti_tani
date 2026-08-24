<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->integer('potongan_saldo_n2')->default(0)->after('lama_cuti_hari');
            $table->integer('potongan_saldo_n1')->default(0)->after('potongan_saldo_n2');
            $table->integer('potongan_saldo_n')->default(0)->after('potongan_saldo_n1');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_cutis', function (Blueprint $table) {
            $table->dropColumn(['potongan_saldo_n2', 'potongan_saldo_n1', 'potongan_saldo_n']);
        });
    }
};
