<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function tandaiDibaca(Request $request, string $notifikasi): RedirectResponse
    {
        $notifikasi = $request->user()
            ->unreadNotifications()
            ->whereKey($notifikasi)
            ->firstOrFail();
        $notifikasi->markAsRead();

        $pengajuanId = $notifikasi->data['pengajuan_id'] ?? null;
        $pengajuan = is_numeric($pengajuanId)
            ? $request->user()->pengajuanSurat()->find($pengajuanId)
            : null;

        return $pengajuan
            ? redirect()->route('warga.pengajuan.show', $pengajuan)
            : back()->with('status', 'Notifikasi ditandai sudah dibaca.');
    }
}
