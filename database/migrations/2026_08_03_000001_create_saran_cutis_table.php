<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saran_cutis', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->foreign('nip')->references('nip')->on('pegawais')->onDelete('cascade');
            $table->boolean('kesulitan_menu')->default(false);
            $table->boolean('pengajuan_gagal')->default(false);
            $table->boolean('file_gagal')->default(false);
            $table->boolean('halaman_lambat')->default(false);
            $table->text('saran')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saran_cutis');
    }
};
