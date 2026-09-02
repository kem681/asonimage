<?php

use App\Http\Controllers\Admin\AuthorizedEmailController;
use App\Http\Controllers\Admin\ResourceController as AdminResourceController;
use App\Http\Controllers\Admin\WorkshopCodeController;
use App\Http\Controllers\Admin\WorkshopParticipantController;
use App\Http\Controllers\Admin\WorkshopStatsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\ResourceLibraryController;
use App\Http\Controllers\Workshop\AnchorController;
use App\Http\Controllers\Workshop\DashboardController;
use App\Http\Controllers\Workshop\DiagnosticController;
use App\Http\Controllers\Workshop\FrictionController;
use App\Http\Controllers\Workshop\GroupController;
use App\Http\Controllers\Workshop\JoinController;
use App\Http\Controllers\Workshop\ModelController;
use App\Http\Controllers\Workshop\PathController;
use App\Http\Controllers\Workshop\ProfileController;
use App\Http\Controllers\Workshop\ReviewController;
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
    Route::delete('/ressources/{resource}', [AdminResourceController::class, 'destroy'])->name('resources.destroy');

    // Module 3x30 : codes d'atelier, liste de contact, chiffres agreges.
    Route::prefix('3x30')->name('workshop.')->group(function () {
        Route::get('/codes', [WorkshopCodeController::class, 'index'])->name('codes.index');
        Route::post('/codes', [WorkshopCodeController::class, 'store'])->name('codes.store');
        Route::post('/codes/{workshopCode}/basculer', [WorkshopCodeController::class, 'toggle'])->name('codes.toggle');
        Route::get('/participants', [WorkshopParticipantController::class, 'index'])->name('participants.index');
        Route::get('/participants/export', [WorkshopParticipantController::class, 'export'])->name('participants.export');
        Route::get('/chiffres', [WorkshopStatsController::class, 'index'])->name('stats');
    });
});

/*
|--------------------------------------------------------------------------
| Module 3x30
|--------------------------------------------------------------------------
*/

// Pages publiques du module : creation de compte par code, installation, page hors ligne.
Route::get('/3x30/rejoindre', [RegisterController::class, 'showWorkshop'])->name('workshop.register')->middleware('guest');
Route::view('/3x30/installer', 'workshop.install')->name('workshop.install');
Route::view('/3x30/hors-ligne', 'workshop.offline')->name('workshop.offline');

// Saisie du code par un utilisateur deja connecte.
Route::middleware('auth')->group(function () {
    Route::get('/3x30/code', [JoinController::class, 'show'])->name('workshop.code');
    Route::post('/3x30/code', [JoinController::class, 'store']);
});

Route::middleware(['auth', 'participant'])->prefix('3x30')->name('workshop.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    Route::get('/modele', [ModelController::class, 'index'])->name('model');
    Route::get('/modele/{axis}', [ModelController::class, 'show'])->name('axis');

    Route::get('/diagnostic', [DiagnosticController::class, 'show'])->name('diagnostic');
    Route::post('/diagnostic', [DiagnosticController::class, 'store']);
    Route::get('/diagnostic/resultat', [DiagnosticController::class, 'result'])->name('diagnostic.result');
    Route::post('/diagnostic/axe', [DiagnosticController::class, 'chooseAxis'])->name('diagnostic.axis');

    Route::get('/ancrage/nouveau', [AnchorController::class, 'create'])->name('anchor.create');
    Route::post('/ancrage', [AnchorController::class, 'store'])->name('anchor.store');
    Route::post('/ancrage/pointage', [AnchorController::class, 'checkin'])->name('anchor.checkin');

    Route::get('/chemin', [PathController::class, 'index'])->name('path');

    Route::get('/frottement', [FrictionController::class, 'create'])->name('friction.create');
    Route::post('/frottement', [FrictionController::class, 'store'])->name('friction.store');
    Route::post('/frottement/{friction}/dit', [FrictionController::class, 'told'])->name('friction.told');

    Route::get('/fidelite', [ReviewController::class, 'create'])->name('review.create');
    Route::post('/fidelite', [ReviewController::class, 'store'])->name('review.store');
    Route::get('/memorial', [ReviewController::class, 'index'])->name('memorial');

    Route::get('/groupe', [GroupController::class, 'index'])->name('group.index');
    Route::post('/groupe', [GroupController::class, 'store'])->name('group.store');
    Route::post('/groupe/rejoindre', [GroupController::class, 'join'])->name('group.join');
    Route::get('/groupe/{group}', [GroupController::class, 'show'])->name('group.show');
    Route::post('/groupe/{group}/contact', [GroupController::class, 'contact'])->name('group.contact');
    Route::post('/groupe/{group}/rendez-vous', [GroupController::class, 'meeting'])->name('group.meeting');
    Route::post('/groupe/{group}/quitter', [GroupController::class, 'leave'])->name('group.leave');

    Route::get('/profil', [ProfileController::class, 'show'])->name('profile');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
