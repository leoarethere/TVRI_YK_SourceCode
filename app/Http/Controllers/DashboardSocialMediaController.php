<?php

namespace App\Http\Controllers;

use App\Models\SocialMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardSocialMediaController extends Controller
{
    /**
     * Menampilkan data sosial media (Read Only).
     */
    public function index(): View
    {
        $socialMedia = SocialMedia::firstOrCreate([], [
            'instagram' => null,
            'facebook' => null,
            'twitter' => null,
            'tiktok' => null,
            'youtube' => null,
        ]);

        if ($socialMedia->wasRecentlyCreated) {
            Cache::forget('global_social_media');
        }

        return view('backend.social-media.index', compact('socialMedia'));
    }

    /**
     * Menampilkan form edit.
     */
    public function edit(SocialMedia $socialMedia): View
    {
        return view('backend.social-media.edit', compact('socialMedia'));
    }

    /**
     * Memproses update data.
     */
    public function update(Request $request, SocialMedia $socialMedia): RedirectResponse
    {
        $validated = $request->validate([
            'instagram' => 'nullable|url',
            'twitter'   => 'nullable|url',
            'facebook'  => 'nullable|url',
            'tiktok'    => 'nullable|url',
            'youtube'   => 'nullable|url',
        ], [
            // Opsional: Custom error message jika URL tidak valid
            'instagram.url' => 'Link Instagram harus berupa URL valid (awalan http:// atau https://)',
            'twitter.url'   => 'Link X (Twitter) harus berupa URL valid',
            'facebook.url'  => 'Link Facebook harus berupa URL valid',
            'tiktok.url'    => 'Link TikTok harus berupa URL valid',
            'youtube.url'   => 'Link YouTube harus berupa URL valid',
        ]);

        $socialMedia->update($validated);
        
        // Hapus cache agar frontend segera mendapat perubahan
        Cache::forget('global_social_media'); 

        return redirect()->route('dashboard.social-media.index')
            ->with('success', 'Link sosial media berhasil diperbarui!');
    }
}
