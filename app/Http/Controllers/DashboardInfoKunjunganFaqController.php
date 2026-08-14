<?php

namespace App\Http\Controllers;

use App\Models\InfoKunjunganFaq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class DashboardInfoKunjunganFaqController extends Controller
{
    /**
     * Menampilkan daftar FAQ Kunjungan (Backend).
     */
    public function index(): View
    {
        // Ambil data diurutkan berdasarkan 'order' (terkecil ke terbesar)
        $faqs = InfoKunjunganFaq::orderBy('order', 'asc')->paginate(10);

        return view('backend.info-kunjungan-faq.index', compact('faqs'));
    }

    /**
     * Menampilkan form tambah FAQ baru.
     */
    public function create(): View
    {
        return view('backend.info-kunjungan-faq.create');
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

        // Checkbox handling
        $validated['is_active'] = $request->has('is_active');

        InfoKunjunganFaq::create($validated);

        return redirect()->route('dashboard.info-kunjungan-faq.index')
            ->with('success', 'FAQ Kunjungan berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit FAQ.
     */
    public function edit(InfoKunjunganFaq $infoKunjunganFaq): View
    {
        $faq = $infoKunjunganFaq;

        return view('backend.info-kunjungan-faq.edit', compact('faq'));
    }

    /**
     * Memperbarui data FAQ.
     */
    public function update(Request $request, InfoKunjunganFaq $infoKunjunganFaq): RedirectResponse
    {
        $validated = $request->validate([
            'question'  => 'required|string|max:255',
            'answer'    => 'required|string',
            'order'     => 'required|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $infoKunjunganFaq->update($validated);

        return redirect()->route('dashboard.info-kunjungan-faq.index')
            ->with('success', 'FAQ Kunjungan berhasil diperbarui!');
    }

    /**
     * Menghapus FAQ.
     */
    public function destroy(InfoKunjunganFaq $infoKunjunganFaq): RedirectResponse
    {
        $infoKunjunganFaq->delete();

        return redirect()->route('dashboard.info-kunjungan-faq.index')
            ->with('success', 'FAQ Kunjungan berhasil dihapus!');
    }
}
