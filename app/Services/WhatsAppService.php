<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\PengajuanCuti;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function send(string $phone, string $message, ?string $token = null): bool
    {
        $phone = $this->normalizePhone($phone);

        if (! $phone) {
            Log::warning('WhatsApp: nomor tujuan kosong.', ['phone' => $phone]);
            return false;
        }

        $token = $token ?: config('services.fonnte.token');

        if (! $token) {
            Log::warning('WhatsApp: FONNTE_TOKEN belum diatur.');
            return false;
        }

        try {
            $response = Http::withHeaders(['Authorization' => $token])
                ->asMultipart()
                ->post(config('services.fonnte.base_url'), [
                    'target' => $phone,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            if ($response->failed()) {
                Log::error('WhatsApp: gagal mengirim.', [
                    'phone' => $phone,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }

            Log::info('WhatsApp: pesan terkirim.', ['phone' => $phone, 'body' => $response->body()]);
            return true;
        } catch (\Throwable $e) {
            Log::error('WhatsApp: exception saat mengirim.', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($phone, '62')) {
            return $phone;
        }

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        return $phone ? '62' . $phone : '';
    }

    public function sendToUser(User $user, string $message, ?string $token = null): bool
    {
        $pegawai = $user->pegawai;
        $phone = $pegawai->wa ?? $user->wa ?? null;

        if (! $phone) {
            Log::warning('WhatsApp: nomor WA tidak ditemukan untuk user.', ['user_id' => $user->user_id]);
            return false;
        }

        $deviceToken = $pegawai->fonnte_device_id ?? $token ?? config('services.fonnte.token');

        return $this->send($phone, $message, $deviceToken);
    }

    public function sendToPegawai(Pegawai $pegawai, string $message): bool
    {
        if (! $pegawai->wa) {
            Log::warning('WhatsApp: nomor WA tidak ditemukan untuk pegawai.', ['nip' => $pegawai->nip]);
            return false;
        }

        return $this->send($pegawai->wa, $message, $pegawai->fonnte_device_id);
    }

    public function kirimKeApprover(PengajuanCuti $cuti, User $approver, string $pesan): bool
    {
        if (! $approver) {
            return false;
        }

        return $this->sendToUser($approver, $pesan);
    }
}
