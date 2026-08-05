<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaranCuti extends Model
{
    protected $fillable = [
        'nip',
        'kesulitan_menu',
        'pengajuan_gagal',
        'file_gagal',
        'halaman_lambat',
        'saran',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nip');
    }
}
