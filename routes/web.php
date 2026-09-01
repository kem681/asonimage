<?php

use App\Http\Controllers\Admin\AuthorizedEmailController;
use App\Http\Controllers\Admin\ResourceController as AdminResourceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\ResourceLibraryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::post('/inscription', [InscriptionController::class, 'store'])->name('inscription.store');

Route::get('/connexion', [LoginController::class, 'show'])->name('login')->middleware('guest');
Route::post('/connexion', [LoginController::class, 'store'])->middleware('guest');
Route::post('/deconnexion', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

Route::get('/creer-un-compte', [RegisterController::class, 'show'])->name('register')->middleware('guest');
Route::post('/creer-un-compte', [RegisterController::class, 'store'])->middleware('guest');

Route::middleware('auth')->prefix('membres')->name('membres.')->group(function () {
    Route::get('/', [ResourceLibraryController::class, 'index'])->name('index');
    Route::get('/{edition}', [ResourceLibraryController::class, 'showEdition'])->name('edition');
    Route::get('/{edition}/jour/{day}', [ResourceLibraryController::class, 'showDay'])->name('jour');
    Route::get('/ressources/{resource}', [ResourceLibraryController::class, 'show'])->name('ressources.show');
    Route::get('/ressources/{resource}/fichier', [ResourceLibraryController::class, 'stream'])->name('ressources.fichier');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/emails-autorises', [AuthorizedEmailController::class, 'index'])->name('emails.index');
    Route::post('/emails-autorises', [AuthorizedEmailController::class, 'store'])->name('emails.store');
    Route::post('/emails-autorises/import', [AuthorizedEmailController::class, 'import'])->name('emails.import');

    Route::get('/ressources', [AdminResourceController::class, 'index'])->name('resources.index');
    Route::get('/ressources/nouvelle', [AdminResourceController::class, 'create'])->name('resources.create');
    Route::post('/ressources', [AdminResourceController::class, 'store'])->name('resources.store');
});
