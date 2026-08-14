<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class DashboardPostCategoryController extends Controller
{
    /**
     * Menyimpan kategori baru dari modal Postingan.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'color' => 'required|string|in:blue,pink,green,yellow,indigo,purple,red,gray',
        ]);

        Category::create($validated);

        // Redirect kembali ke halaman index Postingan
        return redirect()->route('dashboard.postingan.index')
            ->with('success', 'Kategori postingan baru berhasil ditambahkan!');
    }

    /**
     * Update kategori dari modal Postingan.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category)],
            'slug' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category)],
            'color' => 'required|string|in:blue,pink,green,yellow,indigo,purple,red,gray',
        ]);

        $category->update($validated);

        // Redirect kembali ke halaman index Postingan
        return redirect()->route('dashboard.postingan.index')
            ->with('success', 'Kategori postingan berhasil diperbarui!');
    }

    /**
     * Hapus kategori dari modal Postingan.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $postCount = $category->posts()->count();

        if ($postCount > 0) {
            return redirect()->route('dashboard.postingan.index')
                ->with('error', 'Kategori ini masih memiliki '.$postCount.' data postingan.');
        }

        $category->delete();

        // Redirect kembali ke halaman index Postingan
        return redirect()->route('dashboard.postingan.index')
            ->with('success', 'Kategori postingan berhasil dihapus!');
    }
}
