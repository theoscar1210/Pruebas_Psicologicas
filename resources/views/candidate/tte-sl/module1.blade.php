@extends('layouts.candidate')

@section('title', 'TTE-SL — Módulo 1')
@section('show-nav', true)

@section('nav-info')
    <span class="text-sm text-slate-700 font-medium truncate max-w-32 sm:max-w-none">{{ $candidate->name }}</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 sm:py-8">

    {{-- Progreso --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-xs font-semibold text-brand-700">Módulo 1 de 3 — Juicio Situacional Grupal</span>
            <span class="text-xs text-slate-400">20 situaciones</span>
        </div>
        <div class="progress-track h-2">
            <div class="progress-bar bg-brand-600" style="width: 33%"></div>
        </div>
    </div>

    <div class="mb-5">
        <h1 class="text-lg font-bold text-slate-900">Módulo 1: Juicio Situacional Grupal</h1>
        <p class="text-sm text-slate-500 mt-1">Para cada situación, seleccione la opción que mejor describe cómo actuaría usted en ese contexto real de trabajo en equipo. No hay respuestas correctas o incorrectas en apariencia — lo que importa es su criterio genuino.</p>
    </div>

    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 mb-6 text-xs text-indigo-800">
        <strong>Instrucción:</strong> Debe responder las 20 situaciones para poder continuar. Una vez enviadas, no podrá modificar sus respuestas.
    </div>

    <form action="{{ route('candidate.tte-sl.module1.store', $assignment) }}" method="POST" id="form-m1">
        @csrf

        @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            Por favor, responda todas las situaciones antes de continuar.
        </div>
        @endif

        @php
        $items = [
            1 => [
                'text' => 'El equipo está discutiendo opciones para un proyecto urgente. Usted tiene una idea que podría ser muy valiosa, pero teme que suene arriesgada y que los demás la rechacen.',
                'dim'  => 'C1',
                'opts' => [
                    'A' => 'Espera a ver si alguien más llega a la misma idea y la presenta primero.',
                    'B' => 'Comparte la idea presentando primero los riesgos para protegerse del rechazo.',
                    'C' => 'Presenta la idea con claridad, explica el razonamiento y se abre a escuchar las reacciones del grupo.',
                    'D' => 'La comenta solo con un colega de confianza después de la reunión.',
                ],
            ],
            2 => [
                'text' => 'En una reunión de equipo, usted nota que lleva 15 minutos sin hablar porque el debate lo dominan dos colegas más extrovertidos. Usted tiene información relevante que ninguno ha mencionado.',
                'dim'  => 'C1',
                'opts' => [
                    'A' => 'Espera a que los demás terminen y si no lo preguntan, asume que no es necesario.',
                    'B' => 'Interrumpe en el primer momento de pausa e introduce la información que falta.',
                    'C' => 'Envía un correo después de la reunión con la información que no pudo compartir.',
                    'D' => 'Le señala a alguien en privado la información pero no la menciona en grupo.',
                ],
            ],
            3 => [
                'text' => 'Su equipo tiene una carga de trabajo desigual: usted terminó sus tareas y un colega lleva dos días atrasado con una entrega crítica.',
                'dim'  => 'C1',
                'opts' => [
                    'A' => 'Continúa con sus propias tareas pendientes de menor urgencia, sin intervenir.',
                    'B' => 'Le ofrece proactivamente ayuda a su colega sin esperar que él lo pida.',
                    'C' => 'Le comenta a su supervisor que el colega está atrasado para que intervenga.',
                    'D' => 'Le pregunta a su colega si necesita algo, pero solo si le surge el momento.',
                ],
            ],
            4 => [
                'text' => 'Su equipo está decidiendo el enfoque para un proyecto. Usted ya tiene clara su posición, pero un colega está presentando una alternativa que inicialmente no le pareció buena. Lleva 3 minutos explicando.',
                'dim'  => 'C2',
                'opts' => [
                    'A' => 'Lo interrumpe amablemente para señalar los problemas que ya identificó con esa opción.',
                    'B' => 'Escucha hasta que termine, identifica los méritos de su propuesta y los incorpora antes de expresar su posición.',
                    'C' => 'Escucha con paciencia pero ya tiene claro que va a votar en contra cuando le toque.',
                    'D' => 'Asiente durante la presentación para no desanimarlo y luego vota diferente.',
                ],
            ],
            5 => [
                'text' => 'Usted presentó una propuesta al equipo y un colega con menos experiencia que usted hace una crítica válida que no había considerado.',
                'dim'  => 'C2',
                'opts' => [
                    'A' => 'Defiende su propuesta original y responde a la crítica con más argumentos a su favor.',
                    'B' => 'Reconoce el punto del colega, agradece la observación y ajusta la propuesta.',
                    'C' => 'Dice que tendrá en cuenta el comentario pero internamente no lo modifica.',
                    'D' => 'Pide al equipo que vote sobre si la crítica es válida antes de responder.',
                ],
            ],
            6 => [
                'text' => 'Durante una lluvia de ideas, un colega propone algo que a usted le parece completamente fuera de lugar. Los demás parecen indiferentes a la idea.',
                'dim'  => 'C2',
                'opts' => [
                    'A' => 'Señala que la idea no aplica al contexto actual para no perder tiempo.',
                    'B' => 'Pregunta al colega qué lo llevó a esa idea para entender su razonamiento antes de descartarla.',
                    'C' => 'No dice nada y espera a que el facilitador pase a la siguiente idea.',
                    'D' => 'Señala que es una idea interesante pero que mejor la evalúan en otro momento.',
                ],
            ],
            7 => [
                'text' => 'Un colega nuevo en el equipo está luchando visiblemente con una herramienta que usted domina. No le ha pedido ayuda y el equipo tiene prioridades urgentes.',
                'dim'  => 'C3',
                'opts' => [
                    'A' => 'Espera a que el colega pida ayuda para no parecer condescendiente.',
                    'B' => 'Le ofrece 10 minutos para mostrarle lo básico de la herramienta entre una tarea y otra.',
                    'C' => 'Le señala al supervisor que el nuevo colega necesita capacitación.',
                    'D' => 'Le dice que hay tutoriales buenos en línea para esa herramienta.',
                ],
            ],
            8 => [
                'text' => 'Su equipo logró un resultado excelente. Usted contribuyó de forma clave, pero el reconocimiento público del supervisor recayó sobre otro colega que también trabajó duro.',
                'dim'  => 'C3',
                'opts' => [
                    'A' => 'Le menciona al supervisor en privado que usted también tuvo un rol importante.',
                    'B' => 'Celebra el reconocimiento de su colega genuinamente. El resultado del equipo es lo que importa.',
                    'C' => 'No dice nada pero internamente le molesta la falta de reconocimiento.',
                    'D' => 'Felicita al colega pero luego le comenta que esperaba más reconocimiento del supervisor.',
                ],
            ],
            9 => [
                'text' => 'Detecta que un colega tiene una dificultad personal (problemas de concentración, estrés visible) que está afectando su rendimiento esta semana.',
                'dim'  => 'C3',
                'opts' => [
                    'A' => 'Informa al supervisor para que gestione la situación.',
                    'B' => 'No interviene — las dificultades personales son asunto privado.',
                    'C' => 'Busca un momento apropiado para preguntarle cómo está y si puede hacer algo por él.',
                    'D' => 'Asume más carga de trabajo esta semana para compensar sin mencionárselo.',
                ],
            ],
            10 => [
                'text' => 'Usted y un colega tienen posiciones completamente opuestas sobre cómo ejecutar una tarea. Llevan dos reuniones sin resolver el desacuerdo y la fecha límite se acerca.',
                'dim'  => 'C4',
                'opts' => [
                    'A' => 'Cede ante la posición del colega para no seguir perdiendo tiempo.',
                    'B' => 'Propone un espacio de 30 minutos solo con el colega para explorar los desacuerdos de fondo y buscar un punto de integración.',
                    'C' => 'Pide al supervisor que tome la decisión final.',
                    'D' => 'Sigue ejecutando su posición en paralelo y ve cuál resultado es mejor.',
                ],
            ],
            11 => [
                'text' => 'Un colega le hace un comentario crítico sobre su trabajo frente a todo el equipo. El comentario tiene algo de validez, pero el tono fue innecesariamente duro.',
                'dim'  => 'C4',
                'opts' => [
                    'A' => 'Responde defensivamente en el momento para que el equipo vea que no acepta ese trato.',
                    'B' => 'En el momento, reconoce el punto válido. Después, en privado, le dice cómo le afectó el tono.',
                    'C' => 'Guarda silencio en la reunión pero después comenta la situación con otros colegas.',
                    'D' => 'Le dice al colega en la misma reunión que agradece el punto pero que el tono no fue apropiado.',
                ],
            ],
            12 => [
                'text' => 'Hay una tensión no resuelta entre dos miembros de su equipo que está afectando el clima grupal. Usted no es el líder, pero tiene buena relación con ambos.',
                'dim'  => 'C4',
                'opts' => [
                    'A' => 'No interviene: no es su rol mediar en conflictos ajenos.',
                    'B' => 'Habla por separado con cada uno, escucha su perspectiva y explora si estarían dispuestos a conversarlo directamente.',
                    'C' => 'Lo comenta con el líder del equipo para que tome acción.',
                    'D' => 'Organiza una actividad grupal informal para distender el ambiente.',
                ],
            ],
            13 => [
                'text' => 'El equipo no cumplió una entrega importante. Usted completó su parte a tiempo, pero otros colegas se retrasaron. En la reunión de revisión, el supervisor pregunta qué ocurrió.',
                'dim'  => 'C5',
                'opts' => [
                    'A' => 'Explica que su parte estaba lista e identifica quiénes se retrasaron.',
                    'B' => 'Dice que como equipo no lograron coordinar bien la integración y propone qué hacer diferente.',
                    'C' => 'Guarda silencio y espera a que los colegas responsables den la explicación.',
                    'D' => 'Presenta la evidencia de que su parte estuvo lista antes del plazo.',
                ],
            ],
            14 => [
                'text' => 'Un colega comprometió al equipo con una fecha de entrega sin consultar. La fecha es muy ajustada y usted sabe que va a generar sobrecarga.',
                'dim'  => 'C5',
                'opts' => [
                    'A' => 'Lo asume sin decir nada y trabaja horas extra para cumplir.',
                    'B' => 'Le dice al colega en privado que esa práctica afecta al equipo y deben acordar cómo manejar compromisos futuros.',
                    'C' => 'Lo reporta al supervisor para que corrija al colega.',
                    'D' => 'En la siguiente reunión, menciona frente al equipo que no todos estuvieron de acuerdo con el plazo.',
                ],
            ],
            15 => [
                'text' => 'Usted descubrió que cometió un error que afecta el trabajo de dos colegas y que retrasará una entrega del equipo.',
                'dim'  => 'C5',
                'opts' => [
                    'A' => 'Intenta corregirlo solo sin decir nada, esperando que nadie lo note.',
                    'B' => 'Informa de inmediato a los afectados, asume la responsabilidad, explica el impacto y propone un plan de corrección.',
                    'C' => 'Informa al supervisor antes que a los colegas para cubrirse.',
                    'D' => 'Corrige el error y luego lo menciona de pasada en la siguiente reunión.',
                ],
            ],
            16 => [
                'text' => 'Su equipo incluye a un colega que trabaja de forma muy diferente a usted: él prefiere planificar cada detalle antes de actuar y usted prefiere iterar rápido sobre la marcha.',
                'dim'  => 'C6',
                'opts' => [
                    'A' => 'Continúa con su forma de trabajar y asume que el colega se irá adaptando.',
                    'B' => 'Conversa con el colega para entender sus preferencias y busca un acuerdo de trabajo que respete ambos estilos.',
                    'C' => 'Le pide al líder que defina cuál forma de trabajar es la oficial del equipo.',
                    'D' => 'Acepta el ritmo del colega en los proyectos compartidos aunque le genere frustración.',
                ],
            ],
            17 => [
                'text' => 'En un proyecto de alta presión, el equipo decide tomar una dirección con la que usted no está completamente de acuerdo pero que fue elegida por mayoría.',
                'dim'  => 'C6',
                'opts' => [
                    'A' => 'Ejecuta su parte de mala gana y espera que los resultados demuestren que tenía razón.',
                    'B' => 'Una vez tomada la decisión, la apoya activamente y contribuye a que funcione lo mejor posible.',
                    'C' => 'Ejecuta su parte pero sigue expresando sus reservas en las reuniones para que quede registrado.',
                    'D' => 'Pide que se evalúe la decisión de nuevo antes de ejecutar.',
                ],
            ],
            18 => [
                'text' => 'Su equipo incluye personas de distintas generaciones con formas muy distintas de comunicarse: algunos prefieren chat, otros correo, otros reuniones presenciales.',
                'dim'  => 'C6',
                'opts' => [
                    'A' => 'Usa su canal preferido y asume que los demás se adaptarán.',
                    'B' => 'Establece un canal principal acordado con el equipo y ajusta su comunicación al contexto y al receptor.',
                    'C' => 'Usa todos los canales por si acaso y espera que el mensaje llegue por alguno.',
                    'D' => 'Le pide al líder que establezca un solo canal obligatorio para todos.',
                ],
            ],
            19 => [
                'text' => 'El equipo está a punto de presentar un trabajo en el que usted tiene una contribución menor de lo esperado porque las circunstancias del proyecto lo requirieron así.',
                'dim'  => 'C7',
                'opts' => [
                    'A' => 'Busca el momento adecuado para explicar su rol real al supervisor antes de la presentación.',
                    'B' => 'Apoya la presentación del equipo tal como está, confiando en que su contribución es conocida.',
                    'C' => 'Pide que en la presentación se especifique qué hizo cada miembro.',
                    'D' => 'Le dice al líder que la distribución de roles no fue justa y que debe corregirse antes de presentar.',
                ],
            ],
            20 => [
                'text' => 'El equipo está bajo presión de tiempo y necesita tomar una decisión rápida. Usted tiene la solución más elaborada, pero no hay tiempo para presentarla completamente. Un colega tiene una solución más simple que funcionará.',
                'dim'  => 'C7',
                'opts' => [
                    'A' => 'Insiste en que se tome el tiempo necesario para escuchar su propuesta completa.',
                    'B' => 'Apoya la solución del colega porque responde al objetivo del equipo en el momento.',
                    'C' => 'Presenta un resumen de su propuesta pero acepta que el equipo decida.',
                    'D' => 'Presenta su propuesta completa pero de forma más rápida, comprimiendo los detalles.',
                ],
            ],
        ];

        $dimColors = [
            'C1' => ['bg-indigo-50','border-indigo-200','text-indigo-700'],
            'C2' => ['bg-sky-50','border-sky-200','text-sky-700'],
            'C3' => ['bg-teal-50','border-teal-200','text-teal-700'],
            'C4' => ['bg-rose-50','border-rose-200','text-rose-700'],
            'C5' => ['bg-orange-50','border-orange-200','text-orange-700'],
            'C6' => ['bg-violet-50','border-violet-200','text-violet-700'],
            'C7' => ['bg-amber-50','border-amber-200','text-amber-700'],
        ];
        @endphp

        <div class="space-y-6">
            @foreach($items as $num => $item)
            @php $dc = $dimColors[$item['dim']]; @endphp
            <div class="card border-slate-100" id="item-{{ $num }}">
                <div class="px-5 py-3 {{ $dc[0] }} border-b {{ $dc[1] }} flex items-center justify-between">
                    <span class="text-xs font-bold {{ $dc[2] }}">Situación {{ $num }}</span>
                    <span class="text-[10px] font-semibold {{ $dc[2] }} border {{ $dc[1] }} rounded-full px-2 py-0.5">{{ $item['dim'] }}</span>
                </div>
                <div class="p-5">
                    <p class="text-sm text-slate-700 leading-relaxed mb-4">{{ $item['text'] }}</p>
                    <div class="space-y-2">
                        @foreach($item['opts'] as $letter => $text)
                        <label class="flex gap-3 p-3 rounded-lg border border-slate-100 hover:border-brand-300 hover:bg-brand-50/40 cursor-pointer transition-all has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50">
                            <input type="radio"
                                   name="m1[{{ $num }}]"
                                   value="{{ $letter }}"
                                   class="mt-0.5 flex-shrink-0 text-brand-600 focus:ring-brand-500"
                                   required>
                            <div class="flex gap-2.5 flex-1 min-w-0">
                                <span class="text-xs font-bold text-slate-400 flex-shrink-0 mt-0.5">{{ $letter }}.</span>
                                <span class="text-sm text-slate-700 leading-relaxed">{{ $text }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Barra inferior --}}
        <div class="sticky bottom-4 mt-6">
            <div class="bg-white border border-slate-200 rounded-xl shadow-lg p-4 flex items-center justify-between gap-4">
                <div class="text-sm text-slate-600">
                    <span id="answered-count" class="font-bold text-brand-700">0</span>
                    <span class="text-slate-400"> / 20 respondidas</span>
                </div>
                <button type="submit" id="btn-submit"
                        class="btn-primary btn-sm opacity-40 cursor-not-allowed"
                        disabled>
                    Continuar al Módulo 2
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>

    </form>
</div>

<script>
(function() {
    const total = 20;
    const counter = document.getElementById('answered-count');
    const btn = document.getElementById('btn-submit');

    function updateCount() {
        let answered = 0;
        for (let i = 1; i <= total; i++) {
            if (document.querySelector(`input[name="m1[${i}]"]:checked`)) answered++;
        }
        counter.textContent = answered;
        const done = answered === total;
        btn.disabled = !done;
        btn.classList.toggle('opacity-40', !done);
        btn.classList.toggle('cursor-not-allowed', !done);
    }

    document.getElementById('form-m1').addEventListener('change', updateCount);
    updateCount();
})();
</script>
@endsection
