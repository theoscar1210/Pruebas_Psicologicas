<?php

use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EvaluatorAssessmentController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\PsychologicalReportController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\Admin\TscSlAdminController;
use App\Http\Controllers\Admin\TteSlAdminController;
use App\Http\Controllers\Candidate\TestTakingController;
use App\Http\Controllers\Candidate\TscSlController;
use App\Http\Controllers\Candidate\TteSlController;
use App\Http\Controllers\Candidate\WarteggController;
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
    Route::delete('assignments/{assignment}', [CandidateController::class, 'destroyAssignment'])
        ->name('assignments.destroy');
    Route::get('perfiles', [CandidateController::class, 'perfilesIndex'])
        ->name('perfiles.index');

    // TSC-SL: calificación M3 por el evaluador
    Route::get('tsc-sl/{session}/calificar',  [TscSlAdminController::class, 'score'])->name('tsc-sl.score');
    Route::post('tsc-sl/{session}/calificar', [TscSlAdminController::class, 'storeScore'])->name('tsc-sl.score.store');
    Route::get('tsc-sl/{session}/resultados', [TscSlAdminController::class, 'results'])->name('tsc-sl.results');

    // TTE-SL: calificación M3 por el evaluador
    Route::get('tte-sl/{session}/calificar',  [TteSlAdminController::class, 'score'])->name('tte-sl.score');
    Route::post('tte-sl/{session}/calificar', [TteSlAdminController::class, 'storeScore'])->name('tte-sl.score.store');
    Route::get('tte-sl/{session}/resultados', [TteSlAdminController::class, 'results'])->name('tte-sl.results');

    // Evaluaciones clínicas del evaluador
    Route::get('candidates/{candidate}/evaluar', [EvaluatorAssessmentController::class, 'select'])->name('assessments.select');
    Route::get('candidates/{candidate}/evaluacion', [EvaluatorAssessmentController::class, 'create'])->name('assessments.create');
    Route::post('candidates/{candidate}/evaluacion', [EvaluatorAssessmentController::class, 'store'])->name('assessments.store');
    Route::get('evaluaciones/{assessment}/editar', [EvaluatorAssessmentController::class, 'edit'])->name('assessments.edit');
    Route::put('evaluaciones/{assessment}', [EvaluatorAssessmentController::class, 'update'])->name('assessments.update');

    // Perfil psicológico
    Route::get('candidates/{candidate}/perfil', [PsychologicalReportController::class, 'show'])->name('profile.show');
    Route::post('candidates/{candidate}/perfil/generar', [PsychologicalReportController::class, 'generate'])->name('profile.generate');
    Route::post('candidates/{candidate}/perfil/completar', [PsychologicalReportController::class, 'complete'])->name('profile.complete');
    Route::get('candidates/{candidate}/perfil/pdf', [PsychologicalReportController::class, 'pdf'])->name('profile.pdf');

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

        // Wartegg digital
        Route::get('/wartegg/{assignment}/instrucciones', [WarteggController::class, 'start'])->name('wartegg.start');
        Route::get('/wartegg/{assignment}/dibujar',       [WarteggController::class, 'draw'])->name('wartegg.draw');
        Route::post('/wartegg/{assignment}/guardar-caja', [WarteggController::class, 'saveBox'])->name('wartegg.save-box');
        Route::post('/wartegg/{assignment}/finalizar',    [WarteggController::class, 'finish'])->name('wartegg.finish');
        Route::get('/wartegg/{assignment}/completado',    [WarteggController::class, 'complete'])->name('wartegg.complete');

        // TSC-SL: Test de Servicio al Cliente
        Route::get('/tsc-sl/{assignment}/instrucciones',  [TscSlController::class, 'start'])->name('tsc-sl.start');
        Route::post('/tsc-sl/{assignment}/instrucciones', [TscSlController::class, 'storeConsent'])->name('tsc-sl.consent');
        Route::get('/tsc-sl/{assignment}/modulo1',       [TscSlController::class, 'module1'])->name('tsc-sl.module1');
        Route::post('/tsc-sl/{assignment}/modulo1',      [TscSlController::class, 'storeModule1'])->name('tsc-sl.module1.store');
        Route::get('/tsc-sl/{assignment}/modulo2',       [TscSlController::class, 'module2'])->name('tsc-sl.module2');
        Route::post('/tsc-sl/{assignment}/modulo2',      [TscSlController::class, 'storeModule2'])->name('tsc-sl.module2.store');
        Route::get('/tsc-sl/{assignment}/modulo3',       [TscSlController::class, 'module3'])->name('tsc-sl.module3');
        Route::post('/tsc-sl/{assignment}/modulo3',      [TscSlController::class, 'storeModule3'])->name('tsc-sl.module3.store');
        Route::get('/tsc-sl/{assignment}/completado',    [TscSlController::class, 'complete'])->name('tsc-sl.complete');

        // TTE-SL: Test de Trabajo en Equipo
        Route::get('/tte-sl/{assignment}/instrucciones',  [TteSlController::class, 'start'])->name('tte-sl.start');
        Route::post('/tte-sl/{assignment}/instrucciones', [TteSlController::class, 'storeConsent'])->name('tte-sl.consent');
        Route::get('/tte-sl/{assignment}/modulo1',       [TteSlController::class, 'module1'])->name('tte-sl.module1');
        Route::post('/tte-sl/{assignment}/modulo1',      [TteSlController::class, 'storeModule1'])->name('tte-sl.module1.store');
        Route::get('/tte-sl/{assignment}/modulo2',       [TteSlController::class, 'module2'])->name('tte-sl.module2');
        Route::post('/tte-sl/{assignment}/modulo2',      [TteSlController::class, 'storeModule2'])->name('tte-sl.module2.store');
        Route::get('/tte-sl/{assignment}/modulo3',       [TteSlController::class, 'module3'])->name('tte-sl.module3');
        Route::post('/tte-sl/{assignment}/modulo3',      [TteSlController::class, 'storeModule3'])->name('tte-sl.module3.store');
        Route::get('/tte-sl/{assignment}/completado',    [TteSlController::class, 'complete'])->name('tte-sl.complete');
    });
});

require __DIR__.'/auth.php';
