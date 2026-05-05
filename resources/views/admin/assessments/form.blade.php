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

<div class="max-w-4xl pb-48">

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

    {{-- Auditoría (solo en edición Wartegg) --}}
    @if($isEdit && $type === 'wartegg')
    <div class="card border-slate-100 mb-5">
        <div class="card-body py-3">
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Auditoría del protocolo</p>
            <div class="flex flex-wrap gap-x-6 gap-y-1 text-xs text-slate-600">
                <span><span class="text-slate-400">Evaluador:</span>
                    {{ $existing->evaluator?->name ?? 'Desconocido' }}
                </span>
                <span><span class="text-slate-400">Creado:</span>
                    {{ $existing->created_at->format('d/m/Y H:i') }}
                </span>
                <span><span class="text-slate-400">Última modificación:</span>
                    {{ $existing->updated_at->format('d/m/Y H:i') }}
                </span>
                @if($existing->completed_at)
                <span><span class="text-slate-400">Completado:</span>
                    {{ $existing->completed_at->format('d/m/Y H:i') }}
                </span>
                @endif
            </div>
        </div>
    </div>
    @endif

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

        $wBoxBars = [
            1 => [ // Campo I — Punto
                5 => 'Figura elaborada, expansiva e integrada con el punto como núcleo central. Trazo firme, tamaño apropiado, contenido simbólico rico → autoconcepto positivo, seguridad interna sólida.',
                4 => 'Figura integrada con buena elaboración. Trazo estable y tamaño adecuado. Menor riqueza expresiva pero autoconcepto funcional.',
                3 => 'Punto integrado, figura simple o convencional. Trazo adecuado. Autoconcepto presente pero sin profundidad ni diferenciación.',
                2 => 'Punto no integrado o ignorado como núcleo. Figura muy pequeña, periférica o trazo inseguro → fragilidad en la imagen de sí mismo.',
                1 => 'Campo vacío, punto ignorado, o figura primitiva sin relación con el estímulo. Posible dificultad severa de autoconcepto o contacto con la realidad.',
            ],
            2 => [ // Campo II — Línea ondulada
                5 => 'Dibujo fluido y orgánico (agua, paisaje, ser vivo) con la curva naturalmente integrada. Elaboración rica y contenido cálido → flexibilidad emocional e inteligencia afectiva altas.',
                4 => 'Curva integrada con buena fluidez. Contenido empático aunque menos expresivo. Adaptación emocional adecuada.',
                3 => 'Curva integrada pero de forma rígida o convencional. Contenido neutro, poca expresividad emocional. Adaptación funcional.',
                2 => 'Curva convertida en elemento rígido o escasamente integrada. Contenido frío o sin vínculo emocional → rigidez afectiva.',
                1 => 'Dibujo muy pobre o vacío. Curva completamente ignorada o bloqueada. Posible bloqueo emocional significativo.',
            ],
            3 => [ // Campo III — Tres puntos
                5 => 'Diagonal ascendente clara con los tres puntos integrados. Contenido de logro o progresión (escalera, cohete, montaña). Trazo firme → alta orientación al logro y motivación de logro.',
                4 => 'Dos o tres puntos integrados, orientación mayormente ascendente. Contenido de logro aunque menos elaborado. Motivación de logro presente.',
                3 => 'Puntos integrados sin orientación clara. Figura simple o convencional sin elemento de progresión. Nivel de aspiración moderado.',
                2 => 'Diagonal invertida (descendente) o puntos no integrados. Sin carga aspiracional → motivación de logro baja o inhibida.',
                1 => 'Puntos ignorados, figura primitiva o diagonal descendente marcada. Posible desmotivación o dificultad con metas a futuro.',
            ],
            4 => [ // Campo IV — Cuadrado negro
                5 => 'Cuadrado integrado creativamente (ventana, pantalla, base positiva). Tono neutro o positivo → manejo maduro de la autoridad y la norma, sin conflicto.',
                4 => 'Cuadrado integrado con tono neutro o levemente positivo. Figura funcional. Relación con la autoridad adecuada.',
                3 => 'Cuadrado contenido en figura simple sin transformarlo. Tono neutro. Relación con la norma funcional pero sin elaboración.',
                2 => 'Cuadrado amplificado, usado como elemento pesado, o levemente ignorado → posible conflicto con la autoridad o la norma.',
                1 => 'Campo vacío, cuadrado ignorado, o contenido destructivo (bomba, trampa, amenaza). Alta conflictividad con figuras de autoridad.',
            ],
            5 => [ // Campo V — Ángulo (techo)
                5 => 'Dibujo expansivo y dinámico (pájaro, avión, flecha). Trazo enérgico, uso amplio del campo, contenido vital positivo → alto dinamismo, energía e iniciativa conductual.',
                4 => 'Figura dinámica con buen uso del campo. Trazo firme. Contenido de movimiento aunque menos elaborado. Buena energía vital.',
                3 => 'Figura integrada con el ángulo pero sin dinamismo marcado. Tamaño adecuado, contenido neutro. Energía funcional.',
                2 => 'Figura muy pequeña, encerrada o sin relación con el ángulo. Contenido sin movimiento → energía inhibida o baja proactividad.',
                1 => 'Dibujo ausente o muy pequeño. Contenido agresivo o destructivo. Sin dinamismo → bloqueo de la vitalidad o agresividad sin canalización.',
            ],
            6 => [ // Campo VI — Ángulo recto
                5 => 'Ambas líneas integradas armónicamente en figura significativa (árbol, gráfico, arquitectura) con variación creativa. Muestra integración de razón y emoción.',
                4 => 'Ambas líneas integradas, figura bien elaborada. Puede ser algo rígida en la simetría pero funcional. Pensamiento analítico adecuado.',
                3 => 'Ambas líneas integradas de forma simple o convencional. Simetría perfecta sin variación. Pensamiento correcto pero poco flexible.',
                2 => 'Una línea ignorada o figura con asimetría disfuncional. Dificultad para integrar dimensiones distintas del problema.',
                1 => 'Ambas líneas ignoradas, figura sin relación con el estímulo. Posible dificultad en la integración lógica o pensamiento muy concreto.',
            ],
            7 => [ // Campo VII — Puntos dispersos
                5 => 'Contenido grupal o social (constelación, personas en red). Puntos integrados en figura cohesionada. Calidez y riqueza → altas habilidades sociales y sentido de pertenencia.',
                4 => 'Contenido social con puntos bien integrados. Figura cálida aunque menos elaborada. Habilidades sociales presentes.',
                3 => 'Puntos integrados en figura individual o neutral (lluvia, geometría). Sin contenido claramente relacional. Sociabilidad funcional pero no proactiva.',
                2 => 'Puntos escasamente integrados. Sin vínculo relacional. Figuras humanas rígidas o amenazantes → posible dificultad en la pertenencia.',
                1 => 'Puntos completamente ignorados. Sin ninguna figura relacional. Contenido primitivo o aislado → posible dificultad severa en la vinculación.',
            ],
            8 => [ // Campo VIII — Arco abierto
                5 => 'Dibujo abierto que usa el arco como horizonte (paisaje, amanecer, cuenco abierto). Uso completo del campo, tono optimista → alta integración del yo y apertura al entorno.',
                4 => 'Arco integrado con apertura, contenido positivo. Buen uso del campo. Integración del yo adecuada.',
                3 => 'Arco integrado pero con tendencia a cerrarse. Contenido neutro, uso moderado del campo. Apertura funcional.',
                2 => 'Arco muy pequeño o parcialmente cerrado. Contenido sin apertura → defensividad o cierre ante el entorno.',
                1 => 'Arco completamente cerrado (círculo), campo vacío o contenido oscuro. Posible dificultad severa en la apertura o integración del yo.',
            ],
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

        {{-- ── Seguridad y advertencia ética ──────────────────────────────── --}}
        <div class="card border-amber-200 bg-amber-50 mb-5">
            <div class="card-body py-3">
                <div class="flex items-start gap-3">
                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1.5">
                            <strong class="text-xs text-amber-800">Protocolo confidencial — Uso profesional exclusivo</strong>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-200 text-amber-900 uppercase tracking-wide flex-shrink-0">🔒 Confidencial</span>
                        </div>
                        <div class="text-xs text-amber-800 leading-relaxed space-y-1">
                            <p>Registre únicamente conductas directamente observadas en el protocolo. Las señales de alerta no son diagnósticos clínicos: deben confirmarse con múltiples fuentes.</p>
                            <p>La interpretación proyectiva requiere formación certificada (Ley 1090/2006, Art. 36). La custodia de datos sensibles está regulada por la <strong>Ley 1581/2012</strong> (Habeas Data); no comparta capturas de pantalla ni información de este protocolo fuera del sistema.</p>
                        </div>
                    </div>
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

        {{-- ── Integración con otros tests ────────────────────────────────── --}}
        @if(isset($candidateContext) && ($candidateContext['completedTests']->isNotEmpty() || $candidateContext['otherAssessments']->isNotEmpty()))
        @php
        $crossRef = [
            'raven'   => ['Campo III (logro/metas)','Campo VI (análisis)','Campo I (autoconcepto intelectual)'],
            'star_interview' => ['Dimensión Logro (Campo III)','Dimensión Social (Campo VII)','Dimensión Autoridad (Campo IV)'],
            'assessment_center' => ['Dimensión Energía/Proactividad (Campo V)','Dimensión Social (Campo VII)','Dimensión Adaptabilidad (Campo VIII)'],
        ];
        @endphp
        <div class="card mb-5 border-sky-200">
            <div class="card-body">
                <p class="text-xs font-bold text-sky-700 uppercase tracking-wider mb-3">Integración con otros instrumentos psicométricos</p>
                <div class="space-y-2">
                    @foreach($candidateContext['completedTests'] as $ct)
                    <div class="flex items-start gap-3 p-2.5 bg-sky-50/60 rounded-lg">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-slate-700">{{ $ct['name'] }}</p>
                            <p class="text-[11px] text-slate-500">
                                Puntaje: <strong>{{ $ct['score'] }}/{{ $ct['max_score'] }}</strong> ({{ $ct['percentage'] }}%)
                                @if($ct['passed'] !== null)
                                — <span class="{{ $ct['passed'] ? 'text-emerald-600' : 'text-red-600' }} font-medium">{{ $ct['passed'] ? 'Aprobado' : 'No aprobado' }}</span>
                                @endif
                            </p>
                            @if(isset($crossRef[$ct['test_type']]))
                            <p class="text-[10px] text-sky-600 mt-0.5">
                                Contrastar con: {{ implode(', ', $crossRef[$ct['test_type']]) }}
                            </p>
                            @endif
                        </div>
                        <span class="text-[10px] text-slate-400 flex-shrink-0 mt-0.5">{{ optional($ct['completed_at'])->format('d/m/Y') }}</span>
                    </div>
                    @endforeach

                    @foreach($candidateContext['otherAssessments'] as $oa)
                    <div class="flex items-start gap-3 p-2.5 bg-violet-50/50 rounded-lg">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-slate-700">{{ $oa['type_label'] }}</p>
                            <p class="text-[11px] text-slate-500">
                                Puntaje global: <strong>{{ $oa['overall_score'] !== null ? number_format($oa['overall_score'], 1).'/100' : '—' }}</strong>
                            </p>
                            @if(isset($crossRef[$oa['type']]))
                            <p class="text-[10px] text-violet-600 mt-0.5">
                                Contrastar con: {{ implode(', ', $crossRef[$oa['type']]) }}
                            </p>
                            @endif
                        </div>
                        <span class="text-[10px] text-slate-400 flex-shrink-0 mt-0.5">{{ optional($oa['completed_at'])->format('d/m/Y') }}</span>
                    </div>
                    @endforeach
                </div>
                <p class="text-[10px] text-slate-400 mt-3">La integración entre instrumentos refuerza hipótesis pero nunca reemplaza el juicio clínico del evaluador.</p>
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
                            <details class="group mb-2">
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

                            {{-- Guía de calificación BARS por campo --}}
                            <details class="group mb-3">
                                <summary class="cursor-pointer select-none text-[11px] font-semibold text-violet-500 hover:text-violet-700 flex items-center gap-1 py-1">
                                    <svg class="w-3 h-3 transition-transform group-open:rotate-90 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    Guía de interpretación (1–5)
                                </summary>
                                <div class="mt-2 space-y-1.5 pl-1">
                                    @foreach(array_reverse($wBoxBars[$boxNum], true) as $bLevel => $bDesc)
                                    @php
                                        $bCls = match((int)$bLevel) {
                                            5 => 'bg-emerald-100 text-emerald-700',
                                            4 => 'bg-brand-100 text-brand-700',
                                            3 => 'bg-amber-100 text-amber-700',
                                            2 => 'bg-orange-100 text-orange-700',
                                            default => 'bg-red-100 text-red-700',
                                        };
                                    @endphp
                                    <div class="flex gap-2 items-start">
                                        <span class="flex-shrink-0 text-[10px] font-bold px-1.5 py-0.5 rounded {{ $bCls }}">{{ $bLevel }}</span>
                                        <p class="text-[11px] text-slate-600 leading-snug">{{ $bDesc }}</p>
                                    </div>
                                    @endforeach
                                </div>
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

        {{-- ══ EEC-SL — ENTREVISTA ESTRUCTURADA POR COMPETENCIAS ════════════ --}}
        @elseif($type === 'star_interview')
        @php
        // ── Competencias ─────────────────────────────────────────────────────
        $eecComps = [
          'L1'=>['cluster'=>'Liderazgo','label'=>'L1 — Liderazgo e Influencia',
            'definition'=>'Capacidad para orientar, motivar y movilizar a otros hacia el logro de objetivos, adaptando el estilo al contexto y gestionando la dinámica grupal con eficacia.',
            'indicators'=>['Toma iniciativa y establece dirección con claridad.','Moviliza a otros sin necesidad de autoridad formal.','Gestiona conflictos y tensiones grupales de forma constructiva.','Asume responsabilidad por los resultados del equipo.'],
            'questions'=>[
              ['q'=>'Cuénteme sobre una situación en la que tuvo que liderar a un grupo de personas que no le reportaban directamente. ¿Cómo logró que lo siguieran y comprometieran con el objetivo?','probes'=>['S'=>'¿Cuántas personas involucraba el grupo? ¿Cuál era la relación previa con ellas?','T'=>'¿Qué resistencias encontró? ¿Cómo las manejó?','A'=>'¿Qué acciones concretas tomó para generar compromiso?','R'=>'¿Cuál fue el resultado final y qué papel tuvo usted en él?']],
              ['q'=>'Descríbame una situación en la que tomó una decisión impopular que afectaba a su equipo. ¿Cómo la comunicó y cómo manejó la reacción?','probes'=>['S'=>'¿Por qué era impopular la decisión? ¿A quiénes afectaba?','T'=>'¿Cómo decidió el enfoque para comunicarla?','A'=>'¿Cuáles fueron las reacciones y cómo respondió a cada una?','R'=>'¿Qué ocurrió con el equipo y los resultados después?']],
              ['q'=>'Cuénteme sobre un miembro del equipo con bajo desempeño o desmotivado que usted tuvo que gestionar. ¿Qué hizo?','probes'=>['S'=>'¿Cómo identificó que había un problema? ¿Qué señales observó?','T'=>'¿Qué conversaciones tuvo con esa persona y cómo las preparó?','A'=>'¿Qué acciones específicas implementó para apoyarlo o redirigirlo?','R'=>'¿Cuál fue el desenlace?']],
            ]],
          'L2'=>['cluster'=>'Liderazgo','label'=>'L2 — Toma de Decisiones bajo Presión',
            'definition'=>'Capacidad para analizar información disponible, evaluar alternativas con criterio y elegir cursos de acción bajo presión de tiempo, escasez de información o alta ambigüedad.',
            'indicators'=>['Prioriza correctamente lo urgente vs. lo importante.','Fundamenta decisiones en datos y criterios explícitos.','Actúa con decisión sin paralizarse ante la incertidumbre.','Evalúa consecuencias y asume responsabilidad.'],
            'questions'=>[
              ['q'=>'Descríbame la decisión más difícil que haya tenido que tomar en su vida profesional. ¿Con qué información contaba? ¿Qué proceso siguió?','probes'=>['S'=>'¿Por qué era difícil? ¿Qué estaba en juego?','T'=>'¿Qué información tenía disponible y cuál faltaba?','A'=>'¿Qué alternativas consideró antes de decidir?','R'=>'¿Cómo resultó y qué haría diferente hoy?']],
              ['q'=>'Cuénteme sobre una situación en la que tuvo que tomar una decisión crítica con muy poco tiempo y sin poder consultar a nadie. ¿Qué hizo?','probes'=>['S'=>'¿Cuál era el contexto y qué tan urgente era?','T'=>'¿Cómo priorizó qué información era suficiente para decidir?','A'=>'¿Qué criterio usó para elegir entre las opciones disponibles?','R'=>'¿Cuál fue el resultado y cómo lo evaluó posteriormente?']],
              ['q'=>'Cuénteme sobre una ocasión en que tomó una decisión que resultó equivocada. ¿Cómo lo identificó? ¿Qué hizo al respecto?','probes'=>['S'=>'¿En qué momento se dio cuenta de que fue un error?','T'=>'¿Qué hizo para corregir o mitigar el impacto?','A'=>'¿Cómo comunicó el error a quienes correspondía?','R'=>'¿Qué aprendizaje concreto obtuvo de esa experiencia?']],
            ]],
          'R1'=>['cluster'=>'Relaciones','label'=>'R1 — Trabajo en Equipo y Colaboración',
            'definition'=>'Capacidad para colaborar activamente con otros, aportar valor al logro colectivo, construir sinergias y subordinar el interés individual al resultado del equipo.',
            'indicators'=>['Escucha y construye sobre las ideas de otros.','Apoya a compañeros de forma proactiva.','Cede protagonismo cuando la propuesta del otro es más sólida.','Contribuye a un clima positivo de equipo.'],
            'questions'=>[
              ['q'=>'Descríbame un proyecto en el que el éxito dependía completamente de la colaboración con otras personas. ¿Cuál fue su rol y cómo contribuyó específicamente?','probes'=>['S'=>'¿Quiénes eran los otros miembros y qué rol tenía cada uno?','T'=>'¿Qué aportó usted que otros no podían aportar?','A'=>'¿Hubo momentos de tensión o desalineación en el equipo? ¿Cómo los manejó?','R'=>'¿Qué resultó y cuál fue su contribución concreta al resultado?']],
              ['q'=>'Cuénteme sobre una situación en la que un compañero tenía dificultades para cumplir con su parte. ¿Qué hizo usted?','probes'=>['S'=>'¿Cómo se enteró de las dificultades del compañero?','T'=>'¿Cuál fue su decisión: intervenir, no hacerlo? ¿Por qué?','A'=>'¿Qué acciones concretas tomó para apoyarlo sin sobrepasar su autonomía?','R'=>'¿Cómo impactó esto en el resultado y en la relación con esa persona?']],
              ['q'=>'Cuénteme sobre una vez en que tuvo que ceder su posición o propuesta en favor de la de un compañero, aunque inicialmente no estaba de acuerdo.','probes'=>['S'=>'¿En qué contexto ocurrió esto y qué estaba en juego?','T'=>'¿Qué argumentos presentó la otra persona que cambiaron su perspectiva, o simplemente cedió?','A'=>'¿Cómo manejó internamente el hecho de no imponer su criterio?','R'=>'¿Cuál fue el resultado final y cómo lo evalúa hoy?']],
            ]],
          'R2'=>['cluster'=>'Relaciones','label'=>'R2 — Comunicación Efectiva',
            'definition'=>'Capacidad para transmitir ideas con claridad y estructura, escuchar activamente, adaptar el mensaje al interlocutor y usar el lenguaje verbal y no verbal de forma coherente.',
            'indicators'=>['Expresa ideas con estructura clara (contexto→argumento→conclusión).','Escucha activamente y hace preguntas pertinentes de clarificación.','Adapta el registro y vocabulario al interlocutor.','Da y recibe retroalimentación de forma constructiva.'],
            'questions'=>[
              ['q'=>'Descríbame una situación en la que tuvo que comunicar información técnica compleja a una audiencia sin conocimiento del tema. ¿Cómo lo preparó y cómo resultó?','probes'=>['S'=>'¿Quién era la audiencia y qué sabían del tema?','T'=>'¿Cómo decidió qué incluir y qué simplificar?','A'=>'¿Qué recursos o formatos utilizó para facilitar la comprensión?','R'=>'¿Cómo supo que el mensaje fue comprendido? ¿Qué feedback recibió?']],
              ['q'=>'Cuénteme sobre una ocasión en que su mensaje fue malinterpretado por alguien importante. ¿Cómo lo descubrió y cómo lo resolvió?','probes'=>['S'=>'¿Cuál era el mensaje original y cómo fue malinterpretado?','T'=>'¿En qué momento se dio cuenta del malentendido?','A'=>'¿Qué acciones tomó para corregir la percepción?','R'=>'¿Qué consecuencias tuvo el malentendido y cómo las manejó?']],
              ['q'=>'Descríbame una ocasión en que tuvo que dar retroalimentación difícil o negativa a alguien. ¿Cómo preparó y estructuró el mensaje?','probes'=>['S'=>'¿A quién iba dirigida? ¿Cuál era la relación con esa persona?','T'=>'¿Cómo preparó la conversación? ¿Eligió el momento y el lugar?','A'=>'¿Qué estructura siguió para entregar el mensaje?','R'=>'¿Cómo reaccionó la persona y cuál fue el resultado posterior?']],
            ]],
          'R3'=>['cluster'=>'Relaciones','label'=>'R3 — Orientación al Cliente',
            'definition'=>'Disposición para identificar y anticipar las necesidades del cliente interno o externo, generando soluciones que superen sus expectativas y construyendo relaciones de confianza a largo plazo.',
            'indicators'=>['Indaga la necesidad real detrás de la solicitud explícita.','Propone soluciones personalizadas con justificación clara.','Mantiene la calma y la empatía ante clientes difíciles.','Hace seguimiento para verificar satisfacción.'],
            'questions'=>[
              ['q'=>'Cuénteme sobre el cliente más difícil que haya tenido que manejar. ¿Qué lo hacía difícil? ¿Qué hizo usted exactamente?','probes'=>['S'=>'¿Qué tipo de cliente era y cuál era la relación previa?','T'=>'¿Cuál era el motivo concreto de la dificultad?','A'=>'¿Qué estrategia adoptó y qué pasos siguió?','R'=>'¿Cómo terminó la situación y qué aprendió?']],
              ['q'=>'Descríbame una situación en la que identificó una necesidad del cliente que él mismo no había verbalizado. ¿Cómo la detectó y qué hizo con esa información?','probes'=>['S'=>'¿Cuál era el contexto de la interacción con el cliente?','T'=>'¿Qué señales observó que le indicaron la necesidad no verbalizada?','A'=>'¿Cómo actuó sobre esa información? ¿Se lo comunicó? ¿Propuso algo?','R'=>'¿Cuál fue la reacción del cliente y el resultado?']],
              ['q'=>'Cuénteme sobre una situación en la que excedió las expectativas de un cliente. ¿Qué hizo diferente a lo que habría sido el estándar?','probes'=>['S'=>'¿Cuál era la expectativa original del cliente?','T'=>'¿Qué lo motivó a ir más allá del estándar?','A'=>'¿Qué acciones específicas tomó y qué recursos usó?','R'=>'¿Cómo respondió el cliente y qué impacto tuvo esto para la relación o para el negocio?']],
            ]],
          'D1'=>['cluster'=>'Desempeño','label'=>'D1 — Orientación a Resultados',
            'definition'=>'Capacidad para establecer metas ambiciosas, actuar con sentido de urgencia y persistencia ante los obstáculos, y responsabilizarse por los resultados obtenidos.',
            'indicators'=>['Establece metas con métricas concretas, no solo intenciones.','Supera obstáculos sin esperar apoyo externo cuando está en su mano.','Mantiene el foco en el resultado final ante distracciones.','Asume responsabilidad explícita por los resultados, positivos o negativos.'],
            'questions'=>[
              ['q'=>'Cuénteme sobre el logro profesional del que más se enorgullece. ¿Cuál era el objetivo, qué obstáculos enfrentó y qué hizo para superarlos?','probes'=>['S'=>'¿Por qué este logro es el que más lo enorgullece?','T'=>'¿Qué obstáculos concretos enfrentó y cuál fue el más difícil?','A'=>'¿Qué hizo específicamente usted para superarlos?','R'=>'¿Cómo midió el resultado? ¿Tenía una meta numérica o cuantificable?']],
              ['q'=>'Descríbame una situación en la que un proyecto estuvo en riesgo de no cumplirse en el plazo o sin alcanzar la meta. ¿Qué hizo usted?','probes'=>['S'=>'¿Cuándo detectó que estaba en riesgo? ¿Qué señales vio?','T'=>'¿Cuáles eran las causas del riesgo?','A'=>'¿Qué acciones específicas tomó usted (no el equipo) para retomar el camino?','R'=>'¿Cuál fue el resultado final? ¿Se cumplió la meta?']],
              ['q'=>'Cuénteme sobre una ocasión en que no cumplió un resultado esperado. ¿Qué ocurrió, cómo lo comunicó y qué hizo a partir de ahí?','probes'=>['S'=>'¿Cuál era la meta y en qué medida no se cumplió?','T'=>'¿Cuáles fueron las causas reales (no las justificaciones)?','A'=>'¿Cómo comunicó el incumplimiento a quien correspondía?','R'=>'¿Qué aprendizaje concreto y verificable obtuvo?']],
            ]],
          'D2'=>['cluster'=>'Desempeño','label'=>'D2 — Pensamiento Analítico y Resolución de Problemas',
            'definition'=>'Capacidad para descomponer problemas complejos, identificar causas raíz, establecer relaciones lógicas entre variables y proponer soluciones estructuradas basadas en evidencia.',
            'indicators'=>['Diferencia síntomas de causas raíz.','Usa datos o criterios explícitos para fundamentar análisis.','Estructura el razonamiento de forma lógica y secuencial.','Anticipa consecuencias de segundo orden de las soluciones propuestas.'],
            'questions'=>[
              ['q'=>'Descríbame el problema más complejo que haya tenido que resolver en su trabajo. ¿Cómo lo abordó y qué herramientas o métodos utilizó?','probes'=>['S'=>'¿Qué hacía complejo ese problema?','T'=>'¿Cómo lo descompuso o estructuró para abordarlo?','A'=>'¿Qué herramientas, marcos o metodologías utilizó?','R'=>'¿Cuál fue la solución y qué impacto tuvo?']],
              ['q'=>'Cuénteme sobre una situación en la que la causa real de un problema era diferente a lo que todo el mundo asumía. ¿Cómo lo identificó usted?','probes'=>['S'=>'¿Cuál era la causa que todos asumían y cuál era la real?','T'=>'¿Qué lo llevó a cuestionar el diagnóstico común?','A'=>'¿Qué proceso siguió para identificar la causa raíz?','R'=>'¿Cómo convenció a otros de su análisis y qué pasó?']],
              ['q'=>'Cuénteme sobre una decisión que tomó basada en datos que resultó contraria a lo que la intuición o la experiencia del equipo indicaban.','probes'=>['S'=>'¿Cuál era la posición del equipo y qué decían los datos?','T'=>'¿Cómo procesó esa tensión entre intuición y análisis?','A'=>'¿Cómo presentó su análisis y lo defendió ante los demás?','R'=>'¿Cuál fue el resultado y qué validación obtuvo el análisis?']],
            ]],
          'D3'=>['cluster'=>'Desempeño','label'=>'D3 — Adaptabilidad y Gestión del Cambio',
            'definition'=>'Capacidad para ajustar el comportamiento, los planes y las estrategias frente a cambios imprevistos, ambigüedad o nueva información, manteniendo la efectividad.',
            'indicators'=>['Reencuadra el plan sin resistencia cuando cambian las condiciones.','Mantiene el desempeño funcional ante la ambigüedad.','No se aferra a soluciones obsoletas ante nueva evidencia.','Expresa apertura genuina ante retroalimentación correctiva.'],
            'questions'=>[
              ['q'=>'Cuénteme sobre una situación en la que las condiciones de un proyecto cambiaron radicalmente a mitad del camino. ¿Cómo respondió?','probes'=>['S'=>'¿Qué cambió y con qué tan poco aviso?','T'=>'¿Cuál fue su reacción inicial?','A'=>'¿Qué acciones concretas tomó para adaptarse?','R'=>'¿Cómo afectó a los resultados y qué le quedó de aprendizaje?']],
              ['q'=>'Descríbame una situación en la que tuvo que aprender algo completamente nuevo en muy poco tiempo para cumplir una responsabilidad.','probes'=>['S'=>'¿Qué era lo que tenía que aprender y cuánto tiempo tenía?','T'=>'¿Qué estrategia de aprendizaje usó?','A'=>'¿Qué obstáculos encontró en el proceso de aprendizaje acelerado?','R'=>'¿Cómo aplicó ese aprendizaje y cuál fue el resultado?']],
              ['q'=>'Cuénteme sobre una ocasión en que recibió retroalimentación que no esperaba y con la que inicialmente no estaba de acuerdo. ¿Cómo lo procesó y qué hizo?','probes'=>['S'=>'¿De quién vino la retroalimentación y cuál fue el contenido concreto?','T'=>'¿Cuál fue su reacción inmediata, honestamente?','A'=>'¿Qué hizo para procesar si era válida o no?','R'=>'¿Qué cambio condujo esa retroalimentación en su comportamiento?']],
            ]],
          'P1'=>['cluster'=>'Desarrollo','label'=>'P1 — Integridad y Ética Profesional',
            'definition'=>'Capacidad para actuar con coherencia entre los valores declarados y la conducta real, incluso bajo presión, y para mantener estándares éticos ante dilemas o conflictos de interés.',
            'indicators'=>['Actúa con coherencia entre lo que dice y lo que hace.','Comunica malas noticias con transparencia y oportunidad.','Reconoce sus errores sin justificaciones ni evasión.','Mantiene sus principios ante presión o conveniencia personal.'],
            'questions'=>[
              ['q'=>'Cuénteme sobre una situación en la que tuvo que comunicar una mala noticia o un error que podría haberle generado consecuencias negativas. ¿Cómo lo manejó?','probes'=>['S'=>'¿Cuál era la mala noticia o el error?','T'=>'¿Qué consecuencias podía tener para usted decirlo?','A'=>'¿Cómo decidió comunicarlo, cuándo y a quién?','R'=>'¿Cuál fue la reacción y el resultado final?']],
              ['q'=>'Descríbame una situación en la que enfrentó un dilema ético o un conflicto entre lo que le pedían y lo que consideraba correcto.','probes'=>['S'=>'¿Cuál era la situación y quién le pedía actuar de determinada manera?','T'=>'¿Cómo evaluó las implicaciones éticas?','A'=>'¿Qué decisión tomó y cómo la comunicó?','R'=>'¿Cuáles fueron las consecuencias para usted y para otros?']],
              ['q'=>'Cuénteme sobre una vez en que cometió un error significativo en el trabajo. ¿Cómo lo reconoció y cómo lo manejó frente a otros?','probes'=>['S'=>'¿Cuál fue el error y cuál fue su magnitud?','T'=>'¿Cuánto tiempo pasó entre el error y el momento en que lo reconoció?','A'=>'¿Cómo lo comunicó a las personas afectadas o implicadas?','R'=>'¿Qué consecuencias tuvo y qué aprendió?']],
            ]],
          'P2'=>['cluster'=>'Desarrollo','label'=>'P2 — Autodesarrollo y Aprendizaje Continuo',
            'definition'=>'Disposición proactiva para identificar las propias brechas de conocimiento o habilidad, buscar activamente las oportunidades de aprendizaje y aplicar lo aprendido para mejorar el desempeño.',
            'indicators'=>['Identifica sus propias brechas sin que se las señalen.','Busca retroalimentación proactivamente.','Aplica lo aprendido de forma medible en el trabajo.','Aprende tanto de los éxitos como de los fracasos.'],
            'questions'=>[
              ['q'=>'Descríbame una situación en la que identificó por iniciativa propia una brecha en sus habilidades y tomó acciones para cerrarla. ¿Qué hizo?','probes'=>['S'=>'¿Cómo identificó que tenía esa brecha?','T'=>'¿Qué plan de acción diseñó para cerrarla?','A'=>'¿Qué recursos utilizó (cursos, mentores, práctica deliberada)?','R'=>'¿Cómo midió que había progresado y cuándo lo aplicó en el trabajo?']],
              ['q'=>'Cuénteme sobre el aprendizaje más transformador que haya tenido en su vida profesional. ¿Cómo ocurrió y qué cambió en usted?','probes'=>['S'=>'¿Qué aprendizaje fue y en qué momento de su carrera ocurrió?','T'=>'¿Fue a través de una experiencia, de alguien específico, o de estudio formal?','A'=>'¿Qué cambió concretamente en la forma en que trabajaba o tomaba decisiones?','R'=>'¿Cómo aplica hoy ese aprendizaje?']],
              ['q'=>'Descríbame una situación en que buscó retroalimentación activa de alguien cuya opinión era importante para su desarrollo, aunque pudiera no ser cómoda.','probes'=>['S'=>'¿A quién pidió retroalimentación y por qué a esa persona?','T'=>'¿Cómo hizo la solicitud y en qué contexto?','A'=>'¿Qué recibió? ¿Hubo algo que le sorprendió o incomodó?','R'=>'¿Qué acciones concretas tomó a partir de esa retroalimentación?']],
            ]],
        ];

        // ── Clusters ──────────────────────────────────────────────────────────
        $eecClusters = [
          ['name'=>'Liderazgo',  'codes'=>['L1','L2'],         'color'=>'violet'],
          ['name'=>'Relaciones', 'codes'=>['R1','R2','R3'],    'color'=>'sky'],
          ['name'=>'Desempeño',  'codes'=>['D1','D2','D3'],    'color'=>'emerald'],
          ['name'=>'Desarrollo', 'codes'=>['P1','P2'],         'color'=>'rose'],
        ];

        // ── Prioridades por perfil de cargo ───────────────────────────────────
        $eecProfiles = [
          ''               => ['label'=>'— Seleccionar tipo de cargo —','p'=>[]],
          'alta_direccion' => ['label'=>'Alta dirección / Gerencia','p'=>['L1'=>'C','L2'=>'C','R1'=>'A','R2'=>'A','R3'=>'A','D1'=>'C','D2'=>'C','D3'=>'C','P1'=>'C','P2'=>'A']],
          'jefatura'       => ['label'=>'Jefatura / Gestión de equipos','p'=>['L1'=>'C','L2'=>'A','R1'=>'C','R2'=>'C','R3'=>'A','D1'=>'C','D2'=>'A','D3'=>'C','P1'=>'C','P2'=>'A']],
          'ventas'         => ['label'=>'Ventas / Relaciones comerciales','p'=>['L1'=>'A','L2'=>'A','R1'=>'C','R2'=>'C','R3'=>'C','D1'=>'C','D2'=>'A','D3'=>'C','P1'=>'C','P2'=>'A']],
          'analitico'      => ['label'=>'Analítico / Técnico','p'=>['L1'=>'A','L2'=>'C','R1'=>'A','R2'=>'A','R3'=>'A','D1'=>'C','D2'=>'C','D3'=>'A','P1'=>'C','P2'=>'C']],
          'atencion'       => ['label'=>'Atención al cliente / Soporte','p'=>['L1'=>'A','L2'=>'A','R1'=>'C','R2'=>'C','R3'=>'C','D1'=>'A','D2'=>'A','D3'=>'C','P1'=>'C','P2'=>'A']],
          'rrhh'           => ['label'=>'Recursos Humanos','p'=>['L1'=>'C','L2'=>'A','R1'=>'C','R2'=>'C','R3'=>'C','D1'=>'A','D2'=>'A','D3'=>'C','P1'=>'C','P2'=>'C']],
          'operativo'      => ['label'=>'Operativo / Administrativo','p'=>['L1'=>'A','L2'=>'A','R1'=>'A','R2'=>'A','R3'=>'C','D1'=>'C','D2'=>'A','D3'=>'C','P1'=>'C','P2'=>'A']],
        ];

        // ── Puntajes de corte ─────────────────────────────────────────────────
        $eecCutoffs = [
          'alta_direccion' => ['avg'=>4.0,'min_c'=>4.0,'label'=>'Alta dirección'],
          'jefatura'       => ['avg'=>3.5,'min_c'=>3.5,'label'=>'Jefatura'],
          'ventas'         => ['avg'=>3.0,'min_c'=>3.0,'label'=>'Ventas'],
          'analitico'      => ['avg'=>3.0,'min_c'=>3.0,'label'=>'Analítico'],
          'atencion'       => ['avg'=>2.5,'min_c'=>2.5,'label'=>'Atención cliente'],
          'rrhh'           => ['avg'=>3.5,'min_c'=>3.5,'label'=>'RRHH'],
          'operativo'      => ['avg'=>2.5,'min_c'=>2.5,'label'=>'Operativo'],
        ];

        // ── Descriptores BARS globales ────────────────────────────────────────
        $starBars = [
          5=>['label'=>'Sobresaliente',          'cls'=>'bg-emerald-100 text-emerald-700','desc'=>'Todas las respuestas incluyen los 4 componentes STAR completos y verificables. Acciones demuestran la competencia de forma proactiva, con impacto medible en situaciones de alta complejidad. Sin generalizaciones.'],
          4=>['label'=>'Por encima del promedio', 'cls'=>'bg-brand-100 text-brand-700',  'desc'=>'La mayoría de respuestas tienen STAR completo. Evidencia sólida en situaciones complejas. Ocasionalmente faltan detalles de resultado o de acción personal.'],
          3=>['label'=>'En el nivel esperado',   'cls'=>'bg-amber-100 text-amber-700',   'desc'=>'STAR completo en al menos 2 de 3 preguntas. Competencia demostrada en complejidad media. Generalización ocasional pero con ejemplos concretos al sondear.'],
          2=>['label'=>'Por debajo del nivel',   'cls'=>'bg-orange-100 text-orange-700', 'desc'=>'Respuestas parciales: falta T, A específica o R medible. Tiende a generalizar ("yo siempre…") en vez de citar episodios concretos. Evidencia solo en situaciones simples.'],
          1=>['label'=>'No demostrado',           'cls'=>'bg-red-100 text-red-700',      'desc'=>'Sin ejemplos conductuales concretos pese a los sondeos. Respuestas hipotéticas ("si eso me pasara…"), circulares o que no responden a la competencia evaluada.'],
        ];

        $clusterPalette = [
          'violet' => ['badge'=>'bg-violet-100 text-violet-700','border'=>'border-violet-200','head'=>'text-violet-700'],
          'sky'    => ['badge'=>'bg-sky-100 text-sky-700',      'border'=>'border-sky-200',   'head'=>'text-sky-700'],
          'emerald'=> ['badge'=>'bg-emerald-100 text-emerald-700','border'=>'border-emerald-200','head'=>'text-emerald-700'],
          'rose'   => ['badge'=>'bg-rose-100 text-rose-700',    'border'=>'border-rose-200',  'head'=>'text-rose-700'],
        ];

        // Perfil guardado y prioridades para Alpine.js
        $savedProfile  = $scores['profile'] ?? '';
        $alpineProfiles = collect($eecProfiles)->map(fn($p) => $p['p'])->toArray();
        @endphp

        {{-- Alpine wrapper ──────────────────────────────────────────────── --}}
        <div x-data="{
            profile: '{{ e($savedProfile) }}',
            profiles: {!! json_encode($alpineProfiles) !!},
            pri(code) {
                if (!this.profile || !this.profiles[this.profile]) return '';
                return this.profiles[this.profile][code] || '';
            },
            isC(code) { return this.pri(code) === 'C'; },
            isA(code) { return this.pri(code) === 'A'; }
        }">

        {{-- ── Banner confidencialidad ────────────────────────────────────── --}}
        <div class="card border-amber-200 bg-amber-50 mb-5">
            <div class="card-body py-3 flex items-start gap-3">
                <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <strong class="text-xs text-amber-800">EEC-SL — Protocolo confidencial · Uso exclusivo de entrevistadores certificados</strong>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-200 text-amber-900 uppercase tracking-wide flex-shrink-0">🔒 Confidencial</span>
                    </div>
                    <p class="text-xs text-amber-800 leading-relaxed">Registre únicamente conductas observadas con episodios concretos. <strong>Nunca califique durante la entrevista</strong> — asigne puntajes BARS solo al finalizar, con base en sus notas. Aplica Ley 1581/2012 (protección de datos) y Ley 1090/2006 (ética del psicólogo). No comparta capturas ni registros fuera del sistema.</p>
                </div>
            </div>
        </div>

        {{-- ── Guión del entrevistador (colapsible) ────────────────────────── --}}
        <details class="card mb-5 group">
            <summary class="card-body py-3 cursor-pointer flex items-center gap-2 select-none">
                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform group-open:rotate-90 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-xs font-semibold text-slate-600">Guión del entrevistador — 5 fases (expandir antes de iniciar)</span>
            </summary>
            <div class="px-5 pb-5 space-y-3 text-xs text-slate-600">
                <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="font-bold text-slate-700 mb-1">Fase 1 — Apertura <span class="font-normal text-slate-400">(5 min)</span></p>
                        <p>Bienvenida + propósito ("conocer experiencias profesionales pasadas") + metodología STAR + confidencialidad + permiso de notas.</p>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="font-bold text-slate-700 mb-1">Fase 2 — Contexto <span class="font-normal text-slate-400">(5–7 min)</span></p>
                        <p>"¿Podría resumirme su trayectoria más reciente (últimos 3–5 años)?" — cargo, responsabilidades, número de personas a cargo.</p>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="font-bold text-slate-700 mb-1">Fase 3 — Preguntas <span class="font-normal text-slate-400">(30–50 min)</span></p>
                        <p>Leer la pregunta exactamente. Si respuesta es hipotética ("haría"), sondear: <em>"¿Puede darme un ejemplo de una situación real?"</em> Si respuesta grupal ("nosotros"), sondear: <em>"¿Qué hizo usted específicamente?"</em></p>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="font-bold text-slate-700 mb-1">Fase 4 — Espacio candidato <span class="font-normal text-slate-400">(5 min)</span></p>
                        <p>"¿Tiene alguna pregunta sobre el cargo o la empresa?" — No responder sobre compensación ni timelines sin autorización de HR.</p>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="font-bold text-slate-700 mb-1">Fase 5 — Cierre <span class="font-normal text-slate-400">(2–3 min)</span></p>
                        <p>Agradecer. Indicar próximos pasos. <strong>No dar feedback ni señales de decisión al candidato.</strong> Mantener neutralidad absoluta.</p>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 pt-1 border-t border-slate-100">Sondeo STAR universal: <em>¿Cuándo exactamente? · ¿Cuál era su rol específico? · ¿Qué hizo usted, no el equipo? · ¿Cuál fue el resultado medible?</em></p>
            </div>
        </details>

        {{-- ── Selector de perfil de cargo ─────────────────────────────────── --}}
        <div class="card mb-5 border-brand-100">
            <div class="card-body py-4">
                <div class="flex items-center gap-3 flex-wrap">
                    <label class="text-xs font-semibold text-slate-700 flex-shrink-0">Perfil del cargo evaluado:</label>
                    <select x-model="profile" name="scores[profile]" class="select text-sm flex-1 min-w-[220px]">
                        @foreach($eecProfiles as $key => $prof)
                        <option value="{{ $key }}" {{ $savedProfile === $key ? 'selected' : '' }}>{{ $prof['label'] }}</option>
                        @endforeach
                    </select>
                    <span x-show="profile" x-transition class="text-[11px] text-brand-600 flex-shrink-0">
                        Las competencias <span class="font-bold text-red-600">Críticas</span> requieren puntaje mínimo según nivel de cargo para continuar en el proceso.
                    </span>
                </div>
            </div>
        </div>

        {{-- ── Advertencia anti-discriminación ─────────────────────────────── --}}
        <div class="card mb-5 border-red-100 bg-red-50/40">
            <div class="card-body py-3 flex items-start gap-3">
                <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/></svg>
                <p class="text-xs text-red-700 leading-relaxed"><strong>Preguntas prohibidas (Código Sustantivo del Trabajo, Art. 10 y 13):</strong> No indague sobre estado civil, planes de maternidad/paternidad, afiliaciones religiosas o políticas, orientación sexual, condición de salud, discapacidad ni origen étnico. Todas las preguntas deben tener relación directa y documentada con la competencia evaluada.</p>
            </div>
        </div>

        {{-- ══ COMPETENCIAS POR CLÚSTER ═══════════════════════════════════════ --}}
        @foreach($eecClusters as $cluster)
        @php $pal = $clusterPalette[$cluster['color']]; @endphp

        <div class="mb-6">
            {{-- Cluster header --}}
            <div class="flex items-center gap-2 mb-3">
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $pal['badge'] }}">
                    {{ $cluster['name'] }}
                </span>
                <div class="flex-1 h-px bg-slate-100"></div>
            </div>

            <div class="space-y-4">
            @foreach($cluster['codes'] as $code)
            @php $comp = $eecComps[$code]; @endphp

            <div class="card border-2 transition-all duration-200"
                 :class="{
                     'border-red-300 shadow-sm shadow-red-100': isC('{{ $code }}'),
                     'border-amber-200': isA('{{ $code }}') && !isC('{{ $code }}'),
                     'border-slate-100': !isC('{{ $code }}') && !isA('{{ $code }}')
                 }">
                <div class="card-body">

                    {{-- Header competencia --}}
                    <div class="flex items-start gap-3 mb-4">
                        <span class="font-mono text-xs font-bold px-2 py-1 rounded flex-shrink-0 {{ $pal['badge'] }}">{{ $code }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                <h3 class="font-semibold text-slate-800 text-sm">{{ $comp['label'] }}</h3>
                                <span x-show="isC('{{ $code }}')" x-transition
                                      class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-red-100 text-red-700 uppercase tracking-wide flex-shrink-0">Crítica</span>
                                <span x-show="isA('{{ $code }}') && !isC('{{ $code }}')" x-transition
                                      class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 uppercase tracking-wide flex-shrink-0">Alta</span>
                            </div>
                            <p class="text-xs text-slate-400 leading-relaxed">{{ $comp['definition'] }}</p>
                        </div>
                    </div>

                    {{-- Indicadores conductuales --}}
                    <details class="group mb-5">
                        <summary class="cursor-pointer select-none text-[11px] font-semibold text-slate-400 hover:text-slate-600 flex items-center gap-1 py-1">
                            <svg class="w-3 h-3 transition-transform group-open:rotate-90 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            Indicadores conductuales a observar
                        </summary>
                        <ul class="mt-2 space-y-1 pl-4">
                            @foreach($comp['indicators'] as $ind)
                            <li class="text-xs text-slate-500 flex items-start gap-1.5"><span class="text-slate-300 flex-shrink-0 mt-0.5">•</span>{{ $ind }}</li>
                            @endforeach
                        </ul>
                    </details>

                    {{-- ── Preguntas STAR ─────────────────────────────────── --}}
                    <div class="space-y-5">
                        @foreach($comp['questions'] as $qi => $question)
                        @php $qNum = $qi + 1; $qKey = $code.'_q'.$qNum; @endphp
                        <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-4">

                            {{-- Número de pregunta --}}
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-5 h-5 rounded-full {{ $pal['badge'] }} text-[11px] font-bold flex items-center justify-center flex-shrink-0">{{ $qNum }}</span>
                                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Pregunta {{ $qNum }} de 3</span>
                            </div>

                            {{-- Texto de la pregunta --}}
                            <p class="text-sm text-slate-800 font-medium leading-relaxed mb-4">"{{ $question['q'] }}"</p>

                            {{-- Sondeos STAR --}}
                            <details class="group mb-3">
                                <summary class="cursor-pointer select-none text-[11px] font-semibold {{ $pal['head'] }} hover:opacity-80 flex items-center gap-1 py-0.5">
                                    <svg class="w-3 h-3 transition-transform group-open:rotate-90 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    Sondeos STAR obligatorios
                                </summary>
                                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($question['probes'] as $letter => $probe)
                                    <div class="flex items-start gap-2 p-2 bg-white rounded-lg border border-slate-100">
                                        <span class="flex-shrink-0 w-5 h-5 rounded font-bold text-[11px] flex items-center justify-center {{ $pal['badge'] }}">{{ $letter }}</span>
                                        <p class="text-xs text-slate-600 leading-snug">{{ $probe }}</p>
                                    </div>
                                    @endforeach
                                </div>
                            </details>

                            {{-- Notas / citas textuales --}}
                            <div class="form-group mb-0">
                                <label class="form-label text-[11px]">Notas · citas textuales del candidato</label>
                                <textarea name="scores[{{ $qKey }}]" rows="3"
                                          class="textarea text-xs font-mono leading-relaxed"
                                          placeholder="S: [contexto] · T: [responsabilidad] · A: [acciones concretas del candidato] · R: [resultado medible]…">{{ $scores[$qKey] ?? '' }}</textarea>
                            </div>

                        </div>
                        @endforeach
                    </div>

                    {{-- ── Calificación BARS (post-entrevista) ────────────── --}}
                    <div class="mt-5 pt-5 border-t-2 border-dashed border-slate-200">
                        <div class="flex items-center gap-2 mb-4 flex-wrap">
                            <p class="form-label text-xs mb-0">Calificación BARS</p>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 border border-amber-200">
                                ⚠ Asignar solo DESPUÉS de terminar la entrevista
                            </span>
                        </div>

                        {{-- BARS 1–5 --}}
                        <div class="grid grid-cols-5 gap-2 mb-3">
                            @foreach([1,2,3,4,5] as $bv)
                            <label class="cursor-pointer relative">
                                <input type="radio" name="scores[{{ $code }}]" value="{{ $bv }}"
                                       {{ ($scores[$code] ?? null) == $bv ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="py-2 text-center rounded-lg border-2 transition-all select-none text-xs font-bold
                                            border-slate-200 bg-white text-slate-400
                                            peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-700">
                                    <span class="block text-sm font-bold">{{ $bv }}</span>
                                    <span class="block text-[9px] leading-tight mt-0.5">{{ ['','No dem.','Debajo','Nivel','Encima','Sobresal.'][$bv] }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>

                        {{-- Descriptores BARS (colapsible) --}}
                        <details class="group mb-3">
                            <summary class="cursor-pointer select-none text-[11px] text-slate-400 hover:text-slate-600 flex items-center gap-1 py-0.5">
                                <svg class="w-3 h-3 transition-transform group-open:rotate-90 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                Ver descriptores BARS (1–5)
                            </summary>
                            <div class="mt-2 space-y-1.5">
                                @foreach(array_reverse($starBars, true) as $bLevel => $bar)
                                <div class="flex gap-2 items-start">
                                    <span class="flex-shrink-0 text-[10px] font-bold px-1.5 py-0.5 rounded {{ $bar['cls'] }}">{{ $bLevel }}</span>
                                    <p class="text-[11px] text-slate-600 leading-snug">{{ $bar['desc'] }}</p>
                                </div>
                                @endforeach
                            </div>
                        </details>

                        {{-- Evidencia suficiente + Justificación --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="form-group mb-0">
                                <label class="form-label text-[11px]">¿Evidencia suficiente?</label>
                                <div class="flex gap-3 mt-1">
                                    <label class="flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer">
                                        <input type="radio" name="scores[{{ $code }}_suf]" value="si"
                                               {{ ($scores[$code.'_suf'] ?? '') === 'si' ? 'checked' : '' }}>
                                        <span class="text-emerald-600 font-medium">Sí</span>
                                    </label>
                                    <label class="flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer">
                                        <input type="radio" name="scores[{{ $code }}_suf]" value="no"
                                               {{ ($scores[$code.'_suf'] ?? '') === 'no' ? 'checked' : '' }}>
                                        <span class="text-red-600 font-medium">No</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group mb-0 sm:col-span-2">
                                <label class="form-label text-[11px]">Justificación del puntaje</label>
                                <input type="text" name="scores[{{ $code }}_just]"
                                       value="{{ $scores[$code.'_just'] ?? '' }}"
                                       class="input text-xs"
                                       placeholder="Breve justificación basada en las notas…">
                            </div>
                        </div>

                    </div>{{-- /BARS --}}
                </div>
            </div>
            @endforeach
            </div>
        </div>
        @endforeach

        {{-- ══ SÍNTESIS E INTEGRACIÓN ══════════════════════════════════════ --}}
        <div class="card mb-5 border-brand-200">
            <div class="card-body">
                <h2 class="text-sm font-semibold text-brand-700 mb-4">Síntesis e Integración — Hoja de Decisión Final</h2>

                {{-- Tabla resumen de puntajes --}}
                <div class="overflow-x-auto mb-5">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="text-left py-2 pr-3 text-slate-500 font-semibold">Competencia</th>
                                <th class="text-center py-2 px-2 text-slate-500 font-semibold">Puntaje</th>
                                <th class="text-center py-2 px-2 text-slate-500 font-semibold">Evidencia</th>
                                <th class="text-left py-2 pl-2 text-slate-500 font-semibold">Prioridad (cargo)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @php
                            $allCodes = ['L1','L2','R1','R2','R3','D1','D2','D3','P1','P2'];
                            $ratingsSum = 0; $ratingsCount = 0;
                            @endphp
                            @foreach($eecClusters as $cl)
                            @foreach($cl['codes'] as $code)
                            @php
                            $sc  = $scores[$code] ?? null;
                            $suf = $scores[$code.'_suf'] ?? '';
                            if (is_numeric($sc)) { $ratingsSum += $sc; $ratingsCount++; }
                            $savedPri = ($savedProfile && isset($eecProfiles[$savedProfile]['p'][$code])) ? $eecProfiles[$savedProfile]['p'][$code] : '';
                            $scoreCls = match(true) {
                                !is_numeric($sc) => 'text-slate-300',
                                (float)$sc >= 4   => 'text-emerald-600 font-bold',
                                (float)$sc === 3.0 => 'text-amber-600 font-semibold',
                                default            => 'text-red-600 font-bold',
                            };
                            @endphp
                            <tr>
                                <td class="py-2 pr-3 font-medium text-slate-700">
                                    <span class="font-mono text-[10px] {{ $clusterPalette[$cl['color']]['badge'] }} px-1 rounded mr-1">{{ $code }}</span>
                                    {{ $eecComps[$code]['label'] }}
                                </td>
                                <td class="text-center px-2 {{ $scoreCls }}">{{ $sc ?? '—' }}</td>
                                <td class="text-center px-2">
                                    @if($suf === 'si') <span class="text-emerald-600">✓</span>
                                    @elseif($suf === 'no') <span class="text-red-500">✗</span>
                                    @else <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="pl-2">
                                    @if($savedPri === 'C') <span class="text-[10px] font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded">Crítica</span>
                                    @elseif($savedPri === 'A') <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded">Alta</span>
                                    @elseif($savedProfile) <span class="text-[10px] text-slate-400">Complementaria</span>
                                    @else <span class="text-slate-300 text-[10px]">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-brand-100">
                                <td class="py-2 pr-3 font-bold text-brand-700">Promedio ponderado</td>
                                <td class="text-center px-2 font-bold text-brand-700">
                                    {{ $ratingsCount > 0 ? number_format($ratingsSum / $ratingsCount, 2) : '—' }}
                                </td>
                                <td colspan="2">
                                    @if($savedProfile && $ratingsCount > 0 && isset($eecCutoffs[$savedProfile]))
                                    @php $cut = $eecCutoffs[$savedProfile]; $avg = $ratingsSum / $ratingsCount; @endphp
                                    <span class="text-[11px] {{ $avg >= $cut['avg'] ? 'text-emerald-600' : 'text-red-600' }} font-medium">
                                        {{ $avg >= $cut['avg'] ? '✓ Sobre el umbral' : '✗ Bajo el umbral' }}
                                        (mínimo: {{ $cut['avg'] }} para {{ $cut['label'] }})
                                    </span>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Errores comunes al calificar (recordatorio) --}}
                <details class="group mb-5">
                    <summary class="cursor-pointer select-none text-[11px] font-semibold text-slate-400 hover:text-slate-600 flex items-center gap-1 py-1">
                        <svg class="w-3 h-3 transition-transform group-open:rotate-90 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        Errores comunes al calificar — verificar antes de guardar
                    </summary>
                    <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach([
                            ['Efecto halo','Una impresión positiva/negativa contamina todas las demás competencias.'],
                            ['Efecto similitud','Puntuar más alto a candidatos similares al entrevistador en estilo o historia.'],
                            ['Efecto recencia','Recordar mejor lo último dicho y usarlo como base de toda la evaluación.'],
                            ['Resp. hipotéticas','Aceptar "haría" en lugar de insistir en "hice". Invalida el instrumento conductual.'],
                            ['Falta de sondeo','Dar por válida una respuesta general sin verificar los 4 componentes STAR.'],
                            ['Severidad/leniencia','Calificar a todos sistemáticamente alto o bajo sin base en conductas.'],
                        ] as [$err, $desc])
                        <div class="p-2 bg-slate-50 rounded-lg border border-slate-100">
                            <p class="text-[11px] font-bold text-slate-700 mb-0.5">{{ $err }}</p>
                            <p class="text-[11px] text-slate-500">{{ $desc }}</p>
                        </div>
                        @endforeach
                    </div>
                </details>

                {{-- Fortalezas y brechas --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="form-group mb-0">
                        <label class="form-label">Fortalezas evidenciadas</label>
                        <textarea name="scores[sintesis_fortalezas]" rows="3" class="textarea text-xs"
                            placeholder="Citar competencias y episodios concretos que las sustentan…">{{ $scores['sintesis_fortalezas'] ?? '' }}</textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Brechas identificadas</label>
                        <textarea name="scores[sintesis_brechas]" rows="3" class="textarea text-xs"
                            placeholder="Citar competencias con bajo puntaje y conductas observadas…">{{ $scores['sintesis_brechas'] ?? '' }}</textarea>
                    </div>
                </div>

                {{-- Recomendación final --}}
                <div class="form-group mb-0">
                    <label class="form-label">Recomendación para el cargo</label>
                    <div class="flex gap-4 flex-wrap">
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

        </div>{{-- /x-data --}}

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
