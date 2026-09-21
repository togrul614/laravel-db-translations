<?php

use Globalsoft\DbTranslations\Http\Controllers\TranslationController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('db-translations.route_prefix', 'backend/translations'),
    'as' => config('db-translations.route_name_prefix', 'backend.translations.'),
    'middleware' => config('db-translations.middleware', ['web']),
], function () {
    Route::get('/', [TranslationController::class, 'index'])->name('index');
    Route::get('/create', [TranslationController::class, 'create'])->name('create');
    Route::post('/', [TranslationController::class, 'store'])->name('store');
    Route::get('/{translation}/edit', [TranslationController::class, 'edit'])->name('edit');
    Route::put('/{translation}', [TranslationController::class, 'update'])->name('update');
    Route::delete('/{translation}', [TranslationController::class, 'destroy'])->name('destroy');
    Route::post('/import', [TranslationController::class, 'import'])->name('import');
    Route::post('/clear-cache', [TranslationController::class, 'clearCache'])->name('clear_cache');
});
