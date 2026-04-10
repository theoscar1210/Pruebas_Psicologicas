<?php

use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EvaluatorAssessmentController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\PsychologicalReportController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\Candidate\TestTakingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ── Inicio ────────────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('login'));

// ── Dashboard del administrador ───────────────────────────────────────────────
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ── Panel de administración (requiere auth) ───────────────────────────────────
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cargos
    Route::resource('positions', PositionController::class);

    // Pruebas y sus preguntas
    Route::resource('tests', TestController::class);
    Route::prefix('tests/{test}')->name('tests.')->group(function () {
        Route::get('questions', [QuestionController::class, 'index'])->name('questions.index');
        Route::post('questions', [QuestionController::class, 'store'])->name('questions.store');
        Route::put('questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
        Route::delete('questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
    });

    // Candidatos
    Route::resource('candidates', CandidateController::class);
    Route::post('candidates/{candidate}/assign-test', [CandidateController::class, 'assignTest'])
        ->name('candidates.assign-test');

    // Reportes
    Route::prefix('reportes')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('candidato/{candidate}/pdf', [ReportController::class, 'candidatePdf'])->name('candidate.pdf');
        Route::get('ranking/pdf', [ReportController::class, 'rankingPdf'])->name('ranking.pdf');
        Route::get('exportar/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
        Route::get('ranking/excel', [ReportController::class, 'exportRankingExcel'])->name('ranking.excel');
    });
});

// ── Portal de candidatos (acceso por código único) ────────────────────────────
Route::prefix('candidato')->name('candidate.')->group(function () {
    Route::get('/', [TestTakingController::class, 'accessForm'])->name('access');
    Route::post('/', [TestTakingController::class, 'access'])->name('access.post');
    Route::post('/logout', [TestTakingController::class, 'logout'])->name('logout');

    Route::middleware('candidate.session')->group(function () {
        Route::get('/inicio', [TestTakingController::class, 'dashboard'])->name('dashboard');
        Route::get('/prueba/{assignment}/iniciar', [TestTakingController::class, 'start'])->name('start');
        Route::post('/prueba/{assignment}/respuesta', [TestTakingController::class, 'saveAnswer'])->name('answer');
        Route::post('/prueba/{assignment}/finalizar', [TestTakingController::class, 'finish'])->name('finish');
        Route::get('/prueba/{assignment}/resultado', [TestTakingController::class, 'result'])->name('result');
    });
});

require __DIR__.'/auth.php';
