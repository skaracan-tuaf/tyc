<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\BackendController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MunitionController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
//use App\Http\Controllers\MunitionAttributeController;
//use App\Http\Controllers\ImageController;

/*
Route::get('/', function () {
    return view('welcome');
});
*/

Route::get('/', [FrontendController::class, 'index'])->name('AnaSayfa');
Route::get('/yonetim', [BackendController::class, 'index'])->name('YoneticiAnaSayfa');

Route::prefix('yonetim')->group(function () {
    Route::resource('kategori', CategoryController::class);
    Route::resource('muhimmat', MunitionController::class);
    Route::resource('ozellik', AttributeController::class);

    Route::resource('makale', PostController::class);
    Route::resource('etiket', TagController::class);

    Route::put('/kategori/{id}/durum-degistir', [CategoryController::class, 'changeStatus'])->name('kategoriDurumunuDegistir');
    Route::put('/ozellik/{id}/durum-degistir', [AttributeController::class, 'changeStatus'])->name('ozellikDurumunuDegistir');
    Route::put('/muhimmat/{id}/durum-degistir', [MunitionController::class, 'changeStatus'])->name('muhimmatDurumunuDegistir');
    Route::put('/makale/{id}/durum-degistir', [PostController::class, 'changeStatus'])->name('makaleDurumunuDegistir');
    Route::put('/makale/{id}/arsivle', [PostController::class, 'remove'])->name('makaleyiArsivle');
    Route::get('/makale/arsiv', [PostController::class, 'indexDeleted'])->name('arsiviGoster');
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
