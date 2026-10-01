<?php

use App\Http\Controllers\Admin\InverterController;
use App\Http\Controllers\Admin\PanelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectReviewController;
use App\Http\Controllers\SolarCalculatorController;
use App\Http\Controllers\SolarProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Dashboard - sadržaj zavisi od uloge prijavljenog korisnika.
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->isAdmin() || $user->isDesigner()) {
        return redirect()->route('review.index');
    }

    return redirect()->route('projects.index');
})->middleware(['auth'])->name('dashboard');

// Javni kalkulator isplativosti solarnog sistema (dostupan bez prijave).
Route::get('/kalkulator', [SolarCalculatorController::class, 'index'])->name('calculator.index');
Route::post('/kalkulator/izracunaj', [SolarCalculatorController::class, 'calculate'])->name('calculator.calculate');

Route::middleware('auth')->group(function () {
    // Profil korisnika
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Solarni projekti korisnika (kupca): moji projekti, snimanje proračuna, narudžba
    Route::get('/projekti', [SolarProjectController::class, 'index'])->name('projects.index');
    Route::post('/projekti', [SolarProjectController::class, 'store'])->name('projects.store');
    Route::get('/projekti/{project}', [SolarProjectController::class, 'show'])->name('projects.show');
    Route::get('/projekti/{project}/uredi', [SolarProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projekti/{project}', [SolarProjectController::class, 'update'])->name('projects.update');
    Route::post('/projekti/{project}/narudzba', [SolarProjectController::class, 'submit'])->name('projects.submit');
    Route::delete('/projekti/{project}', [SolarProjectController::class, 'destroy'])->name('projects.destroy');

    // Radni prostor projektanta i administratora: obrada narudžbi
    Route::middleware('role:designer,admin')->prefix('projektant')->name('review.')->group(function () {
        Route::get('/', [ProjectReviewController::class, 'index'])->name('index');
        Route::get('/{project}', [ProjectReviewController::class, 'show'])->name('show');
        Route::post('/{project}/preuzmi', [ProjectReviewController::class, 'claim'])->name('claim');
        Route::put('/{project}/sistem', [ProjectReviewController::class, 'updateDesign'])->name('updateDesign');
        Route::post('/{project}/odobri', [ProjectReviewController::class, 'approve'])->name('approve');
        Route::post('/{project}/odbij', [ProjectReviewController::class, 'reject'])->name('reject');
        Route::post('/{project}/zakazi', [ProjectReviewController::class, 'schedule'])->name('schedule');
        Route::post('/{project}/zavrsi', [ProjectReviewController::class, 'complete'])->name('complete');
    });

    // Administracija kataloga opreme
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('panels', PanelController::class)->except(['show']);
        Route::resource('inverters', InverterController::class)->except(['show']);
    });
});

require __DIR__.'/auth.php';
