<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuoteRequestController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\PvgisController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

// Dashboard ruta, dostupna prijavljenim korisnicima
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Javna ruta web shop
Route::get('/shop', [ProductController::class, 'shopIndex'])->name('shop.index');

// PVGIS stranica sa formom
Route::get('/pvgis', [PvgisController::class, 'index'])->name('pvgis.index');



//  PVGIS 
Route::get('/pvgis', [PvgisController::class, 'index'])->name('pvgis.index');
Route::post('/pvgis/calculate', [PvgisController::class, 'calculate'])->name('pvgis.calculate');
Route::get('/products/suggest', [PvgisController::class, 'suggestProducts']);


Route::resource('products', ProductController::class);


// Rute koje zahtijevaju autentifikaciju
Route::middleware('auth')->group(function () {
    // Profil korisnika rute
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // proizvodi (CRUD operacije)

    // zahtjevi za ponudu
    Route::resource('quote-requests', QuoteRequestController::class);

    // ponude 
    Route::resource('proposals', ProposalController::class);

    // prihvatanje i odbijanje ponude
    Route::post('proposals/{proposal}/accept', [ProposalController::class, 'accept'])->name('proposals.accept');
    Route::post('proposals/{proposal}/reject', [ProposalController::class, 'reject'])->name('proposals.reject');
});

require __DIR__.'/auth.php';

