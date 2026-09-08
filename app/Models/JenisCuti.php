<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisCuti extends Model
{
    protected $primaryKey = 'kode';
    protected $keyType = 'int';
    public $incrementing = false;

    protected $fillable = ['kode', 'nama'];

    public function pengajuanCutis()
    {
        return $this->hasMany(PengajuanCuti::class);
    }

    public function isCutiBerpotonganSaldo(): bool
    {
        return (int) $this->kode === 1;
    }
}
