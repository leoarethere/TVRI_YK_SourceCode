<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class SymlinkController extends Controller
{
    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Hanya admin utama yang dapat menjalankan perintah ini.');
        }

        try {
            Artisan::call('storage:link');
            return 'Symlink berhasil dibuat! Silakan cek folder public/storage.';
        } catch (\Exception $e) {
            return 'Gagal membuat symlink: ' . $e->getMessage();
        }
    }
}
