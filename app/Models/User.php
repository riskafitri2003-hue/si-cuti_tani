<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'nip',
        'password',
        'role',
        'email',
        'wa',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nip');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPegawai(): bool
    {
        return $this->role === 'pegawai';
    }

    public function isAtasan(): bool
    {
        return $this->role === 'atasan';
    }

    public function isPejabat(): bool
    {
        return $this->role === 'pejabat';
    }

    public function isAtasanLangsung(): bool
    {
        return $this->role === 'atasan_langsung';
    }

    public function isKasubag(): bool
    {
        return $this->role === 'kasubag';
    }

    public function isSekretaris(): bool
    {
        return $this->role === 'sekretaris';
    }

    public function isKepalaDinas(): bool
    {
        return $this->role === 'kepala_dinas';
    }

    public function isWalikota(): bool
    {
        return $this->role === 'walikota';
    }
}
