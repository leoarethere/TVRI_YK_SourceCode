<?php

namespace App\Http\Controllers;

use App\Models\InfoMagangFaq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class DashboardInfoMagangFaqController extends Controller
{
    /**
     * Menampilkan daftar FAQ (Backend).
     */
    public function index(): View
    {
        // Ambil data diurutkan berdasarkan 'order' (terkecil ke terbesar)
        $faqs = InfoMagangFaq::orderBy('order', 'asc')->paginate(10);

        // Kita asumsikan view-nya akan disimpan di folder backend/info-magang-faq
        return view('backend.info-magang-faq.index', compact('faqs'));
    }

    /**
     * Menampilkan form tambah FAQ baru.
     */
    public function create(): View
    {
        return view('backend.info-magang-faq.create');
    }

    /**
     * Menyimpan data FAQ baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question'  => 'required|string|max:255',
            'answer'    => 'required|string',
            'order'     => 'required|integer|min:0',
        ]);

        // Checkbox HTML mengembalikan value "1" atau null (tidak terkirim)
        // Kita gunakan $request->has() atau boolean() untuk menangkapnya
        $validated['is_active'] = $request->has('is_active');

        InfoMagangFaq::create($validated);

        return redirect()->route('dashboard.info-magang-faq.index')
            ->with('success', 'FAQ berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit FAQ.
     */
    public function edit(InfoMagangFaq $infoMagangFaq): View
    {
        $faq = $infoMagangFaq;

        return view('backend.info-magang-faq.edit', compact('faq'));
    }

    /**
     * Memperbarui data FAQ.
     */
    public function update(Request $request, InfoMagangFaq $infoMagangFaq): RedirectResponse
    {
        $validated = $request->validate([
            'question'  => 'required|string|max:255',
            'answer'    => 'required|string',
            'order'     => 'required|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $infoMagangFaq->update($validated);

        return redirect()->route('dashboard.info-magang-faq.index')
            ->with('success', 'FAQ berhasil diperbarui!');
    }

    /**
     * Menghapus FAQ.
     */
    public function destroy(InfoMagangFaq $infoMagangFaq): RedirectResponse
    {
        $infoMagangFaq->delete();

        return redirect()->route('dashboard.info-magang-faq.index')
            ->with('success', 'FAQ berhasil dihapus!');
    }
}
