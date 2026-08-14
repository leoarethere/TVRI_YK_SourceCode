<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserSessionService;
use App\Support\LikeSearch;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardUserController extends Controller
{
    public function __construct(private readonly UserSessionService $sessions) {}

    // Menampilkan daftar user
    public function index(Request $request): View
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $query = User::query();

        // Fitur Pencarian (dibungkus grouping agar aman)
        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                LikeSearch::contains($q, 'name', $search);
                LikeSearch::contains($q, 'email', $search, 'or');
            });
        }

        // Tampilkan 10 user per halaman
        $users = $query->latest()->paginate(10)->withQueryString();

        return view('backend.users.index', compact('users'));
    }

    // Menampilkan form tambah user
    public function create(): View
    {
        return view('backend.users.create');
    }

    // Menyimpan user baru
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users', 'alpha_dash'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', 'in:admin,secondary,pegawai'],
        ]);

        $role = $request->role;

        // Hanya admin yang bisa membuat akun admin/pegawai
        if (! Auth::user()->isAdmin()) {
            $role = 'secondary';
        }

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);

        $label = $role === 'pegawai' ? 'Pegawai' : 'Admin';

        return redirect()->route('dashboard.users.index')
            ->with('success', "Akun {$label} baru berhasil ditambahkan!");
    }

    // Menampilkan form edit user
    public function edit(User $user): View
    {
        // Proteksi: Admin Secondary tidak bisa mengedit Akun Utama
        if (! Auth::user()->isAdmin() && $user->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah Akun Utama.');
        }

        return view('backend.users.edit', compact('user'));
    }

    // ✅ PERBAIKAN: Mengupdate data user
    public function update(Request $request, User $user): RedirectResponse
    {
        // Proteksi: Admin Secondary tidak bisa mengedit Akun Utama
        if (! Auth::user()->isAdmin() && $user->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah Akun Utama.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', 'min:8'],
            'role' => ['nullable', 'in:admin,secondary,pegawai'],
        ]);

        $dataToUpdate = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        if (Auth::id() === $user->id && $request->has('role') && $request->role !== 'admin') {
            return back()->withErrors([
                'error' => 'Anda tidak dapat menurunkan role akun sendiri. Gunakan Super Admin lain untuk perubahan ini.',
            ]);
        }

        // Role hanya bisa diubah oleh admin. Pemeriksaan "admin terakhir" dan
        // perubahan role harus berada dalam transaksi yang sama agar dua
        // permintaan bersamaan tidak dapat menurunkan dua admin terakhir.
        if (Auth::user()->isAdmin() && $request->has('role')) {
            $dataToUpdate['role'] = $request->role;
        }

        $passwordChanged = $request->filled('password');

        if ($passwordChanged) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        $blockedMessage = DB::transaction(function () use ($dataToUpdate, $passwordChanged, $user): ?string {
            $primaryAdminIds = User::query()
                ->where('role', 'admin')
                ->orderBy('id')
                ->lockForUpdate()
                ->pluck('id');

            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);
            $newRole = $dataToUpdate['role'] ?? $lockedUser->role;

            if ($lockedUser->isAdmin() && $newRole !== 'admin' && $primaryAdminIds->count() <= 1) {
                return 'Tidak dapat mengubah role. Harus ada setidaknya satu Super Admin di dalam sistem.';
            }

            $lockedUser->fill($dataToUpdate);

            if ($passwordChanged) {
                $lockedUser->setRememberToken(Str::random(60));
            }

            $lockedUser->save();

            return null;
        });

        if ($blockedMessage !== null) {
            return back()->withErrors(['error' => $blockedMessage]);
        }

        $user->refresh();

        if ($passwordChanged) {
            $this->sessions->revokeAll($user);
        }

        if ($passwordChanged && Auth::id() === $user->id) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('success', 'Kata sandi berhasil diperbarui. Silakan masuk kembali.');
        }

        return redirect()->route('dashboard.users.index')
            ->with('success', 'Data user berhasil diperbarui!');
    }

    // Menghapus user
    public function destroy(User $user): RedirectResponse
    {
        // Proteksi: Admin Secondary tidak bisa menghapus Akun Utama
        if (! Auth::user()->isAdmin() && $user->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus Akun Utama.');
        }

        // Proteksi: Tidak boleh menghapus diri sendiri
        if (Auth::user()->id === $user->id) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun sendiri yang sedang login.']);
        }

        try {
            $blockedMessage = DB::transaction(function () use ($user): ?string {
                $primaryAdminIds = User::query()
                    ->where('role', 'admin')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->pluck('id');

                $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);

                if ($lockedUser->isAdmin() && $primaryAdminIds->count() <= 1) {
                    return 'Tidak dapat menghapus user ini. Harus ada setidaknya satu Super Admin di dalam sistem.';
                }

                $lockedUser->loadCount(['posts', 'news', 'broadcasts']);
                $detail = [];

                if ($lockedUser->posts_count > 0) {
                    $detail[] = $lockedUser->posts_count.' postingan';
                }
                if ($lockedUser->news_count > 0) {
                    $detail[] = $lockedUser->news_count.' berita';
                }
                if ($lockedUser->broadcasts_count > 0) {
                    $detail[] = $lockedUser->broadcasts_count.' penyiaran';
                }

                if ($detail !== []) {
                    return 'Gagal menghapus! User ini masih memiliki '.implode(', ', $detail).'. Pindahkan atau hapus kontennya terlebih dahulu.';
                }

                $lockedUser->delete();

                return null;
            });
        } catch (QueryException $exception) {
            report($exception);

            return back()->withErrors([
                'error' => 'Gagal menghapus! User masih terhubung dengan data lain. Pindahkan atau hapus datanya terlebih dahulu.',
            ]);
        }

        if ($blockedMessage !== null) {
            return back()->withErrors(['error' => $blockedMessage]);
        }

        return redirect()->route('dashboard.users.index')
            ->with('success', 'User berhasil dihapus!');
    }
}
