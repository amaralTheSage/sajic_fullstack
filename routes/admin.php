<?php

use App\Http\Controllers\LectureController;
use App\Http\Controllers\SpeakerController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Todas as rotas nesse arquivo são protegidas pelos middlewares auth, verified e admin

// Resource de Palestrantes
Route::get('palestrantes', [SpeakerController::class, 'index'])->name('speakers.index');
Route::post('palestrantes/novo', [SpeakerController::class, 'store'])->name('speakers.store');
Route::patch('palestrantes/{speaker}/edit', [SpeakerController::class, 'update'])->name('speakers.update');
Route::delete('palestrantes/{speaker}', [SpeakerController::class, 'destroy'])->name('speakers.destroy');

// Criação de Palestras
Route::get('nova-palestra', [LectureController::class, 'create'])->name('lectures.create');
Route::post('nova-palestra', [LectureController::class, 'store'])->name('lectures.store');

// Edição e Remoção de Palestras
Route::get('palestras/{lecture}/edit', [LectureController::class, 'edit'])->name('lectures.edit');
Route::patch('palestras/{lecture}/edit', [LectureController::class, 'update'])->name('lectures.update');
Route::delete('palestras/{lecture}', [LectureController::class, 'destroy'])->name('lectures.destroy');

// Lista de Presenças
Route::get('presencas', [UserController::class, 'attendance_list'])->name('user.attendance_list');

// Check In
Route::get('palestras/{lecture}/check-in', [LectureController::class, 'attendant_table'])->name('lectures.attendant_table');
Route::patch('palestras/{lecture}/check-in', [LectureController::class, 'checkin'])->name('lectures.check-in');

//Finaliza Palestra
Route::patch('palestras/{lecture}/finish', [LectureController::class, 'finish'])->name('lectures.finish');

// Reabre inscrições
Route::patch('palestras/{lecture}/reabrir-inscricoes', [LectureController::class, 'reopen_enrollment'])->name('lectures.reopen_enrollment');

// Enfim gera os certificados
Route::post('certificados', [LectureController::class, 'generate_certificates'])->name('lectures.certificates');
