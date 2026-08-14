<?php

namespace App\Http\Controllers;

use App\Models\NewsCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class DashboardNewsCategoryController extends Controller
{
    /**
     * Simpan kategori berita baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:news_categories,slug',
            'color' => 'nullable|string|in:blue,red,green,yellow,purple,gray',
        ]);

        NewsCategory::create($validated);

        return back()->with('success', 'Kategori berita berhasil ditambahkan!');
    }

    /**
     * Update kategori berita.
     */
    public function update(Request $request, NewsCategory $newsCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('news_categories')->ignore($newsCategory),
            ],
            'color' => 'nullable|string|in:blue,red,green,yellow,purple,gray',
        ]);

        $newsCategory->update($validated);

        return back()->with('success', 'Kategori berita berhasil diperbarui!');
    }

    /**
     * Hapus kategori berita.
     */
    public function destroy(NewsCategory $newsCategory): RedirectResponse
    {
        // Cek apakah kategori masih dipakai di berita
        if ($newsCategory->news()->count() > 0) {
            return back()->with('error', 'Kategori ini masih digunakan oleh beberapa berita.');
        }

        $newsCategory->delete();

        return back()->with('success', 'Kategori berita berhasil dihapus!');
    }
}
