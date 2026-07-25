<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PpidController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\PasswordResetLinkController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardCommentController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\PrestasiController;
use App\Http\Controllers\VisiMisiController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HymneTvriController;
use App\Http\Controllers\StreamingController;
use App\Http\Controllers\InfoMagangController;
use App\Http\Controllers\ReformasiRbController;
use App\Http\Controllers\TugasFungsiController;
use App\Http\Controllers\DashboardNewsController;
use App\Http\Controllers\DashboardPostController;
use App\Http\Controllers\DashboardPpidController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\InfoKunjunganController;
use App\Http\Controllers\DashboardHistoryController;
use App\Http\Controllers\DashboardPrestasiController;
use App\Http\Controllers\DashboardVisiMisiController;
use App\Http\Controllers\DashboardBroadcastController;
use App\Http\Controllers\DashboardHymneTvriController;
use App\Http\Controllers\DashboardInfoMagangController;
use App\Http\Controllers\DashboardContactInfoController;
use App\Http\Controllers\DashboardJadwalAcaraController;
use App\Http\Controllers\DashboardReformasiRbController;
use App\Http\Controllers\DashboardSocialMediaController;
use App\Http\Controllers\DashboardTugasFungsiController;
use App\Http\Controllers\DashboardNewsCategoryController;
use App\Http\Controllers\DashboardPostCategoryController;
use App\Http\Controllers\DashboardInfoKunjunganController;
use App\Http\Controllers\DashboardJadwalCategoryController;
use App\Http\Controllers\DashboardBroadcastCategoryController;
use App\Http\Controllers\DashboardAksesInformasiController;
use App\Http\Controllers\PegawaiLoginController;
use App\Http\Controllers\SymlinkController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Definisi semua route aplikasi, baik untuk frontend (publik) maupun
| backend (dashboard/admin).
|
*/

// ================= FRONTEND =================
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/postingan', [PostController::class, 'index'])->name('postingan.index');
Route::get('/postingan/{post:slug}', [PostController::class, 'show'])->name('postingan.show');

Route::get('/penyiaran', [BroadcastController::class, 'index'])->name('penyiaran.index');
Route::get('/penyiaran/{broadcast:slug}', [BroadcastController::class, 'show'])->name('penyiaran.show');

Route::get('/kategori/{category:slug}', [PostController::class, 'category'])->name('kategori.show');
Route::get('/penulis/{user:username}', [PostController::class, 'author'])->name('penulis.show');

Route::get('/visi-misi', [VisiMisiController::class, 'visiMisi'])->name('visi-misi');
Route::get('/tugas-fungsi', [TugasFungsiController::class, 'index'])->name('tugas-fungsi');
Route::get('/sejarah', [HistoryController::class, 'index'])->name('sejarah');
Route::get('/prestasi', [PrestasiController::class, 'index'])->name('prestasi.index');

Route::get('/jadwal-acara', [JadwalController::class, 'index'])->name('publikasi.jadwal');
Route::get('/info-ppid', [PpidController::class, 'index'])->name('ppid.index');
Route::get('/himne-tvri', [HymneTvriController::class, 'index'])->name('himne-tvri.index');
Route::get('/info-rb', [ReformasiRbController::class, 'index'])->name('info-rb.index');

Route::get('/info-magang', [InfoMagangController::class, 'index'])->name('info-magang.index');
Route::get('/info-kunjungan', [InfoKunjunganController::class, 'index'])->name('info-kunjungan.index');

// Komentar (Frontend) - Dilindungi dengan throttle untuk mencegah spam
Route::post('/komentar', [CommentController::class, 'store'])->name('komentar.store')->middleware('throttle:10,1');

Route::get('/streaming', [StreamingController::class, 'index'])->name('streaming');

// Route Berita (News)
Route::get('/berita', [NewsController::class, 'index'])->name('berita.index');
Route::get('/berita/{news:slug}', [NewsController::class, 'show'])->name('berita.show');

// Route Filter Berita (Redirect ke Index dengan parameter)
Route::get('/berita/kategori/{category:slug}', [NewsController::class, 'category'])->name('berita.kategori.show');
Route::get('/berita/penulis/{user:username}', [NewsController::class, 'author'])->name('berita.penulis.show');

// ================= AUTENTIKASI =================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
    
    // Lupa Password Routes
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

// ================= PEGAWAI LOGIN =================
Route::prefix('pegawai')->group(function () {
    Route::get('/login', [PegawaiLoginController::class, 'index'])->name('pegawai.login');
    Route::post('/login', [PegawaiLoginController::class, 'store'])->middleware('throttle:5,1');
});

Route::middleware('auth')->prefix('pegawai')->group(function () {
    Route::post('/logout', [PegawaiLoginController::class, 'logout'])->name('pegawai.logout');
    Route::get('/logout', [PegawaiLoginController::class, 'logout'])->name('pegawai.logout.get');
});

// ================= DASHBOARD =================
Route::middleware(['auth', 'isAdmin'])->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/visitor-chart', [DashboardController::class, 'visitorChartData'])->name('dashboard.visitor-chart');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::resource('/penyiaran', DashboardBroadcastController::class)->names('dashboard.penyiaran');
    Route::resource('/banners', BannerController::class)->except(['show']);
    Route::resource('/postingan', DashboardPostController::class)->names('dashboard.postingan')->parameters(['postingan' => 'post']);
    Route::resource('/visi-misi', DashboardVisiMisiController::class)->names('dashboard.visi-misi');
    Route::resource('/sejarah', DashboardHistoryController::class)->names('dashboard.sejarah')->parameters(['sejarah' => 'history']);

    // Kategori Penyiaran
    Route::post('/penyiaran-kategori', [DashboardBroadcastCategoryController::class, 'store'])->name('dashboard.penyiaran-kategori.store');
    Route::put('/penyiaran-kategori/{broadcastCategory}', [DashboardBroadcastCategoryController::class, 'update'])->name('dashboard.penyiaran-kategori.update');
    Route::delete('/penyiaran-kategori/{broadcastCategory}', [DashboardBroadcastCategoryController::class, 'destroy'])->name('dashboard.penyiaran-kategori.destroy');

    // Kategori Postingan
    Route::post('/kategori-postingan', [DashboardPostCategoryController::class, 'store'])->name('dashboard.kategori-postingan.store');
    Route::put('/kategori-postingan/{category}', [DashboardPostCategoryController::class, 'update'])->name('dashboard.kategori-postingan.update');
    Route::delete('/kategori-postingan/{category}', [DashboardPostCategoryController::class, 'destroy'])->name('dashboard.kategori-postingan.destroy');

    Route::resource('/tugas-fungsi', DashboardTugasFungsiController::class)->names('dashboard.tugas-fungsi')->parameters(['tugas-fungsi' => 'tugasFungsi']);
    Route::resource('/ppid', DashboardPpidController::class)->names('dashboard.ppid')->parameters(['ppid' => 'ppid']);
    Route::resource('/jadwal-acara', DashboardJadwalAcaraController::class)->names('dashboard.jadwal-acara')->parameters(['jadwal-acara' => 'jadwalAcara']);
    Route::resource('/prestasi', DashboardPrestasiController::class)->names('dashboard.prestasi')->parameters(['prestasi' => 'prestasi']);
    Route::resource('/himne-tvri', DashboardHymneTvriController::class)->names('dashboard.hymne-tvri')->parameters(['himne-tvri' => 'hymneTvri']);
    Route::resource('/info-rb', DashboardReformasiRbController::class)->names('dashboard.reformasi-rb')->parameters(['info-rb' => 'reformasiRb']);
    Route::resource('/info-magang', DashboardInfoMagangController::class)->names('dashboard.info-magang')->parameters(['info-magang' => 'infoMagang']);
    Route::resource('/info-kunjungan', DashboardInfoKunjunganController::class)->names('dashboard.info-kunjungan')->parameters(['info-kunjungan' => 'infoKunjungan']);
    Route::resource('/users', DashboardUserController::class)->names('dashboard.users')->parameters(['users' => 'user']);

    // Social Media
    Route::resource('social-media', DashboardSocialMediaController::class)->names('dashboard.social-media')->parameters(['social-media' => 'id'])->only(['index', 'edit', 'update']);

    // Kategori Jadwal
    Route::post('/jadwal-categories', [\App\Http\Controllers\DashboardJadwalCategoryController::class, 'store'])->name('dashboard.jadwal-categories.store');
    Route::put('/jadwal-categories/{jadwalCategory}', [\App\Http\Controllers\DashboardJadwalCategoryController::class, 'update'])->name('dashboard.jadwal-categories.update');
    Route::delete('/jadwal-categories/{jadwalCategory}', [\App\Http\Controllers\DashboardJadwalCategoryController::class, 'destroy'])->name('dashboard.jadwal-categories.destroy');

    // Informasi Kontak
    Route::resource('contact-info', DashboardContactInfoController::class)->names('dashboard.contact-info')->parameters(['contact-info' => 'id'])->only(['index', 'edit', 'update']);

    // FAQ Info Magang
    Route::resource('/info-magang-faq', \App\Http\Controllers\DashboardInfoMagangFaqController::class)->names('dashboard.info-magang-faq')->parameters(['info-magang-faq' => 'id']);

    // FAQ Info Kunjungan
    Route::resource('/info-kunjungan-faq', \App\Http\Controllers\DashboardInfoKunjunganFaqController::class)->names('dashboard.info-kunjungan-faq')->parameters(['info-kunjungan-faq' => 'id']);

    // [BARU] Resource Berita
    Route::resource('/berita', DashboardNewsController::class)->names('dashboard.berita')->parameters(['berita' => 'news']);

    // [BARU] Kategori Berita
    Route::post('/kategori-berita', [DashboardNewsCategoryController::class, 'store'])->name('dashboard.kategori-berita.store');
    Route::put('/kategori-berita/{newsCategory}', [DashboardNewsCategoryController::class, 'update'])->name('dashboard.kategori-berita.update');
    Route::delete('/kategori-berita/{newsCategory}', [DashboardNewsCategoryController::class, 'destroy'])->name('dashboard.kategori-berita.destroy');

    // Komentar (Backend)
    Route::get('/komentar', [DashboardCommentController::class, 'index'])->name('dashboard.komentar.index');
    Route::put('/komentar/{comment}/setujui', [DashboardCommentController::class, 'approve'])->name('dashboard.komentar.setujui');
    Route::put('/komentar/{comment}/tolak', [DashboardCommentController::class, 'reject'])->name('dashboard.komentar.tolak');
    Route::delete('/komentar/{comment}', [DashboardCommentController::class, 'destroy'])->name('dashboard.komentar.destroy');

    // Akses Informasi (Link dinamis di beranda)
    Route::get('/akses-informasi', [DashboardAksesInformasiController::class, 'index'])->name('dashboard.akses-informasi.index');
    Route::put('/akses-informasi/{aksesInformasi}', [DashboardAksesInformasiController::class, 'update'])->name('dashboard.akses-informasi.update');

    // Utilitas: Membuat symlink storage (hanya untuk admin utama, sekali saat deploy)
    Route::get('/symlink', [SymlinkController::class, 'create'])->name('dashboard.symlink');
});

// ================= UTILITAS =================
Route::post('/theme/toggle', [ThemeController::class, 'toggle'])->name('theme.toggle');