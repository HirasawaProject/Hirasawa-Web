<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OsuWeb\ScoresController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\StripWhitespace;
use App\Http\Middleware\OsuClientOnly;

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

// prefix group with /web
Route::prefix('web')->middleware([StripWhitespace::class, OsuClientOnly::class])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    Route::get('/osu-osz2-getscores.php', [ScoresController::class, 'getScores'])->name('web.scores.get');
    Route::post('/osu-submit-modular.php', [ScoresController::class, 'submitScore'])->name('web.scores.submit');
    Route::post('/osu-submit-modular-selector.php', [ScoresController::class, 'submitScore'])->name('web.scores.submit');
});

Route::get('/u/{user}', [ProfileController::class, 'show'])->name('profile.show-shorthand');
Route::get('/user/{user}', [ProfileController::class, 'show'])->name('profile.show');


require __DIR__.'/auth.php';
