<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Models\Article;
use App\Models\Testimonial;
use App\Models\Registration;
use App\Models\InternetPackage;
use App\Models\WebSetting;
use Illuminate\Http\Request;

Route::get('/', function () {
    $testimonials = Testimonial::all();
    $articles = Article::all();
    $internetPackages = InternetPackage::where('is_active', true)->get();
    
    // Convert settings into key-value array
    $settings = WebSetting::all()->pluck('value', 'key')->toArray();

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'testimonials' => $testimonials,
        'articles' => $articles,
        'internetPackages' => $internetPackages,
        'webSettings' => $settings,
    ]);
});

Route::get('/terima-kasih', function () {
    return Inertia::render('TerimaKasih');
});

use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AdminDashboardController;

Route::get('/daftar', [RegistrationController::class, 'create'])->name('daftar');
Route::post('/daftar', [RegistrationController::class, 'store'])->name('daftar.store');
Route::post('/daftar/upload-photo', [RegistrationController::class, 'uploadPhoto'])->name('daftar.upload');

// Dummy endpoints for public dropdowns to match form-wifi
Route::get('/api/public/villages', function () {
    return response()->json([
        ['id' => 1, 'name' => 'GUMELAR'],
        ['id' => 2, 'name' => 'CIHONJE'],
        ['id' => 3, 'name' => 'TLAGA'],
        ['id' => 4, 'name' => 'SAMUDRA'],
        ['id' => 5, 'name' => 'SAMUDRA KULON'],
        ['id' => 6, 'name' => 'CILANGKAP'],
        ['id' => 7, 'name' => 'PANINGKABAN'],
        ['id' => 8, 'name' => 'KARANG KEMOJING'],
        ['id' => 9, 'name' => 'GANCANG'],
        ['id' => 10, 'name' => 'KEDUNG URANG'],
    ]);
});

Route::get('/api/public/recent-registrations', function (Request $request) {
    $limit = $request->query('limit', 5);
    $recent = \App\Models\Registration::orderBy('created_at', 'desc')->take($limit)->get();
    
    // Anonymize names
    $recent->transform(function ($item) {
        $nameParts = explode(' ', $item->nama);
        $firstName = $nameParts[0];
        $anonymized = $firstName . ' ' . str_repeat('*', max(1, strlen($item->nama) - strlen($firstName) - 1));
        return [
            'id' => $item->id,
            'name' => $anonymized,
            'village' => $item->desa,
            'package' => $item->paket,
            'created_at' => $item->created_at,
            'timestamp' => $item->created_at->toIso8601String()
        ];
    });

    return response()->json($recent);
});

Route::get('/api/public/packages', function () {
    return response()->json([
        ['id' => 1, 'name' => 'PAKET STARTER (20 Mbps) - Rp 115.000/Bln'],
        ['id' => 2, 'name' => 'PAKET BASIC (30 Mbps) - Rp 150.000/Bln'],
        ['id' => 3, 'name' => 'PAKET STANDARD (50 Mbps) - Rp 200.000/Bln'],
        ['id' => 4, 'name' => 'PAKET PREMIUM (75 Mbps) - Rp 250.000/Bln'],
        ['id' => 5, 'name' => 'PAKET ULTRA (100 Mbps) - Rp 350.000/Bln'],
    ]);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::prefix('admin/customers')->group(function () {
        Route::post('/', [AdminDashboardController::class, 'createCustomer'])->name('admin.customers.store');
        Route::put('/{id}', [AdminDashboardController::class, 'updateCustomer'])->name('admin.customers.update');
        Route::delete('/{id}', [AdminDashboardController::class, 'deleteCustomer'])->name('admin.customers.destroy');
        Route::post('/upload-ktp', [AdminDashboardController::class, 'uploadKtp'])->name('admin.customers.uploadKtp');
        Route::patch('/{id}/status', [AdminDashboardController::class, 'updateCustomerStatus'])->name('admin.customers.status');
    });

    Route::prefix('admin/packages')->group(function () {
        Route::post('/', [\App\Http\Controllers\AdminSettingsController::class, 'storePackage']);
        Route::put('/{id}', [\App\Http\Controllers\AdminSettingsController::class, 'updatePackage']);
        Route::delete('/{id}', [\App\Http\Controllers\AdminSettingsController::class, 'deletePackage']);
    });

    Route::prefix('admin/villages')->group(function () {
        Route::post('/', [\App\Http\Controllers\AdminSettingsController::class, 'storeVillage']);
        Route::put('/{id}', [\App\Http\Controllers\AdminSettingsController::class, 'updateVillage']);
        Route::delete('/{id}', [\App\Http\Controllers\AdminSettingsController::class, 'deleteVillage']);
    });

    Route::prefix('admin/users')->group(function () {
        Route::post('/', [\App\Http\Controllers\AdminSettingsController::class, 'storeUser']);
        Route::put('/{id}', [\App\Http\Controllers\AdminSettingsController::class, 'updateUser']);
        Route::delete('/{id}', [\App\Http\Controllers\AdminSettingsController::class, 'deleteUser']);
    });

    Route::post('/admin/notifications/mark-all-read', [\App\Http\Controllers\AdminSettingsController::class, 'markAllRead']);
    Route::post('/admin/notifications/{id}/read', [\App\Http\Controllers\AdminSettingsController::class, 'markRead']);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth:member'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Cetak Slip Gaji ──────────────────────────────────────────────────────
    Route::get('/hrm/payroll/{payroll}/print', function (\App\Models\Payroll $payroll) {
        $payroll->load('employee');
        $months = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
            5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
            9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
        ];
        $d = \Carbon\Carbon::parse($payroll->period);
        $periodLabel = ($months[$d->month] ?? '') . ' ' . $d->year;

        return view('payroll.print-slip', compact('payroll', 'periodLabel'));
    })->name('payroll.print');
});

// ═══════════════════════════════════════════════════════════════
// PUBLIC JSON API — Digunakan oleh Next.js Frontend
// ═══════════════════════════════════════════════════════════════

// GET semua artikel
Route::get('/api/articles', function () {
    $articles = Article::orderBy('created_at', 'desc')->get()->map(function ($a) {
        $imageUrl = $a->cover_image
            ? url('storage/' . $a->cover_image)
            : $a->image_url;

        return [
            'id'          => $a->id,
            'category'    => $a->category,
            'title'       => $a->title,
            'excerpt'     => $a->excerpt,
            'image_url'   => $imageUrl,
            'cover_image' => $a->cover_image ? url('storage/' . $a->cover_image) : null,
            'has_content' => !empty($a->content),
            'created_at'  => $a->created_at,
            'updated_at'  => $a->updated_at,
        ];
    });

    return response()->json($articles)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Cache-Control', 'public, max-age=60');
});

// GET satu artikel detail
Route::get('/api/articles/{id}', function ($id) {
    $article = Article::findOrFail($id);

    $coverUrl = $article->cover_image
        ? url('storage/' . $article->cover_image)
        : $article->image_url;

    $gallery = [];
    if ($article->gallery) {
        $raw = is_string($article->gallery)
            ? json_decode($article->gallery, true)
            : $article->gallery;
        $gallery = collect($raw ?? [])->map(fn($p) => url('storage/' . $p))->toArray();
    }

    return response()->json([
        'id'          => $article->id,
        'category'    => $article->category,
        'title'       => $article->title,
        'excerpt'     => $article->excerpt,
        'content'     => $article->content,
        'image_url'   => $coverUrl,
        'cover_image' => $coverUrl,
        'gallery'     => $gallery,
        'created_at'  => $article->created_at,
        'updated_at'  => $article->updated_at,
    ])->header('Access-Control-Allow-Origin', '*');
});

// GET semua testimoni
Route::get('/api/testimonials', function () {
    $testimonials = Testimonial::orderBy('id')->get()->map(function ($t) {
        $name = $t->author_name ?? $t->name ?? '';
        return [
            'id'             => $t->id,
            'quote'          => $t->quote ?? $t->content ?? '',
            'author_name'    => $name,
            'author_role'    => $t->author_role ?? $t->role ?? '',
            'avatar_initials'=> strtoupper(substr($name, 0, 2)),
        ];
    });

    return response()->json($testimonials)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Cache-Control', 'public, max-age=60');
});

// Endpoint untuk Webhook Mikrotik (Tidak kena CSRF karena ada di prefix /api/)
Route::match(['get', 'post'], '/api/isp/netwatch-webhook', [\App\Http\Controllers\Api\NetwatchWebhookController::class, 'handle'])
    ->middleware(\App\Http\Middleware\VerifyIspApiKey::class);

// Endpoint untuk Webhook Midtrans (Payment Notification)
Route::post('/api/isp/midtrans-webhook', [\App\Http\Controllers\Api\MidtransWebhookController::class, 'handle']);

// Endpoint Internal API untuk Typebot / AI Karyawan
Route::prefix('/api/isp/typebot')
    ->middleware(\App\Http\Middleware\VerifyIspApiKey::class)
    ->group(function () {
    Route::get('/customer', [\App\Http\Controllers\Api\TypebotBridgeController::class, 'getCustomerByPhone']);
    Route::get('/customer/{id}/invoices', [\App\Http\Controllers\Api\TypebotBridgeController::class, 'getUnpaidInvoices']);
    Route::post('/invoices/{id}/pay', [\App\Http\Controllers\Api\TypebotBridgeController::class, 'generatePaymentLink']);
});

// ═══════════════════════════════════════════════════════════════
// PORTAL PELANGGAN — Login & Dashboard self-service pelanggan ISP
// ═══════════════════════════════════════════════════════════════

Route::prefix('pelanggan')->name('portal.')->group(function () {
    Route::get('/login',  [\App\Http\Controllers\CustomerPortalController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\CustomerPortalController::class, 'login'])->name('login.post');
    Route::post('/logout',[\App\Http\Controllers\CustomerPortalController::class, 'logout'])->name('logout');

    Route::middleware('auth:member')->group(function () {
        Route::get('/',              [\App\Http\Controllers\CustomerPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/invoice/{id}',  [\App\Http\Controllers\CustomerPortalController::class, 'invoiceDetail'])->name('invoice');
        // Pembayaran: generate Midtrans Snap Token via AJAX
        Route::post('/invoice/{id}/pay', [\App\Http\Controllers\CustomerPortalController::class, 'generatePayment'])->name('invoice.pay');
        // Tiket Gangguan
        Route::get('/gangguan',      [\App\Http\Controllers\CustomerPortalController::class, 'ticketIndex'])->name('tickets');
        Route::get('/gangguan/buat', [\App\Http\Controllers\CustomerPortalController::class, 'ticketCreate'])->name('ticket.create');
        Route::post('/gangguan/buat',[\App\Http\Controllers\CustomerPortalController::class, 'ticketStore'])->name('ticket.store');
        Route::get('/gangguan/{id}', [\App\Http\Controllers\CustomerPortalController::class, 'ticketDetail'])->name('ticket.show');
    });
});

require __DIR__.'/auth.php';
