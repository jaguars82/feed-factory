<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\ChessController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\TransportController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/chess', [ChessController::class, 'index'])->name('chess.index');
    Route::delete('/chess/delete/{id}', [ChessController::class, 'delete'])->name('chess.delete');
    Route::match(['get', 'post'], '/chess/add', [ChessController::class, 'add'])->name('chess.add');
});

Route::middleware('auth')->group(function () {
    Route::get('/provider', [ProviderController::class, 'index'])->name('provider.index');
    Route::match(['get', 'post'], '/provider/add', [ProviderController::class, 'add'])->name('provider.add');
});

Route::middleware('auth')->group(function () {
    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
    Route::match(['get', 'post'], '/feed/add', [FeedController::class, 'add'])->name('feed.add');
});

Route::middleware('auth')->group(function () {
    Route::get('/transport', [TransportController::class, 'index'])->name('transport.index');
    Route::match(['get', 'post'], '/transport/add', [TransportController::class, 'add'])->name('transport.add');
});

require __DIR__.'/auth.php';
