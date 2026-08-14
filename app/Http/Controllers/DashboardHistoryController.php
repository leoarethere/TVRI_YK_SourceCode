<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Support\PublicStorageWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class DashboardHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $histories = History::latest()->paginate(10);

        return view('backend.sejarah.index', [
            'histories' => $histories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('backend.sejarah.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi Input
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'action' => 'required|in:draft,publish',
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }

        // Process & Compress Image
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                $filename = Str::random(40).'.jpg';
                $path = 'history-images/'.$filename;

                $manager = new ImageManager(new Driver);
                $image = $manager->read($file);
                $image->scale(width: 1200);
                $encodedImage = $image->toJpeg(quality: 75);
                PublicStorageWriter::put($path, (string) $encodedImage);

                $validated['image'] = $path;

            } catch (\Exception $e) {
                Log::error('History image processing error: '.$e->getMessage());

                return back()
                    ->withErrors(['image' => 'Gagal memproses gambar. Silakan coba lagi.'])
                    ->withInput();
            }
        }

        // Set status based on action
        if ($request->input('action') === 'publish') {
            $validated['status'] = 'published';
            $validated['published_at'] = now();
        } else {
            $validated['status'] = 'draft';
            $validated['published_at'] = null;
        }

        // Save to database
        try {
            History::create($validated);
        } catch (\Exception $e) {
            Log::error('History creation error: '.$e->getMessage());

            if (isset($validated['image'])) {
                Storage::disk('public')->delete($validated['image']);
            }

            return back()
                ->withErrors(['error' => 'Gagal menyimpan data sejarah. Silakan coba lagi.'])
                ->withInput();
        }

        $message = $validated['status'] === 'published'
            ? 'Data sejarah berhasil dipublikasikan!'
            : 'Data sejarah berhasil disimpan sebagai draft!';

        return redirect()->route('dashboard.sejarah.index')
            ->with('success', $message);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(History $history): View
    {
        return view('backend.sejarah.edit', compact('history'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, History $history): RedirectResponse
    {
        // Validasi Input
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'action' => 'required|in:draft,publish',
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }

        $oldImagePath = $history->image;

        // Process & Compress New Image
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                $filename = Str::random(40).'.jpg';
                $path = 'history-images/'.$filename;

                $manager = new ImageManager(new Driver);
                $image = $manager->read($file);
                $image->scale(width: 1200);
                $encodedImage = $image->toJpeg(quality: 75);
                PublicStorageWriter::put($path, (string) $encodedImage);

                $validated['image'] = $path;

            } catch (\Exception $e) {
                Log::error('History image processing error: '.$e->getMessage());

                return back()
                    ->withErrors(['image' => 'Gagal memproses gambar. Silakan coba lagi.'])
                    ->withInput();
            }
        }

        // Set status based on action
        if ($request->input('action') === 'publish') {
            $validated['status'] = 'published';
            $validated['published_at'] = $history->published_at ?? now();
        } else {
            $validated['status'] = 'draft';
            $validated['published_at'] = null;
        }

        // Update database
        try {
            $history->update($validated);
        } catch (\Exception $e) {
            Log::error('History update error: '.$e->getMessage());

            if (isset($validated['image'])) {
                Storage::disk('public')->delete($validated['image']);
            }

            return back()
                ->withErrors(['error' => 'Gagal memperbarui data sejarah. Silakan coba lagi.'])
                ->withInput();
        }

        if (isset($validated['image']) && $oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }

        $message = $validated['status'] === 'published'
            ? 'Data sejarah berhasil diperbarui!'
            : 'Perubahan berhasil disimpan sebagai draft!';

        return redirect()->route('dashboard.sejarah.index')
            ->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(History $history): RedirectResponse
    {
        try {
            $imagePath = $history->image;
            $history->delete();

            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            return redirect()->route('dashboard.sejarah.index')
                ->with('success', 'Data sejarah berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('History deletion error: '.$e->getMessage());

            return redirect()->route('dashboard.sejarah.index')
                ->with('error', 'Gagal menghapus data sejarah. Silakan coba lagi.');
        }
    }
}
