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

    public const ROLE_LABELS = [
        'admin' => 'Admin',
        'pegawai' => 'Pegawai',
        'atasan_langsung' => 'Atasan Langsung',
        'kasubag' => 'Kasubag Umum',
        'sekretaris' => 'Sekretaris',
        'kepala_dinas' => 'Kepala Dinas',
        'sekda' => 'Sekretaris Daerah',
        'walikota' => 'Wali Kota',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nip');
    }

    /**
     * Daftar role yang dimiliki (role disimpan CSV, contoh: atasan_langsung,kasubag).
     */
    public function roleList(): array
    {
        if (! $this->role) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $this->role))));
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roleList(), true);
    }

    public function hasAnyRole(array $roles): bool
    {
        return ! empty(array_intersect($roles, $this->roleList()));
    }

    public function roleNames(): string
    {
        $labels = $this->roleList();
        if (empty($labels)) {
            return '-';
        }

        return implode(', ', array_map(fn ($r) => self::ROLE_LABELS[$r] ?? ucwords(str_replace('_', ' ', $r)), $labels));
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isPegawai(): bool
    {
        return $this->hasRole('pegawai');
    }

    public function isAtasan(): bool
    {
        return $this->hasRole('atasan');
    }

    public function isPejabat(): bool
    {
        return $this->hasRole('pejabat');
    }

    public function isAtasanLangsung(): bool
    {
        return $this->hasRole('atasan_langsung');
    }

    public function isKasubag(): bool
    {
        return $this->hasRole('kasubag');
    }

    public function isSekretaris(): bool
    {
        return $this->hasRole('sekretaris');
    }

    public function isKepalaDinas(): bool
    {
        return $this->hasRole('kepala_dinas');
    }

    public function isWalikota(): bool
    {
        return $this->hasRole('walikota');
    }

    public function isSekda(): bool
    {
        return $this->hasRole('sekda');
    }

    public function canBeAtasanLangsung(): bool
    {
        return $this->hasAnyRole(['atasan_langsung', 'kasubag', 'sekretaris', 'kepala_dinas', 'sekda', 'walikota']);
    }
}
