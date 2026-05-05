<?php

use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DataDeletionController as AdminDataDeletionController;
use App\Http\Controllers\Admin\EvaluatorAssessmentController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\PsychologicalReportController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\Admin\TscSlAdminController;
use App\Http\Controllers\Admin\TscSlHAdminController;
use App\Http\Controllers\Admin\TteSlAdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Candidate\DataDeletionController as CandidateDataDeletionController;
use App\Http\Controllers\Candidate\TestTakingController;
use App\Http\Controllers\Candidate\TscSlController;
use App\Http\Controllers\Candidate\TscSlHController;
use App\Http\Controllers\Candidate\TteSlController;
use App\Http\Controllers\Candidate\WarteggController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ── Inicio ────────────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('login'));

// ── Política de privacidad (pública) ─────────────────────────────────────────
Route::get('/politica-de-privacidad', fn () => view('privacy'))->name('privacy');

// ── Autenticación de dos factores (2FA) ──────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/two-factor/challenge', [TwoFactorController::class, 'challenge'])->name('two-factor.challenge');
    Route::post('/two-factor/challenge', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
});

Route::middleware(['auth', 'two-factor'])->group(function () {
    Route::get('/two-factor/setup',   [TwoFactorController::class, 'setup'])->name('two-factor.setup');
    Route::post('/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/two-factor/disable',[TwoFactorController::class, 'disable'])->name('two-factor.disable');
});

// ── Dashboard del administrador ───────────────────────────────────────────────
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified', 'two-factor'])
    ->name('dashboard');

// ── Panel de administración (requiere auth + 2FA) ─────────────────────────────
Route::middleware(['auth', 'two-factor'])->prefix('admin')->name('admin.')->group(function () {

    // Perfil propio — todos los roles autenticados
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Solo admin: gestión de usuarios ──────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    // ── Admin + Psicólogo: configuración de cargos y pruebas ─────────────────
    Route::middleware('role:admin,psicologo')->group(function () {

        Route::resource('positions', PositionController::class);

        Route::resource('tests', TestController::class);
        Route::prefix('tests/{test}')->name('tests.')->group(function () {
            Route::get('questions', [QuestionController::class, 'index'])->name('questions.index');
            Route::post('questions', [QuestionController::class, 'store'])->name('questions.store');
            Route::put('questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
            Route::delete('questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
        });
    });

    // ── Admin + RRHH + Psicólogo: gestión de candidatos ──────────────────────
    Route::middleware('role:admin,psicologo,hr')->group(function () {

        Route::resource('candidates', CandidateController::class);
        Route::post('candidates/{candidate}/assign-test', [CandidateController::class, 'assignTest'])
            ->name('candidates.assign-test');
        Route::delete('assignments/{assignment}', [CandidateController::class, 'destroyAssignment'])
            ->name('assignments.destroy');

        Route::prefix('reportes')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('candidato/{candidate}/pdf', [ReportController::class, 'candidatePdf'])->name('candidate.pdf');
            Route::get('ranking/pdf', [ReportController::class, 'rankingPdf'])->name('ranking.pdf');
            Route::get('exportar/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
            Route::get('ranking/excel', [ReportController::class, 'exportRankingExcel'])->name('ranking.excel');
        });
    });

    // ── Admin + Psicólogo: evaluaciones, calificaciones y perfiles ───────────
    Route::middleware('role:admin,psicologo')->group(function () {

        Route::get('perfiles', [CandidateController::class, 'perfilesIndex'])->name('perfiles.index');

        Route::get('tsc-sl/{session}/calificar',  [TscSlAdminController::class, 'score'])->name('tsc-sl.score');
        Route::post('tsc-sl/{session}/calificar', [TscSlAdminController::class, 'storeScore'])->name('tsc-sl.score.store');
        Route::get('tsc-sl/{session}/resultados', [TscSlAdminController::class, 'results'])->name('tsc-sl.results');

        Route::get('tsc-sl-h/{session}/calificar',  [TscSlHAdminController::class, 'score'])->name('tsc-sl-h.score');
        Route::post('tsc-sl-h/{session}/calificar', [TscSlHAdminController::class, 'storeScore'])->name('tsc-sl-h.score.store');
        Route::get('tsc-sl-h/{session}/resultados', [TscSlHAdminController::class, 'results'])->name('tsc-sl-h.results');

        Route::get('tte-sl/{session}/calificar',  [TteSlAdminController::class, 'score'])->name('tte-sl.score');
        Route::post('tte-sl/{session}/calificar', [TteSlAdminController::class, 'storeScore'])->name('tte-sl.score.store');
        Route::get('tte-sl/{session}/resultados', [TteSlAdminController::class, 'results'])->name('tte-sl.results');

        Route::get('candidates/{candidate}/evaluar', [EvaluatorAssessmentController::class, 'select'])->name('assessments.select');
        Route::get('candidates/{candidate}/evaluacion', [EvaluatorAssessmentController::class, 'create'])->name('assessments.create');
        Route::post('candidates/{candidate}/evaluacion', [EvaluatorAssessmentController::class, 'store'])->name('assessments.store');
        Route::get('evaluaciones/{assessment}/editar', [EvaluatorAssessmentController::class, 'edit'])->name('assessments.edit');
        Route::put('evaluaciones/{assessment}', [EvaluatorAssessmentController::class, 'update'])->name('assessments.update');

        Route::get('candidates/{candidate}/perfil', [PsychologicalReportController::class, 'show'])->name('profile.show');
        Route::post('candidates/{candidate}/perfil/generar', [PsychologicalReportController::class, 'generate'])->name('profile.generate');
        Route::post('candidates/{candidate}/perfil/completar', [PsychologicalReportController::class, 'complete'])->name('profile.complete');
        Route::get('candidates/{candidate}/perfil/pdf', [PsychologicalReportController::class, 'pdf'])->name('profile.pdf');
        Route::post('candidates/{candidate}/perfil/narrativa', [PsychologicalReportController::class, 'generateNarrative'])->name('profile.narrative');
    });

    // ── Admin: solicitudes de eliminación de datos (Ley 1581/2012) ───────────
    Route::middleware('role:admin')->group(function () {
        Route::get('eliminacion-datos', [AdminDataDeletionController::class, 'index'])->name('data-deletion.index');
        Route::post('eliminacion-datos/{deletion}/aprobar', [AdminDataDeletionController::class, 'approve'])->name('data-deletion.approve');
        Route::post('eliminacion-datos/{deletion}/rechazar', [AdminDataDeletionController::class, 'reject'])->name('data-deletion.reject');
    });
});

// ── Portal de candidatos (acceso por código único) ────────────────────────────
Route::prefix('candidato')->name('candidate.')->group(function () {
    Route::get('/', [TestTakingController::class, 'accessForm'])->name('access');
    Route::post('/', [TestTakingController::class, 'access'])->name('access.post');
    Route::post('/logout', [TestTakingController::class, 'logout'])->name('logout');

    Route::middleware('candidate.session')->group(function () {
        Route::get('/inicio', [TestTakingController::class, 'dashboard'])->name('dashboard');
        Route::get('/prueba/{assignment}/consentimiento',  [TestTakingController::class, 'consentForm'])->name('consent');
        Route::post('/prueba/{assignment}/consentimiento', [TestTakingController::class, 'storeConsent'])->name('consent.store');
        Route::get('/prueba/{assignment}/iniciar', [TestTakingController::class, 'start'])->name('start');
        Route::post('/prueba/{assignment}/respuesta', [TestTakingController::class, 'saveAnswer'])->name('answer');
        Route::post('/prueba/{assignment}/finalizar', [TestTakingController::class, 'finish'])->name('finish');
        Route::get('/prueba/{assignment}/resultado', [TestTakingController::class, 'result'])->name('result');

        // Wartegg digital
        Route::get('/wartegg/{assignment}/instrucciones',  [WarteggController::class, 'start'])->name('wartegg.start');
        Route::post('/wartegg/{assignment}/instrucciones', [WarteggController::class, 'storeConsent'])->name('wartegg.consent');
        Route::get('/wartegg/{assignment}/dibujar',        [WarteggController::class, 'draw'])->name('wartegg.draw');
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

        // TSC-SL Hospitalidad: Test de Servicio al Cliente — F&B / Servicio de Mesa
        Route::get('/tsc-sl-h/{assignment}/instrucciones',  [TscSlHController::class, 'start'])->name('tsc-sl-h.start');
        Route::post('/tsc-sl-h/{assignment}/instrucciones', [TscSlHController::class, 'storeConsent'])->name('tsc-sl-h.consent');
        Route::get('/tsc-sl-h/{assignment}/modulo1',        [TscSlHController::class, 'module1'])->name('tsc-sl-h.module1');
        Route::post('/tsc-sl-h/{assignment}/modulo1',       [TscSlHController::class, 'storeModule1'])->name('tsc-sl-h.module1.store');
        Route::get('/tsc-sl-h/{assignment}/modulo2',        [TscSlHController::class, 'module2'])->name('tsc-sl-h.module2');
        Route::post('/tsc-sl-h/{assignment}/modulo2',       [TscSlHController::class, 'storeModule2'])->name('tsc-sl-h.module2.store');
        Route::get('/tsc-sl-h/{assignment}/modulo3',        [TscSlHController::class, 'module3'])->name('tsc-sl-h.module3');
        Route::post('/tsc-sl-h/{assignment}/modulo3',       [TscSlHController::class, 'storeModule3'])->name('tsc-sl-h.module3.store');
        Route::get('/tsc-sl-h/{assignment}/completado',     [TscSlHController::class, 'complete'])->name('tsc-sl-h.complete');

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

        // Derecho al olvido — Ley 1581/2012
        Route::get('/eliminar-mis-datos',  [CandidateDataDeletionController::class, 'create'])->name('data-deletion');
        Route::post('/eliminar-mis-datos', [CandidateDataDeletionController::class, 'store'])->name('data-deletion.store');
    });
});

require __DIR__.'/auth.php';
