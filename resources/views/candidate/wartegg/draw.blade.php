@extends('layouts.candidate')
@section('title', 'Test de Wartegg — Dibujar')

@section('content')
<style nonce="{{ app('csp-nonce') }}">
    body { overflow: hidden; }
    #wzt-app { height: 100dvh; display: flex; flex-direction: column; background: #f1f5f9; }
    #canvas-area { flex: 1; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 8px; }
    #canvas-wrapper { position: relative; background: white; box-shadow: 0 4px 24px rgba(0,0,0,.12); border-radius: 8px; }
    #stimulus-canvas, #drawing-canvas { position: absolute; top: 0; left: 0; border-radius: 8px; }
    #drawing-canvas { touch-action: none; cursor: crosshair; }
    .wzt-topbar    { background: white; border-bottom: 1px solid #e2e8f0; padding: 8px 12px; flex-shrink: 0; }
    .wzt-bottombar { background: white; border-top:    1px solid #e2e8f0; padding: 8px 12px; flex-shrink: 0; }
    .box-btn { width: 36px; height: 36px; border-radius: 8px; border: 2px solid #e2e8f0; background: white;
               font-size: 10px; font-weight: 700; cursor: pointer; transition: all .15s; position: relative;
               display: flex; align-items: center; justify-content: center; color: #94a3b8; font-family: monospace; }
    .box-btn.active { border-color: #7c3aed; background: #7c3aed; color: white; }
    .box-btn.done:not(.active) { border-color: #10b981; background: #ecfdf5; color: #059669; }
    .box-btn .done-dot { position: absolute; top: -3px; right: -3px; width: 8px; height: 8px;
                          background: #10b981; border-radius: 50%; border: 1.5px solid white; }
    .tool-btn { width: 40px; height: 40px; border-radius: 10px; border: 2px solid #e2e8f0; background: white;
                cursor: pointer; display: flex; align-items: center; justify-content: center;
                color: #64748b; transition: all .15s; }
    .tool-btn.active { border-color: #7c3aed; background: #ede9fe; color: #7c3aed; }
    .tool-btn:hover:not(.active) { border-color: #cbd5e1; background: #f8fafc; }
    .size-btn { width: 32px; height: 32px; border-radius: 8px; border: 2px solid #e2e8f0; background: white;
                cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .15s; }
    .size-btn.active { border-color: #7c3aed; background: #ede9fe; }
    .sz-dot-sm { width: 3px;  height: 3px;  background: #1e293b; border-radius: 50%; }
    .sz-dot-md { width: 6px;  height: 6px;  background: #1e293b; border-radius: 50%; }
    .sz-dot-lg { width: 10px; height: 10px; background: #1e293b; border-radius: 50%; }
    .btn-violet { background: #7c3aed; color: white; }
</style>

<div id="wzt-app" x-data>

    {{-- ── Barra superior: navegación entre cajas ────────────────────────── --}}
    <div class="wzt-topbar">
        <div class="flex items-center gap-2 overflow-x-auto scrollbar-none">
            {{-- Logo compacto --}}
            <span class="text-xs font-bold text-violet-700 mr-1 flex-shrink-0">WZT</span>
            <div class="flex items-center gap-1.5 flex-shrink-0">
                @foreach(['I','II','III','IV','V','VI','VII','VIII'] as $i => $roman)
                <button class="box-btn" id="box-btn-{{ $i+1 }}" @click="switchBox({{ $i+1 }})">
                    {{ $roman }}
                    <span class="done-dot hidden" id="done-dot-{{ $i+1 }}"></span>
                </button>
                @endforeach
            </div>
            {{-- Separador --}}
            <div class="flex-1"></div>
            {{-- Timer + progreso --}}
            <div class="flex-shrink-0 flex items-center gap-3">
                <span id="box-timer" class="text-xs font-mono text-slate-500">00:00</span>
                <span id="progress-label" class="text-xs text-slate-400 hidden sm:inline">0 / 8 completados</span>
            </div>
        </div>
    </div>

    {{-- ── Área del canvas ───────────────────────────────────────────────── --}}
    <div id="canvas-area">
        <div id="canvas-wrapper">
            <canvas id="stimulus-canvas"></canvas>
            <canvas id="drawing-canvas"></canvas>
        </div>
    </div>

    {{-- ── Barra inferior: título + herramientas ─────────────────────────── --}}
    <div class="wzt-bottombar">

        {{-- Título del dibujo --}}
        <div class="flex items-center gap-2 mb-2">
            <label class="text-xs text-slate-500 flex-shrink-0">Título:</label>
            <input type="text" id="title-input" maxlength="100"
                   placeholder="Escribe un título breve para este dibujo…"
                   class="flex-1 text-sm border border-slate-200 rounded-lg px-3 py-1.5 focus:outline-none focus:border-violet-400"
                   @change="saveTitleMemory()">
        </div>

        {{-- Herramientas + navegación --}}
        <div class="flex items-center gap-2 flex-wrap">

            {{-- Herramientas de dibujo --}}
            <div class="flex items-center gap-1.5">
                <button class="tool-btn active" id="btn-pencil" @click="setMode('draw')" title="Lápiz">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </button>
                <button class="tool-btn" id="btn-eraser" @click="setMode('erase')" title="Borrador">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
                <button class="tool-btn" @click="undo()" title="Deshacer (Ctrl+Z)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                </button>
            </div>

            {{-- Tamaño de trazo --}}
            <div class="flex items-center gap-1 border border-slate-200 rounded-lg p-1">
                <button class="size-btn active" id="sz-1" @click="setSize(2)" title="Trazo fino">
                    <div class="sz-dot-sm"></div>
                </button>
                <button class="size-btn" id="sz-2" @click="setSize(5)" title="Trazo medio">
                    <div class="sz-dot-md"></div>
                </button>
                <button class="size-btn" id="sz-3" @click="setSize(10)" title="Trazo grueso">
                    <div class="sz-dot-lg"></div>
                </button>
            </div>

            <div class="flex-1"></div>

            {{-- Navegación entre cajas --}}
            <div class="flex items-center gap-2">
                <button @click="prevBox()" class="text-xs text-slate-500 hover:text-slate-700 px-3 py-1.5 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                    ← Anterior
                </button>
                <button id="btn-next" @click="nextOrFinish()"
                        class="btn-violet text-sm font-semibold px-4 py-1.5 rounded-lg transition-colors">
                    Siguiente →
                </button>
            </div>

        </div>
    </div>

    {{-- Modal de confirmación para finalizar --}}
    <div id="finish-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-2xl p-6 mx-4 max-w-sm w-full">
            <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 text-center mb-2">¿Finalizar el test?</h3>
            <p class="text-sm text-slate-500 text-center mb-6">
                Completaste <span id="modal-count" class="font-semibold text-slate-700">0</span> de 8 campos.
                Una vez enviado, no podrás modificar tus dibujos.
            </p>
            <div class="flex gap-3">
                <button @click="hideModal()" class="flex-1 py-2 text-sm text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                    Volver a dibujar
                </button>
                <button @click="submitTest()" id="btn-confirm-finish"
                        class="btn-violet flex-1 py-2 text-sm font-semibold rounded-xl transition-colors">
                    Enviar test
                </button>
            </div>
        </div>
    </div>

</div>

{{-- Formulario oculto para envío final --}}
<form id="finish-form" method="POST" action="{{ route('candidate.wartegg.finish', $assignment) }}">
    @csrf
</form>

<script nonce="{{ app('csp-nonce') }}">
// ── Configuración inicial ──────────────────────────────────────────────────
const CANVAS_RES    = 400; // resolución interna del canvas (px)
const SAVE_URL      = "{{ route('candidate.wartegg.save-box', $assignment) }}";
const CSRF_TOKEN    = document.querySelector('meta[name="csrf-token"]').content;
const ROMAN         = ['I','II','III','IV','V','VI','VII','VIII'];

// Estado global
const state = {
    currentBox: 1,
    mode: 'draw',       // 'draw' | 'erase'
    strokeSize: 2,
    boxDrawings: {},    // { boxNum: ImageData }
    boxTitles: {},      // { boxNum: string }
    boxOrder: [],       // [boxNum, ...] orden de primera interacción
    boxTimers: {},      // { boxNum: { started: timestamp, elapsed: seconds } }
    savedBoxes: new Set(), // cajas guardadas en servidor
    undoStack: {},      // { boxNum: [ImageData, ...] }
    isDrawing: false,
    lastX: 0, lastY: 0,
};

// ── Referencias DOM ────────────────────────────────────────────────────────
const stimulusCanvas = document.getElementById('stimulus-canvas');
const drawingCanvas  = document.getElementById('drawing-canvas');
const sCtx           = stimulusCanvas.getContext('2d');
const dCtx           = drawingCanvas.getContext('2d');
const canvasArea     = document.getElementById('canvas-area');
const wrapper        = document.getElementById('canvas-wrapper');

// ── Inicialización ─────────────────────────────────────────────────────────
function init() {
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    // Cargar dibujos guardados en servidor (si existen del servidor)
    const savedBoxes = @json($session->boxes ?? []);
    savedBoxes.forEach(b => {
        if (b.drawing_data) {
            loadBoxImage(b.number, b.drawing_data);
            state.savedBoxes.add(b.number);
            state.boxTitles[b.number] = b.title || '';
            updateDoneDot(b.number, true);
        }
    });

    switchBox(1, false);

    // Auto-guardado cada 30 segundos
    setInterval(() => saveCurrentBox(false), 30000);

    // Undo con Ctrl+Z
    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'z') { e.preventDefault(); undo(); }
    });
}

// ── Canvas dimensions ──────────────────────────────────────────────────────
function resizeCanvas() {
    const areaH = canvasArea.clientHeight - 16;
    const areaW = canvasArea.clientWidth  - 16;
    const size  = Math.min(areaH, areaW, 500);

    wrapper.style.width  = size + 'px';
    wrapper.style.height = size + 'px';

    // Canvas internos siempre a CANVAS_RES x CANVAS_RES
    [stimulusCanvas, drawingCanvas].forEach(c => {
        c.width  = CANVAS_RES;
        c.height = CANVAS_RES;
        c.style.width  = size + 'px';
        c.style.height = size + 'px';
    });

    drawStimulus(state.currentBox);

    // Restaurar dibujo actual
    if (state.boxDrawings[state.currentBox]) {
        dCtx.putImageData(state.boxDrawings[state.currentBox], 0, 0);
    }
}

// ── Estímulos de los 8 campos ──────────────────────────────────────────────
function drawStimulus(boxNum) {
    const w = CANVAS_RES, h = CANVAS_RES;
    sCtx.clearRect(0, 0, w, h);

    // Fondo blanco
    sCtx.fillStyle = '#ffffff';
    sCtx.fillRect(0, 0, w, h);

    // Número romano en esquina
    sCtx.fillStyle = '#cbd5e1';
    sCtx.font = 'bold 13px monospace';
    sCtx.fillText(ROMAN[boxNum - 1], 10, 20);

    sCtx.fillStyle = '#111827';
    sCtx.strokeStyle = '#111827';
    sCtx.lineWidth = 2.5;
    sCtx.lineCap = 'round';
    sCtx.lineJoin = 'round';

    switch (boxNum) {
        case 1: // Punto central
            sCtx.beginPath();
            sCtx.arc(w * 0.50, h * 0.50, 5, 0, Math.PI * 2);
            sCtx.fill();
            break;

        case 2: // Curva izquierda (paréntesis )
            sCtx.beginPath();
            sCtx.moveTo(w * 0.34, h * 0.22);
            sCtx.bezierCurveTo(w * 0.17, h * 0.38, w * 0.17, h * 0.62, w * 0.34, h * 0.78);
            sCtx.stroke();
            break;

        case 3: // Tres puntos diagonal ascendente (sup-der → inf-izq)
            [[0.62, 0.22], [0.50, 0.50], [0.38, 0.78]].forEach(([x, y]) => {
                sCtx.beginPath();
                sCtx.arc(w * x, h * y, 4, 0, Math.PI * 2);
                sCtx.fill();
            });
            break;

        case 4: // Cuadrado negro relleno — inferior izquierda
            sCtx.fillRect(w * 0.18, h * 0.46, w * 0.30, h * 0.30);
            break;

        case 5: // Ángulo en techo (^) zona superior
            sCtx.beginPath();
            sCtx.moveTo(w * 0.20, h * 0.58);
            sCtx.lineTo(w * 0.50, h * 0.28);
            sCtx.lineTo(w * 0.80, h * 0.58);
            sCtx.stroke();
            break;

        case 6: // Dos líneas perpendiculares (ángulo recto ⌐)
            sCtx.beginPath();
            sCtx.moveTo(w * 0.40, h * 0.20);
            sCtx.lineTo(w * 0.40, h * 0.68);
            sCtx.lineTo(w * 0.76, h * 0.68);
            sCtx.stroke();
            break;

        case 7: // Nube de puntos dispersos
            [[0.33,0.34],[0.52,0.29],[0.43,0.49],[0.60,0.45],[0.29,0.56],[0.49,0.61],[0.64,0.57]]
            .forEach(([x, y]) => {
                sCtx.beginPath();
                sCtx.arc(w * x, h * y, 4, 0, Math.PI * 2);
                sCtx.fill();
            });
            break;

        case 8: // Arco abierto — esquina inferior derecha (cuenco ∪)
            sCtx.beginPath();
            sCtx.arc(w * 0.70, h * 0.70, h * 0.20, 0, Math.PI);
            sCtx.stroke();
            break;
    }
}

// ── Cambio de caja ─────────────────────────────────────────────────────────
async function switchBox(newBox, doSave = true) {
    if (doSave && state.currentBox !== newBox) {
        pauseBoxTimer(state.currentBox);
        await saveCurrentBox(false);
    }

    // Guardar estado visual de la caja anterior
    state.boxDrawings[state.currentBox] = dCtx.getImageData(0, 0, CANVAS_RES, CANVAS_RES);
    state.boxTitles[state.currentBox]   = document.getElementById('title-input').value;

    state.currentBox = newBox;

    // Limpiar canvas de dibujo
    dCtx.clearRect(0, 0, CANVAS_RES, CANVAS_RES);

    // Redibujar estímulo
    drawStimulus(newBox);

    // Restaurar dibujo si existe en memoria
    if (state.boxDrawings[newBox]) {
        dCtx.putImageData(state.boxDrawings[newBox], 0, 0);
    }

    // Restaurar título
    document.getElementById('title-input').value = state.boxTitles[newBox] || '';

    // Actualizar UI
    updateBoxNav();
    updateNextButton();
    resumeBoxTimer(newBox);
}

function prevBox() {
    if (state.currentBox > 1) switchBox(state.currentBox - 1);
}

function nextOrFinish() {
    if (state.currentBox < 8) {
        switchBox(state.currentBox + 1);
    } else {
        showFinishModal();
    }
}

// ── Navegación visual ──────────────────────────────────────────────────────
function updateBoxNav() {
    for (let i = 1; i <= 8; i++) {
        document.getElementById(`box-btn-${i}`).classList.toggle('active', i === state.currentBox);
    }
}

function updateDoneDot(boxNum, done) {
    const dot = document.getElementById(`done-dot-${boxNum}`);
    const btn = document.getElementById(`box-btn-${boxNum}`);
    dot.classList.toggle('hidden', !done);
    btn.classList.toggle('done', done);
}

function updateNextButton() {
    const btn = document.getElementById('btn-next');
    if (state.currentBox === 8) {
        btn.textContent = 'Finalizar ✓';
        btn.style.background = '#059669';
    } else {
        btn.textContent = 'Siguiente →';
        btn.style.background = '#7c3aed';
    }
}

function updateProgress() {
    const done = state.savedBoxes.size;
    document.getElementById('progress-label').textContent = `${done} / 8 completados`;
}

// ── Temporizador por caja ──────────────────────────────────────────────────
function resumeBoxTimer(boxNum) {
    if (!state.boxTimers[boxNum]) {
        state.boxTimers[boxNum] = { elapsed: 0, started: null };
    }
    state.boxTimers[boxNum].started = Date.now();
    if (!state.timerInterval) {
        state.timerInterval = setInterval(tickTimer, 1000);
    }
}

function pauseBoxTimer(boxNum) {
    if (!state.boxTimers[boxNum] || !state.boxTimers[boxNum].started) return;
    const now = Date.now();
    state.boxTimers[boxNum].elapsed += Math.floor((now - state.boxTimers[boxNum].started) / 1000);
    state.boxTimers[boxNum].started = null;
}

function getBoxElapsed(boxNum) {
    const t = state.boxTimers[boxNum];
    if (!t) return 0;
    let total = t.elapsed;
    if (t.started) total += Math.floor((Date.now() - t.started) / 1000);
    return total;
}

function tickTimer() {
    const secs = getBoxElapsed(state.currentBox);
    const m = String(Math.floor(secs / 60)).padStart(2, '0');
    const s = String(secs % 60).padStart(2, '0');
    document.getElementById('box-timer').textContent = `${m}:${s}`;
}

// ── Herramientas ───────────────────────────────────────────────────────────
function setMode(mode) {
    state.mode = mode;
    document.getElementById('btn-pencil').classList.toggle('active', mode === 'draw');
    document.getElementById('btn-eraser').classList.toggle('active', mode === 'erase');
    drawingCanvas.style.cursor = mode === 'erase' ? 'cell' : 'crosshair';
}

function setSize(size) {
    state.strokeSize = size;
    ['sz-1','sz-2','sz-3'].forEach(id => document.getElementById(id).classList.remove('active'));
    const idx = [2,5,10].indexOf(size);
    if (idx >= 0) document.getElementById(`sz-${idx+1}`).classList.add('active');
}

// ── Dibujo ─────────────────────────────────────────────────────────────────
function getPos(e) {
    const rect   = drawingCanvas.getBoundingClientRect();
    const scaleX = CANVAS_RES / rect.width;
    const scaleY = CANVAS_RES / rect.height;
    const src    = e.changedTouches ? e.changedTouches[0] : e;
    return {
        x: (src.clientX - rect.left) * scaleX,
        y: (src.clientY - rect.top)  * scaleY,
    };
}

function startDraw(e) {
    e.preventDefault();
    state.isDrawing = true;

    // Registrar primer uso de esta caja
    if (!state.boxOrder.includes(state.currentBox)) {
        state.boxOrder.push(state.currentBox);
    }
    if (!state.boxTimers[state.currentBox]) resumeBoxTimer(state.currentBox);

    // Guardar snapshot para undo
    if (!state.undoStack[state.currentBox]) state.undoStack[state.currentBox] = [];
    state.undoStack[state.currentBox].push(dCtx.getImageData(0, 0, CANVAS_RES, CANVAS_RES));
    if (state.undoStack[state.currentBox].length > 20) state.undoStack[state.currentBox].shift();

    const pos = getPos(e);
    state.lastX = pos.x;
    state.lastY = pos.y;
}

function draw(e) {
    if (!state.isDrawing) return;
    e.preventDefault();

    const pos = getPos(e);

    if (state.mode === 'erase') {
        dCtx.globalCompositeOperation = 'destination-out';
        dCtx.beginPath();
        dCtx.arc(pos.x, pos.y, state.strokeSize * 4, 0, Math.PI * 2);
        dCtx.fill();
    } else {
        dCtx.globalCompositeOperation = 'source-over';
        dCtx.strokeStyle = '#111827';
        dCtx.lineWidth   = state.strokeSize;
        dCtx.lineCap     = 'round';
        dCtx.lineJoin    = 'round';
        dCtx.beginPath();
        dCtx.moveTo(state.lastX, state.lastY);
        dCtx.lineTo(pos.x, pos.y);
        dCtx.stroke();
    }

    state.lastX = pos.x;
    state.lastY = pos.y;
}

function stopDraw(e) {
    if (e) e.preventDefault();
    state.isDrawing = false;
    dCtx.globalCompositeOperation = 'source-over';
}

// ── Undo ───────────────────────────────────────────────────────────────────
function undo() {
    const stack = state.undoStack[state.currentBox];
    if (!stack || stack.length === 0) return;
    const prev = stack.pop();
    dCtx.putImageData(prev, 0, 0);
}

// ── Guardar caja en servidor ───────────────────────────────────────────────
async function saveCurrentBox(showFeedback = false) {
    // Crear imagen compuesta: estímulo + dibujo
    const temp    = document.createElement('canvas');
    temp.width    = CANVAS_RES;
    temp.height   = CANVAS_RES;
    const tCtx    = temp.getContext('2d');

    tCtx.fillStyle = '#ffffff';
    tCtx.fillRect(0, 0, CANVAS_RES, CANVAS_RES);
    tCtx.drawImage(stimulusCanvas, 0, 0);
    tCtx.drawImage(drawingCanvas,  0, 0);

    const drawingData  = temp.toDataURL('image/png');
    const orderIndex   = state.boxOrder.indexOf(state.currentBox);
    const title        = document.getElementById('title-input').value;
    state.boxTitles[state.currentBox] = title;

    try {
        const res = await fetch(SAVE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN':  CSRF_TOKEN,
            },
            body: JSON.stringify({
                box_number:   state.currentBox,
                drawing_data: drawingData,
                title:        title,
                order:        orderIndex >= 0 ? orderIndex + 1 : null,
                time_seconds: getBoxElapsed(state.currentBox),
            }),
        });
        const data = await res.json();
        if (data.success) {
            state.savedBoxes.add(state.currentBox);
            updateDoneDot(state.currentBox, true);
            updateProgress();
        }
    } catch (err) {
        console.error('Error al guardar caja:', err);
    }
}

// ── Carga de imágenes guardadas del servidor ──────────────────────────────
function loadBoxImage(boxNum, dataUrl) {
    const img = new Image();
    img.onload = () => {
        const tmpCanvas = document.createElement('canvas');
        tmpCanvas.width  = CANVAS_RES;
        tmpCanvas.height = CANVAS_RES;
        const tmpCtx = tmpCanvas.getContext('2d');

        // Dibujar la imagen completa (estímulo + dibujo del candidato mezclados)
        tmpCtx.drawImage(img, 0, 0, CANVAS_RES, CANVAS_RES);

        // La parte del dibujo se guarda como ImageData (sin el estímulo)
        // Para cajas ya guardadas, mostramos la imagen compuesta en el stimulus canvas
        // y dejamos el drawing canvas vacío — el evaluador verá la imagen completa
        state.boxDrawings[boxNum] = tmpCtx.getImageData(0, 0, CANVAS_RES, CANVAS_RES);
    };
    img.src = dataUrl;
}

// ── Modal de finalización ──────────────────────────────────────────────────
function showFinishModal() {
    // Guardar la caja actual antes de mostrar modal
    saveCurrentBox(false);

    document.getElementById('modal-count').textContent = state.savedBoxes.size;
    document.getElementById('finish-modal').classList.remove('hidden');
    document.getElementById('finish-modal').style.display = 'flex';
}

function hideModal() {
    document.getElementById('finish-modal').classList.add('hidden');
    document.getElementById('finish-modal').style.display = 'none';
}

async function submitTest() {
    const btn = document.getElementById('btn-confirm-finish');
    btn.textContent = 'Enviando…';
    btn.disabled = true;

    // Guardar caja actual por si no se ha guardado aún
    await saveCurrentBox(false);

    // Enviar formulario de finalización
    document.getElementById('finish-form').submit();
}

// ── Título en memoria ──────────────────────────────────────────────────────
function saveTitleMemory() {
    state.boxTitles[state.currentBox] = document.getElementById('title-input').value;
}

// ── Event listeners del canvas ─────────────────────────────────────────────
drawingCanvas.addEventListener('pointerdown', startDraw);
drawingCanvas.addEventListener('pointermove', draw);
drawingCanvas.addEventListener('pointerup',   stopDraw);
drawingCanvas.addEventListener('pointerleave',stopDraw);
drawingCanvas.addEventListener('pointercancel',stopDraw);

// Touch: prevenir scroll accidental
drawingCanvas.addEventListener('touchstart', e => e.preventDefault(), { passive: false });
drawingCanvas.addEventListener('touchmove',  e => e.preventDefault(), { passive: false });

// ── Arranque ───────────────────────────────────────────────────────────────
init();
</script>

@endsection
