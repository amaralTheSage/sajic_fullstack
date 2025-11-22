<?php

use App\Http\Controllers\LectureController;
use App\Http\Controllers\UserController;
use App\Models\Lecture;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

// Home
Route::get('/', function () {
    return Inertia::render('home', ['lectures' => Lecture::with('speaker')->get()]);
})->name('home');

Route::get('detona-div', function () {
    return Inertia::render('detona-div');
})->name('detona-div');

Route::get('certificate/{file}', function ($file) {
    $path = "certificates/{$file}";

    if (! Storage::disk('public')->exists($path)) {
        abort(404, 'Certificado não encontrado');
    }

    return Storage::disk('public')->download($path);
})->name('certificate.download');

// Palestras
Route::get('palestras', [LectureController::class, 'index'])->name('lectures.index');

// Minhas Palestras
Route::prefix('minhas-palestras')->middleware(['auth', 'verified'])->group(function () {
    Route::get(
        '/',
        [UserController::class, 'my_lectures']
    )->name('user.lectures');

    Route::post('join', [UserController::class, 'attend_lecture'])->name('user.attend-lecture');

    Route::post('leave', [UserController::class, 'leave_lecture'])->name('user.leave-lecture');

});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
