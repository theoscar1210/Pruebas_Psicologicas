@extends('layouts.admin')

@section('title', 'Evaluación Clínica — ' . $candidate->name)
@section('header', match($type) {
    'wartegg'           => 'Wartegg — ' . $candidate->name,
    'star_interview'    => 'Entrevista STAR — ' . $candidate->name,
    'assessment_center' => 'AC-SL — Assessment Center — ' . $candidate->name,
    default             => 'Evaluación — ' . $candidate->name,
})

@php
    $backUrl = request('back') === 'select'
        ? route('admin.assessments.select', $candidate)
        : route('admin.candidates.show', $candidate);
@endphp

@section('header-actions')
    <a href="{{ $backUrl }}" class="btn-ghost btn-sm">← Volver</a>
@endsection

@section('content')

@php
    $isEdit = isset($existing) && $existing;
    $action = $isEdit
        ? route('admin.assessments.update', $existing)
        : route('admin.assessments.store', $candidate);

    $warteggBoxes = [
        ['key' => 'box_1', 'label' => 'Caja 1 — Punto',         'hint' => 'Estabilidad emocional, seguridad interna'],
        ['key' => 'box_2', 'label' => 'Caja 2 — Línea ondulada', 'hint' => 'Flexibilidad, adaptación emocional'],
        ['key' => 'box_3', 'label' => 'Caja 3 — Tres puntos',    'hint' => 'Tendencias de logro, ambición'],
        ['key' => 'box_4', 'label' => 'Caja 4 — Curva pequeña',  'hint' => 'Actitud ante lo nuevo, receptividad'],
        ['key' => 'box_5', 'label' => 'Caja 5 — Triángulo',      'hint' => 'Vitalidad, motricidad'],
        ['key' => 'box_6', 'label' => 'Caja 6 — Línea oblicua',  'hint' => 'Recursos internos, conflictos'],
        ['key' => 'box_7', 'label' => 'Caja 7 — Puntos curvos',  'hint' => 'Vida afectiva, relaciones'],
        ['key' => 'box_8', 'label' => 'Caja 8 — Línea recta',    'hint' => 'Autocontrol, voluntad'],
    ];

    $starCompetencies = [
        ['key' => 'trabajo_equipo',      'label' => 'Trabajo en equipo',        'q' => '¿Cuéntame una situación en la que trabajaste en equipo para lograr un objetivo difícil? ¿Cuál fue tu rol y qué resultados obtuviste?'],
        ['key' => 'liderazgo',           'label' => 'Liderazgo',                'q' => '¿Describe una situación donde tuviste que liderar un grupo bajo presión. Qué hiciste y qué aprendiste?'],
        ['key' => 'resolucion_problemas','label' => 'Resolución de problemas',  'q' => '¿Cuéntame sobre un problema complejo que enfrentaste en el trabajo. ¿Cómo lo diagnosticaste y qué solución implementaste?'],
        ['key' => 'orientacion_cliente', 'label' => 'Orientación al cliente',   'q' => '¿Describe una situación en la que un cliente estaba insatisfecho. ¿Qué hiciste para resolver la situación?'],
        ['key' => 'adaptabilidad',       'label' => 'Adaptabilidad',            'q' => '¿Cuéntame sobre un momento en que tuviste que adaptarte a un cambio importante. ¿Cómo lo manejaste?'],
        ['key' => 'comunicacion',        'label' => 'Comunicación efectiva',    'q' => '¿Describe una situación donde tuviste que comunicar información difícil o compleja. ¿Cómo lo hiciste?'],
        ['key' => 'iniciativa',          'label' => 'Iniciativa y proactividad','q' => '¿Cuéntame sobre una mejora o proyecto que iniciaste por tu cuenta, sin que te lo pidieran.'],
        ['key' => 'manejo_estres',       'label' => 'Manejo del estrés',        'q' => '¿Describe una situación de alta presión laboral. ¿Cómo mantuviste tu efectividad?'],
        ['key' => 'etica_integridad',    'label' => 'Ética e integridad',       'q' => '¿Cuéntame sobre una situación donde debiste tomar una decisión difícil desde el punto de vista ético.'],
        ['key' => 'planificacion',       'label' => 'Planificación y organización','q' => '¿Describe cómo organizas tu trabajo cuando tienes múltiples prioridades simultáneas.'],
    ];

    $scores = $isEdit ? ($existing->scores ?? []) : [];
@endphp

<div class="max-w-4xl">

    {{-- Información del candidato --}}
    <div class="card-info p-4 mb-5 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-brand-700 flex items-center justify-center text-white font-bold flex-shrink-0">
            {{ strtoupper(substr($candidate->name, 0, 1)) }}
        </div>
        <div>
            <p class="font-semibold text-slate-900">{{ $candidate->name }}</p>
            <p class="text-xs text-slate-500">{{ $candidate->position?->name ?? 'Sin cargo asignado' }} · Doc: {{ $candidate->document_number ?? '—' }}</p>
        </div>
        @if($isEdit)
            <span class="ml-auto badge-warning">Editando evaluación existente</span>
        @endif
    </div>

    <form action="{{ $action }}" method="POST">
        @csrf
        @if($isEdit) @method('PUT') @endif
        <input type="hidden" name="assessment_type" value="{{ $type }}">

        {{-- ══ WARTEGG ══════════════════════════════════════════════════════ --}}
        @if($type === 'wartegg')
        @php
        $roman   = ['I','II','III','IV','V','VI','VII','VIII'];
        $wBoxes  = [
            ['key'=>'box_1','label'=>'Campo I — Punto central',         'psych'=>'Yo central · Autoconcepto · Iniciativa',         'org'=>'Autoconcepto e Iniciativa',       'indicators'=>['Tamaño y elaboración del dibujo','Posición respecto al estímulo (centrado/bordes)','Seguridad del trazo (firme/tembloroso)','¿Integra el punto o lo ignora?'],'contents'=>['Figura humana','Paisaje/naturaleza','Objeto cotidiano','Geometría/abstracción','Símbolo'],'alerts'=>['Dibujo que ignora el punto','Figura muy pequeña en borde','Dibujo que encierra el punto sin integrarlo']],
            ['key'=>'box_2','label'=>'Campo II — Línea ondulada',       'psych'=>'Afectividad · Sensibilidad emocional · Adaptación',  'org'=>'Gestión Emocional e Intel. Afectiva','indicators'=>['¿Integra la curva fluidamente?','Calidad orgánica vs. rígida del dibujo','Contenido empático (agua, camino, serpiente)','Nivel de elaboración (rico/pobre)'],'contents'=>['Agua/olas','Serpiente/animal','Camino/paisaje','Figura humana','Geometría'],'alerts'=>['Curva bloqueada o convertida en algo rígido','Dibujo muy pobre o borrado','Dibujo muy fragmentado']],
            ['key'=>'box_3','label'=>'Campo III — Tres puntos',         'psych'=>'Nivel de aspiración · Metas · Orientación al logro',  'org'=>'Orientación al Logro y Productividad','indicators'=>['¿Respeta la diagonal ascendente?','Contenido de logro (escalera, cohete, flecha)','Pensamiento ordenado vs. disperso','¿Integra los tres puntos?'],'contents'=>['Escalera/montaña','Cohete/flecha','Geometría ordenada','Cara/figura','Sin integrar puntos'],'alerts'=>['Diagonal invertida (hacia abajo)','Puntos no integrados en el dibujo','Figura muy simplificada sin relación con los puntos']],
            ['key'=>'box_4','label'=>'Campo IV — Cuadrado negro',       'psych'=>'Relación con la autoridad · La norma · Lo difícil',   'org'=>'Relación con la Autoridad y Normas','indicators'=>['¿Integra el cuadrado creativamente?','¿Lo amplía, minimiza o ignora?','Tono afectivo del dibujo (positivo/amenazante)','Latencia antes de comenzar'],'contents'=>['Casa/edificio','Pantalla/ventana','Piedra/base','Figura amenazante','Campo vacío/muy pobre'],'alerts'=>['Cuadrado amplificado (más grande)','Contenido destructivo (bomba, trampa)','Campo dejado en blanco','Cuadrado completamente ignorado']],
            ['key'=>'box_5','label'=>'Campo V — Ángulo (techo)',        'psych'=>'Dinamismo · Iniciativa conductual · Energía vital',    'org'=>'Energía, Dinamismo y Proactividad','indicators'=>['Tamaño y dinamismo del dibujo','Contenido de movimiento (pájaro, avión)','Presión del trazo','¿Dibujo abierto o cerrado?'],'contents'=>['Pájaro/avión','Montaña/techo','Flecha/cohete','Figura agresiva','Geometría pequeña'],'alerts'=>['Dibujo muy pequeño o encerrado','Contenido agresivo (rayo, arma, choque)','Sin relación con el estímulo']],
            ['key'=>'box_6','label'=>'Campo VI — Ángulo recto',         'psych'=>'Pensamiento lógico · Análisis · Integración razón-emoción','org'=>'Pensamiento Analítico y Estructurado','indicators'=>['¿Integra ambas líneas?','Contenido técnico (tabla, plano, árbol)','Simetría (rigidez vs. creatividad)','¿Ignora una línea?'],'contents'=>['Tabla/gráfico','Árbol/planta','Cruz/símbolo','Arquitectura','Figura asimétrica creativa'],'alerts'=>['Ignora una de las dos líneas','Figura perfectamente simétrica sin variación','Dibujo sin relación con las perpendiculares']],
            ['key'=>'box_7','label'=>'Campo VII — Puntos dispersos',    'psych'=>'Vínculos afectivos · Relaciones interpersonales · Pertenencia','org'=>'Habilidades Sociales y Trabajo en Equipo','indicators'=>['¿Contenido grupal/social?','Figuras humanas (solas o en grupo)','¿Une los puntos (red, constelación)?','Calidez del dibujo'],'contents'=>['Constelación/estrellas','Grupo de personas','Lluvia/nieve','Red/mapa','Figura solitaria'],'alerts'=>['Sin ninguna figura relacional o social','Figuras humanas rígidas o amenazantes','Puntos completamente ignorados']],
            ['key'=>'box_8','label'=>'Campo VIII — Arco abierto',       'psych'=>'Integración del yo · Apertura al mundo · Síntesis',     'org'=>'Adaptabilidad e Integración','indicators'=>['Apertura vs. cierre del dibujo','Contenido de integración (paisaje, horizonte)','¿Uso completo del campo?','Tono emocional (optimista/amenazante)'],'contents'=>['Paisaje/horizonte','Taza/cuenco','Figura humana abierta','Luna/figura cerrada','Dibujo oscuro'],'alerts'=>['Figura cerrada (arco convertido en círculo cerrado)','Contenido oscuro o amenazante','Dibujo que no usa el arco']],
        ];

        $barsLabels = [1=>'Muy deficiente',2=>'Deficiente',3=>'Adecuado',4=>'Bueno',5=>'Muy destacado'];

        $orgDimensions = [
            ['key'=>'org_autoconcepto',      'label'=>'Autoconcepto e Iniciativa',              'campos'=>'I · V · VIII'],
            ['key'=>'org_gestion_emocional', 'label'=>'Gestión Emocional e Inteligencia Afectiva','campos'=>'II · VII · IV'],
            ['key'=>'org_logro',             'label'=>'Orientación al Logro y Productividad',   'campos'=>'III · VI · V'],
            ['key'=>'org_autoridad',         'label'=>'Relación con la Autoridad y las Normas', 'campos'=>'IV · VI'],
            ['key'=>'org_energia',           'label'=>'Energía, Dinamismo y Proactividad',      'campos'=>'V · I · III'],
            ['key'=>'org_analitico',         'label'=>'Pensamiento Analítico y Estructurado',   'campos'=>'VI · III'],
            ['key'=>'org_social',            'label'=>'Habilidades Sociales y Trabajo en Equipo','campos'=>'VII · II · I'],
            ['key'=>'org_adaptabilidad',     'label'=>'Adaptabilidad e Integración',            'campos'=>'VIII · II · III'],
        ];
        @endphp

        {{-- ── Advertencia ética ─────────────────────────────────────────── --}}
        <div class="card border-amber-200 bg-amber-50 mb-5">
            <div class="card-body py-3 flex items-start gap-3">
                <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="text-xs text-amber-800 leading-relaxed">
                    <strong>Uso profesional exclusivo.</strong> Registre solo conductas directamente observadas. Las señales de alerta no son diagnósticos — deben confirmarse con otras fuentes.
                    La interpretación proyectiva requiere formación certificada en técnicas proyectivas (Ley 1090/2006).
                </div>
            </div>
        </div>

        {{-- ── Variables de Secuencia (si hay sesión digital) ────────────── --}}
        @if(isset($warteggSession) && $warteggSession)
        @php
            $wBoxesSorted = collect($warteggSession->boxes ?? [])->sortBy('order')->values();
            $totalMin = $warteggSession->total_seconds ? intdiv($warteggSession->total_seconds, 60) : null;
        @endphp
        <div class="card mb-5 border-violet-200">
            <div class="card-body">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xs font-bold text-violet-700 uppercase tracking-wider">Sesión digital del candidato</span>
                    <span class="badge-success text-xs flex-shrink-0">Dibujos disponibles</span>
                    @if($totalMin)
                    <span class="ml-auto text-xs text-slate-400">Tiempo total: {{ $totalMin }} min</span>
                    @endif
                </div>
                {{-- Secuencia de realización --}}
                @if($wBoxesSorted->isNotEmpty())
                <div class="mb-3">
                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-2">Secuencia de realización</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($wBoxesSorted as $wb)
                        <span class="inline-flex items-center gap-1 text-xs bg-violet-50 border border-violet-200 text-violet-700 px-2 py-0.5 rounded-full font-mono">
                            {{ $wb['order'] ?? '?' }}° — Campo {{ $roman[($wb['number']-1)] }}
                            @if($wb['time_seconds'] > 0)
                            <span class="text-violet-400">({{ intdiv($wb['time_seconds'],60) }}m {{ $wb['time_seconds']%60 }}s)</span>
                            @endif
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
                <p class="text-[11px] text-slate-400">
                    Completado: {{ $warteggSession->completed_at?->format('d/m/Y H:i') }}
                    · {{ $warteggSession->completedBoxesCount() }}/8 campos con dibujo
                </p>
            </div>
        </div>
        @endif

        {{-- ══ ANÁLISIS POR CAMPO ══════════════════════════════════════════ --}}
        <div class="space-y-4 mb-6">
            @foreach($wBoxes as $idx => $box)
            @php
                $boxNum  = $idx + 1;
                $wbData  = isset($warteggSession) ? $warteggSession->getBox($boxNum) : null;
                $hasImg  = $wbData && !empty($wbData['drawing_data']);
            @endphp
            <div class="card border {{ $hasImg ? 'border-violet-100' : 'border-slate-100' }}">
                <div class="card-body">

                    {{-- Header del campo --}}
                    <div class="flex items-start gap-3 mb-4">
                        <span class="font-mono text-xs font-bold px-2 py-1 rounded bg-slate-100 text-slate-600 flex-shrink-0">{{ $roman[$idx] }}</span>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-slate-800 text-sm">{{ $box['label'] }}</h3>
                            <p class="text-xs text-slate-400">{{ $box['psych'] }}</p>
                        </div>
                        @if($hasImg && !empty($wbData['title']))
                        <span class="text-xs text-slate-500 italic flex-shrink-0">"{{ $wbData['title'] }}"</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                        {{-- Columna izquierda: dibujo + metadata --}}
                        <div>
                            @if($hasImg)
                            <div class="mb-2">
                                <img src="{{ $wbData['drawing_data'] }}"
                                     alt="Dibujo Campo {{ $roman[$idx] }}"
                                     class="w-full max-w-[220px] aspect-square object-cover rounded-lg border border-violet-200 shadow-sm">
                                <div class="flex gap-3 mt-2 text-[11px] text-slate-400">
                                    @if($wbData['order']) <span>Orden: <strong class="text-slate-600">{{ $wbData['order'] }}°</strong></span> @endif
                                    @if($wbData['time_seconds'] > 0)
                                    <span>Tiempo: <strong class="text-slate-600">{{ intdiv($wbData['time_seconds'],60) }}m {{ $wbData['time_seconds']%60 }}s</strong></span>
                                    @endif
                                </div>
                            </div>
                            @else
                            <div class="w-full max-w-[220px] aspect-square rounded-lg border-2 border-dashed border-slate-200 flex items-center justify-center mb-2 bg-slate-50">
                                <span class="text-xs text-slate-300">Sin dibujo digital</span>
                            </div>
                            @endif

                            {{-- Categoría de contenido --}}
                            <div class="form-group mb-2">
                                <label class="form-label text-[11px]">Categoría de contenido</label>
                                <select name="scores[cat_{{ $box['key'] }}]" class="select text-xs py-1">
                                    <option value="">— Seleccionar —</option>
                                    @foreach($box['contents'] as $cat)
                                    <option value="{{ Str::slug($cat) }}"
                                        {{ ($scores['cat_'.$box['key']] ?? '') === Str::slug($cat) ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                    @endforeach
                                    <option value="otro" {{ ($scores['cat_'.$box['key']] ?? '') === 'otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                            </div>

                            {{-- Alertas observadas --}}
                            <div class="form-group mb-0">
                                <label class="form-label text-[11px]">Señales de alerta observadas</label>
                                @foreach($box['alerts'] as $aIdx => $alert)
                                <label class="flex items-start gap-2 text-xs text-slate-600 mb-1 cursor-pointer">
                                    <input type="checkbox"
                                           name="scores[alert_{{ $box['key'] }}_{{ $aIdx }}]"
                                           value="1"
                                           {{ !empty($scores['alert_'.$box['key'].'_'.$aIdx]) ? 'checked' : '' }}
                                           class="mt-0.5 rounded flex-shrink-0">
                                    {{ $alert }}
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Columna derecha: indicadores + calificación --}}
                        <div>
                            {{-- Variables formales a observar --}}
                            <details class="group mb-3">
                                <summary class="cursor-pointer select-none text-[11px] font-semibold text-slate-500 hover:text-slate-700 flex items-center gap-1 py-1">
                                    <svg class="w-3 h-3 transition-transform group-open:rotate-90 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    Variables formales a observar
                                </summary>
                                <ul class="mt-2 space-y-1 pl-4">
                                    @foreach($box['indicators'] as $ind)
                                    <li class="text-xs text-slate-500 flex items-start gap-1.5">
                                        <span class="text-slate-300 flex-shrink-0">•</span>{{ $ind }}
                                    </li>
                                    @endforeach
                                </ul>
                            </details>

                            {{-- Calificación de calidad gráfica (1–5) --}}
                            <div class="mb-3">
                                <p class="form-label text-[11px] mb-2">Calidad gráfica integrada (1–5)</p>
                                <div class="flex gap-1.5">
                                    @for($i = 1; $i <= 5; $i++)
                                    <label class="flex-1 cursor-pointer relative">
                                        <input type="radio" name="scores[{{ $box['key'] }}]" value="{{ $i }}"
                                               {{ ($scores[$box['key']] ?? null) == $i ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="py-1.5 text-center text-xs font-bold rounded-lg border-2 transition-all select-none
                                                    border-slate-200 bg-white text-slate-400
                                                    peer-checked:border-violet-500 peer-checked:bg-violet-50 peer-checked:text-violet-700">
                                            {{ $i }}
                                        </div>
                                    </label>
                                    @endfor
                                </div>
                                <div class="flex justify-between text-[9px] text-slate-400 mt-1">
                                    <span>Muy deficiente</span><span>Muy destacado</span>
                                </div>
                            </div>

                            {{-- Observaciones por campo --}}
                            <div class="form-group mb-0">
                                <label class="form-label text-[11px]">Observaciones de este campo</label>
                                <textarea name="scores[obs_{{ $box['key'] }}]" rows="3"
                                          class="textarea text-xs"
                                          placeholder="Descripción del dibujo, conductas relevantes, aspectos a integrar en el análisis…">{{ $scores['obs_'.$box['key']] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>

        {{-- ══ DIMENSIONES ORGANIZACIONALES ══════════════════════════════ --}}
        <div class="card mb-5 border-brand-200">
            <div class="card-body">
                <h2 class="text-sm font-semibold text-brand-700 mb-1">Dimensiones Organizacionales WZT-SL</h2>
                <p class="text-xs text-slate-400 mb-5">
                    Califica cada dimensión tras integrar la información de todos los campos. Una calificación aquí
                    requiere analizar el protocolo completo — no se infiere de un solo campo.
                    Este puntaje determina la <strong>calificación global</strong> del test.
                </p>

                <div class="space-y-3">
                    @foreach($orgDimensions as $dim)
                    <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                        <div class="flex items-start justify-between gap-4 flex-wrap mb-2">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-800">{{ $dim['label'] }}</p>
                                <p class="text-[11px] text-slate-400">Campos principales: {{ $dim['campos'] }}</p>
                            </div>
                            <div class="flex gap-1.5 flex-shrink-0">
                                @for($i = 1; $i <= 5; $i++)
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="scores[{{ $dim['key'] }}]" value="{{ $i }}"
                                           {{ ($scores[$dim['key']] ?? null) == $i ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-9 h-9 flex items-center justify-center text-sm font-bold rounded-lg border-2 transition-all select-none
                                                border-slate-200 bg-white text-slate-400
                                                peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-700">
                                        {{ $i }}
                                    </div>
                                </label>
                                @endfor
                            </div>
                        </div>
                        <div class="flex items-center gap-4 mt-1">
                            <label class="flex items-center gap-1.5 text-xs text-slate-500 cursor-pointer">
                                <input type="radio" name="scores[fa_{{ $dim['key'] }}]" value="fortaleza"
                                       {{ ($scores['fa_'.$dim['key']] ?? '') === 'fortaleza' ? 'checked' : '' }}>
                                <span class="text-emerald-600 font-medium">Fortaleza</span>
                            </label>
                            <label class="flex items-center gap-1.5 text-xs text-slate-500 cursor-pointer">
                                <input type="radio" name="scores[fa_{{ $dim['key'] }}]" value="area_desarrollo"
                                       {{ ($scores['fa_'.$dim['key']] ?? '') === 'area_desarrollo' ? 'checked' : '' }}>
                                <span class="text-amber-600 font-medium">Área de desarrollo</span>
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ══ SÍNTESIS GLOBAL ══════════════════════════════════════════════ --}}
        <div class="card mb-5">
            <div class="card-body">
                <h2 class="text-sm font-semibold text-slate-700 mb-4">Síntesis Interpretativa Global</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="form-group mb-0">
                        <label class="form-label">Fortalezas identificadas</label>
                        <textarea name="scores[sintesis_fortalezas]" rows="3" class="textarea text-xs"
                            placeholder="Recursos, fortalezas y aspectos positivos observados en el protocolo…">{{ $scores['sintesis_fortalezas'] ?? '' }}</textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Áreas de desarrollo / Alertas</label>
                        <textarea name="scores[sintesis_alertas]" rows="3" class="textarea text-xs"
                            placeholder="Indicadores de alerta, áreas de desarrollo y aspectos a profundizar en entrevista…">{{ $scores['sintesis_alertas'] ?? '' }}</textarea>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">Recomendación para el cargo</label>
                    <div class="flex gap-3 flex-wrap">
                        @foreach(['recomendado'=>['Recomendado','badge-success'],'con_reservas'=>['Recomendado con reservas','badge-warning'],'no_recomendado'=>['No recomendado','badge-danger']] as $val=>[$label,$cls])
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="scores[recomendacion]" value="{{ $val }}"
                                   {{ ($scores['recomendacion'] ?? '') === $val ? 'checked' : '' }}>
                            <span class="{{ $cls }} text-xs">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ ENTREVISTA STAR ═══════════════════════════════════════════════ --}}
        @elseif($type === 'star_interview')

        <div class="card mb-5">
            <div class="card-body">
                <h2 class="text-sm font-semibold text-slate-700 mb-1">Entrevista Estructurada STAR — Calificación por competencia</h2>
                <p class="text-xs text-slate-400 mb-5">Conduce la entrevista con cada pregunta. Califica la respuesta del candidato del 1 al 5 según la calidad de la Situación, Tarea, Acción y Resultado descrito. Registra tus observaciones al final.</p>

                <div class="space-y-4">
                    @foreach($starCompetencies as $comp)
                    <div class="p-4 border border-slate-100 rounded-xl">
                        <div class="flex items-start justify-between gap-4 flex-wrap">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-slate-800">{{ $comp['label'] }}</p>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed italic">"{{ $comp['q'] }}"</p>
                            </div>
                            <div class="flex gap-1.5 flex-shrink-0">
                                @for($i = 1; $i <= 5; $i++)
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="scores[{{ $comp['key'] }}]" value="{{ $i }}"
                                           {{ ($scores[$comp['key']] ?? null) == $i ? 'checked' : '' }}
                                           class="sr-only peer" required>
                                    <div class="w-9 h-9 flex items-center justify-center text-sm font-bold rounded-lg border-2 transition-all
                                                border-slate-200 bg-white text-slate-400
                                                peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-700">
                                        {{ $i }}
                                    </div>
                                </label>
                                @endfor
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card mb-5">
            <div class="card-body">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Rúbrica de calificación STAR</h3>
                <div class="grid grid-cols-5 gap-2 text-xs text-slate-600">
                    <div class="p-2 bg-red-50 rounded-lg text-center"><strong class="block text-red-700">1 — Insuficiente</strong>No responde con el método STAR. Respuesta vaga o sin evidencia conductual.</div>
                    <div class="p-2 bg-orange-50 rounded-lg text-center"><strong class="block text-orange-700">2 — Básico</strong>Describe situación pero sin detalle de acciones concretas o resultados.</div>
                    <div class="p-2 bg-amber-50 rounded-lg text-center"><strong class="block text-amber-700">3 — Adecuado</strong>Respuesta completa pero poco profunda. Resultados mencionados superficialmente.</div>
                    <div class="p-2 bg-brand-50 rounded-lg text-center"><strong class="block text-brand-700">4 — Bueno</strong>Respuesta sólida con acciones claras y resultados medibles.</div>
                    <div class="p-2 bg-emerald-50 rounded-lg text-center"><strong class="block text-emerald-700">5 — Excelente</strong>Respuesta completa, reflexiva, con impacto demostrable y aprendizaje explícito.</div>
                </div>
            </div>
        </div>

        {{-- ══ AC-SL — ASSESSMENT CENTER PARA SELECCIÓN LABORAL ════════════════ --}}
        @elseif($type === 'assessment_center')

        @php
        $barsLabels = [1 => 'No demostrado', 2 => 'Por debajo', 3 => 'En el nivel', 4 => 'Por encima', 5 => 'Sobresaliente'];

        $acslClusters = [
            [
                'key' => 'liderazgo', 'label' => 'Cluster 1 — Liderazgo y Gestión', 'color' => 'violet',
                'competencies' => [
                    [
                        'key' => 'L1', 'label' => 'L1 — Liderazgo e Influencia',
                        'definition' => 'Capacidad para orientar, motivar y movilizar a otros hacia el logro de objetivos, adaptando el estilo de liderazgo al contexto y gestionando la dinámica grupal.',
                        'indicators' => [
                            'Establece dirección clara y objetivos comprensibles para el grupo.',
                            'Moviliza la participación de miembros menos activos o inhibidos.',
                            'Gestiona tensiones o conflictos sin perder el foco en la tarea.',
                            'Asume responsabilidad por los resultados del equipo.',
                        ],
                        'bars' => [
                            5 => 'Organiza al grupo desde el inicio, asigna roles según fortalezas, gestiona el tiempo y logra que todos contribuyan. Media conflictos con eficacia y redirige hacia el objetivo.',
                            4 => 'Orienta al grupo con claridad y alinea a la mayoría. Maneja situaciones difíciles con aplomo, aunque puede perder el hilo del tiempo o dejar a algunos en segundo plano.',
                            3 => 'Lidera cuando es necesario en situaciones estructuradas. En situaciones ambiguas o con alta tensión grupal, su liderazgo se vuelve reactivo en lugar de proactivo.',
                            2 => 'Intenta liderar pero sin efecto visible. Instrucciones confusas, no gestiona el conflicto o el tiempo. El grupo opera sin dirección clara.',
                            1 => 'No asume ningún rol de liderazgo, o sus intervenciones generan desorganización, tensión o rechazo por parte del grupo.',
                        ],
                        'bei' => [
                            'Cuénteme una situación en la que tuvo que liderar un equipo sin autoridad formal sobre sus integrantes. ¿Cómo logró que lo siguieran?',
                            'Describa una situación en la que un miembro de su equipo no estaba comprometido con el objetivo. ¿Qué hizo? ¿Cuál fue el resultado?',
                            'Cuénteme sobre una ocasión en que tuvo que tomar decisiones impopulares para el equipo. ¿Cómo las comunicó y cómo manejó la resistencia?',
                        ],
                    ],
                    [
                        'key' => 'L2', 'label' => 'L2 — Toma de Decisiones',
                        'definition' => 'Capacidad para analizar información disponible, evaluar alternativas con criterio y elegir cursos de acción bajo presión o incertidumbre, asumiendo las consecuencias.',
                        'indicators' => [
                            'Prioriza correctamente las situaciones según urgencia e impacto.',
                            'Fundamenta sus decisiones en información relevante, no en suposiciones.',
                            'Reconoce cuándo escalar una decisión vs. resolverla autónomamente.',
                            'Actúa con decisión sin paralizarse ante la ambigüedad.',
                        ],
                        'bars' => [
                            5 => 'Prioriza con criterio explícito, identifica lo urgente vs. lo importante, toma decisiones bien argumentadas y anticipa consecuencias. Delega con precisión y documenta su razonamiento.',
                            4 => 'Prioriza correctamente la mayoría de situaciones y argumenta decisiones con claridad. Puede subestimar alguna consecuencia secundaria o tardar ante alta ambigüedad.',
                            3 => 'Resuelve situaciones de prioridad evidente pero tiene dificultad con información incompleta. Sus decisiones son funcionales aunque no siempre óptimas.',
                            2 => 'Prioriza inconsistentemente, mezcla lo urgente con lo superficial, o toma decisiones sin fundamentación. Se nota indecisión ante situaciones complejas.',
                            1 => 'No toma decisiones significativas, o las decisiones son claramente contraproducentes y sin justificación lógica.',
                        ],
                        'bei' => [
                            'Describa la decisión más difícil que haya tomado en su vida profesional. ¿Con qué información contaba? ¿Qué proceso siguió? ¿Qué resultó?',
                            'Cuénteme una situación en la que tomó una decisión con información incompleta y bajo presión de tiempo. ¿Qué priorizó y por qué?',
                            'Cuénteme sobre una vez en que tomó una decisión equivocada. ¿Cómo lo identificó? ¿Qué hizo al respecto?',
                        ],
                    ],
                ],
            ],
            [
                'key' => 'relaciones', 'label' => 'Cluster 2 — Relaciones Interpersonales', 'color' => 'sky',
                'competencies' => [
                    [
                        'key' => 'R1', 'label' => 'R1 — Trabajo en Equipo',
                        'definition' => 'Capacidad para colaborar activamente, aportar valor al logro colectivo, reconocer el aporte de otros y subordinar el interés individual al resultado del equipo.',
                        'indicators' => [
                            'Escucha y construye sobre las ideas de otros, no solo propone las propias.',
                            'Apoya a compañeros en dificultad sin que se lo soliciten.',
                            'Cede terreno cuando la propuesta de otro es más sólida.',
                            'No acapara protagonismo ni excluye a miembros del grupo.',
                        ],
                        'bars' => [
                            5 => 'Genera condiciones activas para que todos participen. Construye sobre ideas de otros, cede sin conflicto cuando hay mejor propuesta y celebra el logro colectivo sobre el individual.',
                            4 => 'Colabora con solidez y apoya a sus compañeros. En ocasiones algo competitivo por el protagonismo, pero reencuadra sin intervención externa.',
                            3 => 'Participa y aporta. Colabora cuando se le incluye, pero no siempre toma iniciativa de integrar a otros o de ceder la palabra.',
                            2 => 'Tiende a trabajar de forma individual dentro del grupo. Aporta sus ideas pero escucha poco, interrumpe o compite en lugar de construir.',
                            1 => 'Actitud individualista o destructiva. Bloquea las ideas de otros, monopoliza el espacio o se desconecta de la tarea colectiva.',
                        ],
                        'bei' => [
                            'Descríbame una situación en que un proyecto dependía totalmente del trabajo coordinado con otros. ¿Cuál fue su rol y cómo contribuyó al resultado colectivo?',
                            'Cuénteme una vez en que tuvo que ceder su posición o propuesta en favor de la de un compañero. ¿Qué ocurrió?',
                            'Cuénteme sobre una situación de conflicto interno en un equipo del que hacía parte. ¿Cómo lo manejó?',
                        ],
                    ],
                    [
                        'key' => 'R2', 'label' => 'R2 — Comunicación Efectiva',
                        'definition' => 'Capacidad para transmitir ideas con claridad y estructura, escuchar activamente, adaptarse al interlocutor y usar el lenguaje verbal y no verbal de forma coherente.',
                        'indicators' => [
                            'Expresa ideas de forma organizada, concisa y comprensible.',
                            'Escucha sin interrumpir y hace preguntas de clarificación pertinentes.',
                            'Adapta el registro y vocabulario al interlocutor o audiencia.',
                            'Su lenguaje no verbal (postura, contacto visual, tono) es coherente con el mensaje.',
                        ],
                        'bars' => [
                            5 => 'Se expresa con estructura impecable (contexto→argumento→conclusión), adapta el registro con naturalidad y usa el silencio estratégicamente. Genera comprensión inmediata.',
                            4 => 'Comunica con claridad y estructura. Escucha bien aunque puede interrumpir ocasionalmente. Lenguaje no verbal positivo. Puede mejorar en adaptación ante interlocutores muy distintos.',
                            3 => 'La comunicación es comprensible aunque no siempre bien estructurada. Tiende a extenderse o usar jerga. La escucha activa es inconsistente.',
                            2 => 'El mensaje es difícil de seguir. Habla en exceso o muy entrecortado. Interrumpe con frecuencia o no responde al fondo de lo que se le pregunta.',
                            1 => 'Comunicación ineficaz que genera confusión o malentendidos. Lenguaje no verbal contradictorio (agresividad, desconexión evidente).',
                        ],
                        'bei' => [
                            'Describa una situación en la que tuvo que comunicar información técnica compleja a una audiencia sin conocimiento del tema. ¿Cómo lo preparó y qué resultado tuvo?',
                            'Cuénteme sobre una situación en la que su mensaje fue malinterpretado. ¿Cómo lo identificó y cómo lo resolvió?',
                            'Descríbame una ocasión en que tuvo que dar retroalimentación difícil a alguien. ¿Cómo estructuró el mensaje?',
                        ],
                    ],
                    [
                        'key' => 'R3', 'label' => 'R3 — Orientación al Cliente',
                        'definition' => 'Disposición genuina para identificar y anticipar las necesidades del cliente interno o externo, generando soluciones que superen sus expectativas.',
                        'indicators' => [
                            'Hace preguntas para entender la necesidad real del cliente, no solo la solicitada.',
                            'Propone soluciones personalizadas, no respuestas genéricas.',
                            'Mantiene la calma y empatía ante clientes difíciles o reclamaciones.',
                            'Da seguimiento y verifica la satisfacción del cliente.',
                        ],
                        'bars' => [
                            5 => 'Indaga la necesidad real detrás de la solicitud, propone soluciones a medida con justificación clara, mantiene compostura ante alta presión y establece compromisos concretos con plazos.',
                            4 => 'Identifica la necesidad del cliente con claridad y ofrece soluciones relevantes. Maneja bien situaciones de tensión. Puede ser algo reactivo en el seguimiento.',
                            3 => 'Responde las solicitudes adecuadamente pero sin profundizar en la necesidad de fondo. Maneja la interacción sin conflicto, aunque con poca personalización.',
                            2 => 'Responde de forma genérica o protocolaria. Ante un cliente difícil puede perder la empatía o adoptar una postura defensiva.',
                            1 => 'Ignora las necesidades del cliente, responde con irritación o indiferencia, o genera escalamiento del conflicto.',
                        ],
                        'bei' => [
                            'Cuénteme sobre el cliente más difícil que haya tenido que manejar. ¿Qué lo hacía difícil? ¿Qué hizo? ¿Cómo terminó?',
                            'Describa una situación en que identificó una necesidad del cliente que él mismo no había verbalizado. ¿Cómo la detectó y qué hizo?',
                            'Cuénteme una situación en la que excedió las expectativas de un cliente. ¿Qué hizo diferente?',
                        ],
                    ],
                ],
            ],
            [
                'key' => 'desempeno', 'label' => 'Cluster 3 — Desempeño y Resultados', 'color' => 'emerald',
                'competencies' => [
                    [
                        'key' => 'D1', 'label' => 'D1 — Orientación a Resultados',
                        'definition' => 'Capacidad para establecer metas ambiciosas, actuar con sentido de urgencia, superar obstáculos con persistencia y responsabilizarse de los resultados obtenidos.',
                        'indicators' => [
                            'Plantea acciones concretas y plazos, no solo intenciones.',
                            'Mantiene el foco en el objetivo cuando surgen distractores.',
                            'Supera obstáculos sin esperar apoyo externo cuando está en su mano.',
                            'Evalúa si los resultados obtenidos son suficientes o necesitan ajuste.',
                        ],
                        'bars' => [
                            5 => 'Establece metas con métricas desde el inicio, articula el plan con hitos concretos, anticipa obstáculos y tiene alternativas preparadas. Se responsabiliza explícitamente de los resultados.',
                            4 => 'Actúa con sentido de urgencia y logra resultados en plazos previstos. Puede subestimar algún obstáculo, pero se adapta con rapidez. Asume responsabilidad con naturalidad.',
                            3 => 'Completa las tareas con consistencia en condiciones normales, pero puede perder foco ante obstáculos no previstos o situaciones complejas.',
                            2 => 'Plantea intenciones sin plan concreto. Se dispersa con facilidad o busca apoyo ante el primer obstáculo. Los resultados son parciales o fuera de plazo.',
                            1 => 'No demuestra orientación al logro. Evita comprometerse con resultados, justifica los fracasos externalizando y no completa las tareas del ejercicio.',
                        ],
                        'bei' => [
                            'Cuénteme sobre el logro del que más se enorgullece en su vida profesional. ¿Cuál era el objetivo? ¿Qué obstáculos enfrentó? ¿Qué hizo para superarlos?',
                            'Descríbame una situación en la que un proyecto estuvo en riesgo de no cumplir el plazo o la meta. ¿Qué hizo usted específicamente para resolverlo?',
                            'Cuénteme sobre una ocasión en que no cumplió un resultado esperado. ¿Qué ocurrió? ¿Qué aprendió?',
                        ],
                    ],
                    [
                        'key' => 'D2', 'label' => 'D2 — Pensamiento Analítico',
                        'definition' => 'Capacidad para descomponer problemas complejos, identificar causas raíz, establecer relaciones lógicas entre variables y proponer soluciones estructuradas basadas en evidencia.',
                        'indicators' => [
                            'Diferencia síntomas de causas raíz en un problema complejo.',
                            'Usa datos o criterios explícitos para fundamentar su análisis.',
                            'Estructura su razonamiento de forma lógica y secuencial.',
                            'Anticipa consecuencias o efectos secundarios de las soluciones propuestas.',
                        ],
                        'bars' => [
                            5 => 'Descompone el problema con un marco explícito (causa-efecto, árbol de problemas), identifica la causa raíz con claridad, prioriza variables críticas y anticipa efectos de segundo orden.',
                            4 => 'Analiza el problema con solidez e identifica las variables principales. El razonamiento es lógico y bien estructurado. Puede pasar por alto algún efecto secundario.',
                            3 => 'Identifica los elementos visibles del problema y propone soluciones funcionales. El análisis es correcto pero superficial; no profundiza en causas raíz.',
                            2 => 'El análisis se limita a síntomas evidentes. Las soluciones son genéricas o no están vinculadas a las causas del problema. El razonamiento es parcial.',
                            1 => 'No demuestra capacidad analítica. Las conclusiones no se derivan del análisis o son evidentemente incorrectas o irrelevantes.',
                        ],
                        'bei' => [
                            'Describa el problema más complejo que haya tenido que analizar en su trabajo. ¿Cómo lo abordó? ¿Qué herramientas o métodos usó?',
                            'Cuénteme sobre una situación en que la causa real de un problema era diferente a la que todo el mundo asumía. ¿Cómo lo identificó usted?',
                            'Cuénteme sobre una decisión que tomó basada en datos y análisis que resultó diferente a lo que la intuición indicaba. ¿Qué ocurrió?',
                        ],
                    ],
                    [
                        'key' => 'D3', 'label' => 'D3 — Adaptabilidad',
                        'definition' => 'Capacidad para ajustar el comportamiento, las estrategias y los planes frente a cambios, situaciones imprevistas o información nueva, sin perder efectividad.',
                        'indicators' => [
                            'Reencuadra su plan sin resistencia cuando cambian las condiciones.',
                            'Mantiene el desempeño funcional ante la ambigüedad o la presión.',
                            'No se aferra a soluciones obsoletas cuando la evidencia indica un cambio.',
                            'Expresa apertura genuina ante retroalimentación correctiva.',
                        ],
                        'bars' => [
                            5 => 'Ante un cambio repentino, reencuadra rápidamente sin señales visibles de estrés, ajusta el plan con criterio y mantiene o mejora su efectividad. Modela la calma para el equipo.',
                            4 => 'Se adapta con fluidez a los cambios y mantiene la efectividad. Puede mostrar una breve reacción inicial pero se reencuadra con rapidez y autonomía.',
                            3 => 'Se adapta cuando los cambios son moderados. Ante cambios abruptos puede perder eficiencia temporalmente, aunque logra recuperarla.',
                            2 => 'Muestra resistencia visible ante los cambios. Insiste en el plan original aunque haya perdido viabilidad. Necesita intervención externa para reencuadrarse.',
                            1 => 'No logra adaptarse. La resistencia al cambio afecta su desempeño y el del equipo. Puede generar conflicto o bloqueo ante la nueva información.',
                        ],
                        'bei' => [
                            'Cuénteme sobre una situación en la que las condiciones de un proyecto cambiaron radicalmente a mitad del camino. ¿Cómo respondió?',
                            'Describa una situación en la que tuvo que aprender algo completamente nuevo en muy poco tiempo para cumplir una responsabilidad. ¿Cómo lo hizo?',
                            'Cuénteme sobre una ocasión en que recibió retroalimentación que no esperaba y con la que inicialmente no estaba de acuerdo. ¿Qué hizo?',
                        ],
                    ],
                ],
            ],
        ];

        $clusterBadge  = ['violet' => 'bg-violet-100 text-violet-700',  'sky' => 'bg-sky-100 text-sky-700',  'emerald' => 'bg-emerald-100 text-emerald-700'];
        $clusterBorder = ['violet' => 'border-violet-100',               'sky' => 'border-sky-100',            'emerald' => 'border-emerald-100'];
        @endphp

        {{-- BARS reference card --}}
        <div class="card mb-5">
            <div class="card-body">
                <h2 class="text-sm font-semibold text-slate-700 mb-1">AC-SL — Escala BARS de Calificación (1–5)</h2>
                <p class="text-xs text-slate-400 mb-4">Califica el puntaje final integrado por competencia. Registra únicamente conductas directamente observadas — nunca rasgos inferidos.</p>
                <div class="grid grid-cols-5 gap-2 text-xs">
                    <div class="p-2 bg-red-50 rounded-lg text-center">
                        <strong class="block text-red-700 mb-1">1 — No demostrado</strong>
                        <span class="text-red-600/80">No se observaron conductas que evidencien la competencia.</span>
                    </div>
                    <div class="p-2 bg-orange-50 rounded-lg text-center">
                        <strong class="block text-orange-700 mb-1">2 — Por debajo</strong>
                        <span class="text-orange-600/80">Conductas parciales o inconsistentes con vacíos evidentes.</span>
                    </div>
                    <div class="p-2 bg-amber-50 rounded-lg text-center">
                        <strong class="block text-amber-700 mb-1">3 — En el nivel</strong>
                        <span class="text-amber-600/80">Cumple el estándar del cargo en condiciones normales.</span>
                    </div>
                    <div class="p-2 bg-brand-50 rounded-lg text-center">
                        <strong class="block text-brand-700 mb-1">4 — Por encima</strong>
                        <span class="text-brand-600/80">Supera lo esperado con frecuencia en situaciones complejas.</span>
                    </div>
                    <div class="p-2 bg-emerald-50 rounded-lg text-center">
                        <strong class="block text-emerald-700 mb-1">5 — Sobresaliente</strong>
                        <span class="text-emerald-600/80">Supera ampliamente las expectativas. Modelo de referencia.</span>
                    </div>
                </div>
            </div>
        </div>

        @foreach($acslClusters as $cluster)
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $clusterBadge[$cluster['color']] }}">
                    {{ $cluster['label'] }}
                </span>
            </div>
            <div class="space-y-3">
                @foreach($cluster['competencies'] as $comp)
                <div class="card border {{ $clusterBorder[$cluster['color']] }}">
                    <div class="card-body">

                        {{-- Header --}}
                        <div class="flex items-start gap-3 mb-4">
                            <span class="font-mono text-xs font-bold px-2 py-1 rounded flex-shrink-0 {{ $clusterBadge[$cluster['color']] }}">{{ $comp['key'] }}</span>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-slate-800">{{ $comp['label'] }}</h3>
                                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $comp['definition'] }}</p>
                            </div>
                        </div>

                        {{-- BARS selector --}}
                        <div class="mb-4">
                            <p class="form-label text-xs mb-2">Puntaje BARS final integrado</p>
                            <div class="grid grid-cols-5 gap-2">
                                @for($val = 1; $val <= 5; $val++)
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="scores[{{ $comp['key'] }}]" value="{{ $val }}"
                                           {{ ($scores[$comp['key']] ?? null) == $val ? 'checked' : '' }}
                                           class="sr-only peer" required>
                                    <div class="py-2 px-1 text-center rounded-lg border-2 transition-all select-none
                                                border-slate-200 bg-white text-slate-400
                                                hover:border-brand-300 hover:bg-slate-50
                                                peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-700">
                                        <span class="block font-bold text-sm">{{ $val }}</span>
                                        <span class="block text-[9px] leading-tight mt-0.5">{{ $barsLabels[$val] }}</span>
                                    </div>
                                </label>
                                @endfor
                            </div>
                        </div>

                        {{-- Behavioral indicators + BARS anchors (collapsible) --}}
                        <details class="group">
                            <summary class="cursor-pointer select-none flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-700 py-1">
                                <svg class="w-3 h-3 transition-transform group-open:rotate-90 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                Indicadores conductuales y descriptores BARS
                            </summary>
                            <div class="mt-3 pt-3 border-t border-slate-100 space-y-4">
                                <div>
                                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-2">Conductas a observar</p>
                                    <ul class="space-y-1.5">
                                        @foreach($comp['indicators'] as $indicator)
                                        <li class="flex items-start gap-2 text-xs text-slate-600">
                                            <span class="text-slate-300 flex-shrink-0 mt-0.5">•</span>
                                            {{ $indicator }}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-2">Descriptores BARS por nivel</p>
                                    <div class="space-y-2">
                                        @foreach(array_reverse($comp['bars'], true) as $bLevel => $bDesc)
                                        @php
                                            $bLabelClass = match((int)$bLevel) {
                                                5 => 'bg-emerald-100 text-emerald-700',
                                                4 => 'bg-brand-100 text-brand-700',
                                                3 => 'bg-amber-100 text-amber-700',
                                                2 => 'bg-orange-100 text-orange-700',
                                                default => 'bg-red-100 text-red-700',
                                            };
                                        @endphp
                                        <div class="flex gap-2 items-start">
                                            <span class="flex-shrink-0 text-[11px] font-bold px-1.5 py-0.5 rounded {{ $bLabelClass }}">{{ $bLevel }}</span>
                                            <p class="text-xs text-slate-600 leading-relaxed">{{ $bDesc }}</p>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </details>

                        {{-- BEI questions (collapsible) --}}
                        <details class="group mt-2">
                            <summary class="cursor-pointer select-none flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-700 py-1">
                                <svg class="w-3 h-3 transition-transform group-open:rotate-90 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                Preguntas BEI — Entrevista Conductual Estructurada
                            </summary>
                            <div class="mt-2 pt-3 border-t border-slate-100 space-y-2">
                                @foreach($comp['bei'] as $bIdx => $bQuestion)
                                <div class="flex gap-2 items-start">
                                    <span class="flex-shrink-0 text-[11px] font-bold text-slate-400 mt-0.5">{{ $bIdx + 1 }}.</span>
                                    <p class="text-xs text-slate-600 italic leading-relaxed">{{ $bQuestion }}</p>
                                </div>
                                @endforeach
                                <p class="text-[10px] text-slate-400 pt-2 border-t border-slate-100">
                                    Sondeo STAR: ¿Cuándo exactamente? · ¿Cuál era su rol específico? · ¿Qué hizo usted, no el equipo? · ¿Cuál fue el resultado medible?
                                </p>
                            </div>
                        </details>

                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        @endif

        {{-- Observaciones generales --}}
        <div class="card mb-5">
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Observaciones clínicas del evaluador</label>
                    <textarea name="observations" rows="5" class="textarea"
                        placeholder="Describe hallazgos relevantes, conductas observadas, aspectos a destacar o señales de alerta…">{{ $isEdit ? $existing->observations : '' }}</textarea>
                    <p class="form-hint">Estas observaciones formarán parte del perfil psicológico del candidato.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ $isEdit ? 'Actualizar evaluación' : 'Guardar evaluación' }}
            </button>
            <a href="{{ $backUrl }}" class="btn-ghost">Cancelar</a>
        </div>
    </form>
</div>

@endsection
