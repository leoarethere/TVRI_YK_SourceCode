<?php

namespace App\Http\Controllers;

use App\Models\BroadcastCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class DashboardBroadcastCategoryController extends Controller
{
    /**
     * Menyimpan kategori baru dari modal.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:broadcast_categories,name',
            'slug' => 'required|string|max:255|unique:broadcast_categories,slug',
            'color' => 'required|string|in:blue,pink,green,yellow,indigo,purple,red,gray',
        ]);

        BroadcastCategory::create($validated);

        return redirect()->route('dashboard.penyiaran.index')
            ->with('success', 'Kategori penyiaran baru berhasil ditambahkan!');
    }

    /**
     * Update kategori dari modal.
     */
    public function update(Request $request, BroadcastCategory $broadcastCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('broadcast_categories')->ignore($broadcastCategory)],
            'slug' => ['required', 'string', 'max:255', Rule::unique('broadcast_categories')->ignore($broadcastCategory)],
            'color' => 'required|string|in:blue,pink,green,yellow,indigo,purple,red,gray',
        ]);

        $broadcastCategory->update($validated);

        return redirect()->route('dashboard.penyiaran.index')
            ->with('success', 'Kategori penyiaran berhasil diperbarui!');
    }

    /**
     * Hapus kategori dari modal.
     */
    public function destroy(BroadcastCategory $broadcastCategory): RedirectResponse
    {
        $broadcastCategory->loadCount(['broadcasts', 'jadwalAcaras']);

        if ($broadcastCategory->broadcasts_count > 0 || $broadcastCategory->jadwal_acaras_count > 0) {
            $usage = [];

            if ($broadcastCategory->broadcasts_count > 0) {
                $usage[] = $broadcastCategory->broadcasts_count.' data penyiaran';
            }

            if ($broadcastCategory->jadwal_acaras_count > 0) {
                $usage[] = $broadcastCategory->jadwal_acaras_count.' jadwal acara';
            }

            return redirect()->route('dashboard.penyiaran.index')
                ->with('error', 'Kategori ini masih digunakan oleh '.implode(' dan ', $usage).'.');
        }

        try {
            $broadcastCategory->delete();
        } catch (QueryException $exception) {
            report($exception);

            return redirect()->route('dashboard.penyiaran.index')
                ->with('error', 'Kategori masih digunakan oleh data lain.');
        }

        return redirect()->route('dashboard.penyiaran.index')
            ->with('success', 'Kategori penyiaran berhasil dihapus!');
    }
}
