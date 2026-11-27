<?php

namespace App\Notifications;

use App\Enums\StatusPengajuan;
use App\Models\PengajuanSurat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StatusPengajuanNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly PengajuanSurat $pengajuan,
        private readonly StatusPengajuan $status,
        private readonly ?string $catatan = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Status pengajuan diperbarui',
            'message' => "Pengajuan {$this->pengajuan->kode_pengajuan} berstatus {$this->status->label()}.",
            'pengajuan_id' => $this->pengajuan->id,
            'kode_pengajuan' => $this->pengajuan->kode_pengajuan,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'catatan' => $this->catatan,
        ];
    }
}
