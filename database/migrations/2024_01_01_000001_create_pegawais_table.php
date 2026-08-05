<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nip')->unique();
            $table->string('jabatan')->nullable();
            $table->string('unit_kerja')->nullable();
            $table->string('masa_kerja')->nullable(); // contoh: "14 tahun 3 bulan"
            $table->string('alamat')->nullable();
            $table->string('no_telpon')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
