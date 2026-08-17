<?php

namespace App\Http\Controllers;

use App\Models\JadwalCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class DashboardJadwalCategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:jadwal_categories,slug',
            'color' => ['required', 'string', Rule::in(['blue', 'pink', 'green', 'yellow', 'indigo', 'purple', 'red', 'gray'])],
            'order' => 'required|integer',
        ]);

        JadwalCategory::create($validated);

        return back()->with('success', 'Hari/Kategori baru berhasil ditambahkan!');
    }

    public function update(Request $request, JadwalCategory $jadwalCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('jadwal_categories')->ignore($jadwalCategory)],
            'color' => ['required', 'string', Rule::in(['blue', 'pink', 'green', 'yellow', 'indigo', 'purple', 'red', 'gray'])],
            'order' => 'required|integer',
        ]);

        $jadwalCategory->update($validated);

        return back()->with('success', 'Hari/Kategori berhasil diperbarui!');
    }

    public function destroy(JadwalCategory $jadwalCategory): RedirectResponse
    {
        // Cek jika kategori masih memiliki jadwal acara
        $jadwalCount = $jadwalCategory->jadwalAcaras()->count();

        if ($jadwalCount > 0) {
            return back()->with('error', 'Kategori ini masih memiliki '.$jadwalCount.' jadwal acara.');
        }

        $jadwalCategory->delete();

        return back()->with('success', 'Hari/Kategori berhasil dihapus!');
    }
}
