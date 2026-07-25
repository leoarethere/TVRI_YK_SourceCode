<?php

namespace App\Http\Controllers;

use App\Models\AksesInformasi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DashboardAksesInformasiController extends Controller
{
    /**
     * Tampilkan halaman pengaturan akses informasi.
     */
    public function index()
    {
        $internal = AksesInformasi::internal();
        $publik   = AksesInformasi::publik();

        return view('backend.akses-informasi.index', compact('internal', 'publik'));
    }

    /**
     * Perbarui link akses informasi (internal atau publik).
     */
    public function update(Request $request, AksesInformasi $aksesInformasi)
    {
        $validated = $request->validate([
            'url'       => 'nullable|url|max:2048',
            'label'     => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        // Pastikan checkbox yang tidak terkirim dianggap false
        $validated['is_active'] = $request->boolean('is_active');

        $aksesInformasi->update($validated);

        return back()->with('success', 'Link "' . ucfirst($aksesInformasi->type) . '" berhasil diperbarui!');
    }
}
