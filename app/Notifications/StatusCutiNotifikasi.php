<?php

namespace App\Notifications;

use App\Models\PengajuanCuti;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatusCutiNotifikasi extends Notification
{
    use Queueable;

    public function __construct(
        public PengajuanCuti $cuti,
        public string $pesanTambahan = '',
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $cuti = $this->cuti;
        $pegawai = $cuti->pegawai;
        $status = $cuti->status;

        $statusLabel = match ($status) {
            'diajukan' => 'Diajukan',
            'diproses_kasubag' => 'Diproses Kasubag Umum',
            'diproses_sekretaris' => 'Diproses Sekretaris',
            'diproses_kepala_dinas' => 'Diproses Kepala Dinas',
            'diproses_walikota' => 'Diproses Wali Kota',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            default => $status,
        };

        $mail = (new MailMessage)
            ->subject('Notifikasi Status Cuti - ' . $statusLabel)
            ->greeting('Halo, ' . $pegawai->nama)
            ->line('Pengajuan cuti Anda dengan detail berikut:')
            ->line('Jenis Cuti: ' . $cuti->jenisCuti->nama)
            ->line('Tanggal: ' . $cuti->tanggal_mulai->format('d M Y') . ' s.d. ' . $cuti->tanggal_selesai->format('d M Y'))
            ->line('Lama: ' . $cuti->lama_cuti_hari . ' hari')
            ->line('Status: **' . $statusLabel . '**');

        if ($this->pesanTambahan) {
            $mail->line('Keterangan: ' . $this->pesanTambahan);
        }

        if ($status === 'disetujui') {
            $mail->action('Lihat Detail', route('cuti.show', $cuti))
                ->line('Pengajuan cuti Anda telah disetujui.');
        } elseif ($status === 'ditolak') {
            $mail->line('Pengajuan cuti Anda telah ditolak.');
        } else {
            $mail->action('Lihat Detail', route('cuti.show', $cuti));
        }

        return $mail;
    }
}
