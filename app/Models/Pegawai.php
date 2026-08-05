<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $primaryKey = 'nip';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nama', 'nip', 'jabatan', 'unit_kerja', 'masa_kerja', 'alamat', 'no_telpon',
        'email', 'wa', 'fonnte_device_id',
    ];

    public function saldoCutis()
    {
        return $this->hasMany(SaldoCuti::class, 'nip', 'nip');
    }

    public function pengajuanCutis()
    {
        return $this->hasMany(PengajuanCuti::class, 'nip', 'nip');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'nip', 'nip');
    }
}
