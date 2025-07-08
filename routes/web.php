<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\BackendController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MunitionController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\VariantController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\PayloadController;
use App\Http\Controllers\TargetController;

// olmayan sayfalar için fallback
Route::fallback(function () {
    return app(BackendController::class)->PageNotFound();
});

// frontend
Route::get('/', [FrontendController::class, 'index'])->name('AnaSayfa');
Route::get('/karsilastir', [FrontendController::class, 'kiyasla'])->name('Kiyasla');
Route::get('/blog', [FrontendController::class, 'blog'])->name('Blog');
Route::get('/hakkimizda', [FrontendController::class, 'about'])->name('Hakkimizda');
Route::get('/iletisim', [FrontendController::class, 'contact'])->name('Iletisim');

Route::get('/search', [FrontendController::class, 'search'])->name('search');
Route::get('/kategori/{slug}', [FrontendController::class, 'FilterByCategory'])->name('kategoriFiltresi');
Route::get('/muhimmat-detay/{slug}', [FrontendController::class, 'ShowMunitionDetail'])->name('muhimmatDetay');

// backend
Route::prefix('yonetim')->group(function () {
    Route::get('/', [BackendController::class, 'index'])->name('YoneticiAnaSayfa');

    Route::resource('kategori', CategoryController::class);
    Route::resource('muhimmat', MunitionController::class);
    Route::resource('ozellik', AttributeController::class);
    Route::resource('varyant', VariantController::class);
    Route::resource('makale', PostController::class);
    Route::resource('etiket', TagController::class);
    Route::resource('platform', PlatformController::class);
    Route::resource('payload', PayloadController::class);
    Route::resource('hedef', TargetController::class);

    // durum değiştirme
    Route::put('/kategori/{id}/durum-degistir', [CategoryController::class, 'changeStatus'])->name('kategoriDurumunuDegistir');
    Route::put('/ozellik/{id}/durum-degistir', [AttributeController::class, 'changeStatus'])->name('ozellikDurumunuDegistir');
    Route::put('/muhimmat/{id}/durum-degistir', [MunitionController::class, 'changeStatus'])->name('muhimmatDurumunuDegistir');
    Route::put('/etiket/{id}/durum-degistir', [TagController::class, 'changeStatus'])->name('etiketDurumunuDegistir');
    Route::put('/makale/{id}/durum-degistir', [PostController::class, 'changeStatus'])->name('makaleDurumunuDegistir');
    Route::put('/platform/{id}/durum-degistir', [PlatformController::class, 'changeStatus'])->name('platformDurumunuDegistir');

    // özel işlemler
    Route::put('/makale/{id}/arsivle', [PostController::class, 'remove'])->name('makaleyiArsivle');
    Route::delete('/muhimmat/images/{id}', [MunitionController::class, 'deleteImage'])->name('muhimmatResminiSil');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
