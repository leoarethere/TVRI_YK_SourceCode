<?php

// [PERBAIKAN] Namespace sudah benar, tidak lagi di dalam sub-folder 'Dashboard'
namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\BroadcastCategory;
use Illuminate\Routing\Controller;

class DashboardBroadcastCategoryController extends Controller
{
    /**
     * Menyimpan kategori baru dari modal.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:broadcast_categories,name',
            'slug' => 'required|string|max:255|unique:broadcast_categories,slug',
            'color' => 'required|string|in:blue,pink,green,yellow,indigo,purple,red,gray',
        ]);

        BroadcastCategory::create($validated);

        return redirect()->route('dashboard.penyiaran.index')
                         ->with('category_success', 'Kategori penyiaran baru berhasil ditambahkan!');
    }

    /**
     * Update kategori dari modal.
     */
    public function update(Request $request, BroadcastCategory $broadcastCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:broadcast_categories,name,' . $broadcastCategory->id,
            'slug' => 'required|string|max:255|unique:broadcast_categories,slug,' . $broadcastCategory->id,
            'color' => 'required|string|in:blue,pink,green,yellow,indigo,purple,red,gray',
        ]);

        $broadcastCategory->update($validated);

        return redirect()->route('dashboard.penyiaran.index')
                         ->with('category_success', 'Kategori penyiaran berhasil diperbarui!');
    }

    /**
     * Hapus kategori dari modal.
     */
    public function destroy(BroadcastCategory $broadcastCategory)
    {
        // Cek jika kategori masih memiliki broadcast
        if ($broadcastCategory->broadcasts()->count() > 0) {
            return redirect()->route('dashboard.penyiaran.index')
                             ->with('category_error', 'Gagal! Kategori ini masih memiliki ' . $broadcastCategory->broadcasts()->count() . ' data penyiaran.');
        }

        $broadcastCategory->delete();

        return redirect()->route('dashboard.penyiaran.index')
                         ->with('category_success', 'Kategori penyiaran berhasil dihapus!');
    }
}