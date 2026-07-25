<?php

// [PERBAIKAN] Namespace sudah benar
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller; // Menggunakan model Category (untuk Postingan)


class DashboardPostCategoryController extends Controller
{
    /**
     * Menyimpan kategori baru dari modal Postingan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'color' => 'required|string|in:blue,pink,green,yellow,indigo,purple,red,gray',
        ]);

        Category::create($validated);

        // Redirect kembali ke halaman index Postingan
        return redirect()->route('dashboard.postingan.index')
                         ->with('category_success', 'Kategori postingan baru berhasil ditambahkan!');
    }

    /**
     * Update kategori dari modal Postingan.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'color' => 'required|string|in:blue,pink,green,yellow,indigo,purple,red,gray',
        ]);

        $category->update($validated);

        // Redirect kembali ke halaman index Postingan
        return redirect()->route('dashboard.postingan.index')
                         ->with('category_success', 'Kategori postingan berhasil diperbarui!');
    }

    /**
     * Hapus kategori dari modal Postingan.
     */
    public function destroy(Category $category)
    {
        // Cek jika kategori masih memiliki postingan
        if ($category->posts()->count() > 0) {
return redirect()->route('dashboard.postingan.index')
                              ->with('category_error', 'Gagal! Kategori ini masih memiliki ' . $category->posts()->count() . ' data postingan.');
        }

        $category->delete();

        // Redirect kembali ke halaman index Postingan
        return redirect()->route('dashboard.postingan.index')
                         ->with('category_success', 'Kategori postingan berhasil dihapus!');
    }
}