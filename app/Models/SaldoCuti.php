<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoCuti extends Model
{
    protected $primaryKey = 'nip';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['nip', 'jenis_cuti', 'saldo_n2', 'saldo_n1', 'saldo_n', 'keterangan_n2', 'keterangan_n1', 'keterangan_n'];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nip');
    }

    public function jenisCuti()
    {
        return $this->belongsTo(JenisCuti::class, 'jenis_cuti', 'kode');
    }
}
