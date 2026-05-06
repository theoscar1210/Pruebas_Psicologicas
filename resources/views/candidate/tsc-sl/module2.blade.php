@extends('layouts.candidate')

@section('title', 'TSC-SL — Módulo 2')
@section('show-nav', true)

@section('nav-info')
    <span class="text-sm text-slate-700 font-medium truncate max-w-32 sm:max-w-none">{{ $candidate->name }}</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 sm:py-8">

    {{-- Progreso --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-xs font-semibold text-brand-700">Módulo 2 de 3 — Actitudes hacia el servicio</span>
            <span class="text-xs text-slate-400">40 ítems</span>
        </div>
        <div class="progress-track h-2">
            <div class="progress-bar bg-brand-600" style="width: 66%"></div>
        </div>
    </div>

    <div class="mb-5">
        <h1 class="text-lg font-bold text-slate-900">Módulo 2: Escala de Actitudes</h1>
        <p class="text-sm text-slate-500 mt-1">Indique qué tan de acuerdo está con cada afirmación sobre su forma de trabajar. Responda con honestidad sobre cómo piensa y actúa realmente.</p>
    </div>

    {{-- Leyenda --}}
    <div class="card border-brand-100 bg-brand-50/30 mb-6">
        <div class="card-body py-3">
            <div class="grid grid-cols-5 gap-1 text-center text-[10px] font-semibold">
                <div class="text-red-600">1<br><span class="font-normal text-slate-500">Totalmente<br>en desacuerdo</span></div>
                <div class="text-orange-500">2<br><span class="font-normal text-slate-500">En<br>desacuerdo</span></div>
                <div class="text-slate-500">3<br><span class="font-normal text-slate-500">Ni / ni</span></div>
                <div class="text-brand-600">4<br><span class="font-normal text-slate-500">De<br>acuerdo</span></div>
                <div class="text-emerald-600">5<br><span class="font-normal text-slate-500">Totalmente<br>de acuerdo</span></div>
            </div>
        </div>
    </div>

    <form action="{{ route('candidate.tsc-sl.module2.store', $assignment) }}" method="POST" id="form-m2">
        @csrf

        @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            Por favor, responda todos los ítems antes de continuar.
        </div>
        @endif

        @php
        $sections = [
            ['title' => 'E1 — Empatía y Escucha Activa', 'color' => 'teal', 'items' => [
                21 => 'Cuando un socio o invitado está molesto por un problema en el club, me aseguro de escucharlo completamente antes de buscar una solución.',
                22 => 'Me resulta fácil ponerme en el lugar del huésped y comprender cómo se siente con la situación que vive en el club.',
                23 => 'Cuando un socio me da información confusa sobre su solicitud, prefiero actuar con lo que entendí antes que hacer demasiadas preguntas.',
                24 => 'Considero que validar las emociones del socio o invitado es tan importante como resolver el problema puntual.',
                25 => 'Me incomoda cuando los socios o invitados se toman mucho tiempo para explicar algo que ya entendí.',
            ]],
            ['title' => 'E2 — Comunicación Efectiva con el Huésped', 'color' => 'sky', 'items' => [
                26 => 'Adapto mi lenguaje y tono según el perfil del huésped: no me expreso igual con un socio fundador que con un invitado que llega por primera vez.',
                27 => 'Antes de finalizar una interacción con un socio o invitado, me aseguro de que haya comprendido bien la información que le di.',
                28 => 'Cuando debo dar una mala noticia (instalación no disponible, reserva cancelada, cobro adicional), tiendo a suavizarlo tanto que el mensaje pierde claridad.',
                29 => 'Soy capaz de comunicar una limitación o política del club de forma respetuosa sin que el socio se sienta rechazado.',
                30 => 'Prefiero dar respuestas rápidas aunque no sean completamente exactas, antes que hacer esperar al socio o invitado.',
            ]],
            ['title' => 'P1 — Resolución de Problemas del Huésped', 'color' => 'violet', 'items' => [
                31 => 'Cuando un socio reporta un problema recurrente en alguna instalación del club, busco la causa raíz en lugar de aplicar siempre la misma solución temporal.',
                32 => 'Me siento cómodo coordinando con otras áreas del club (mantenimiento, ama de llaves, cocina, deportes) para resolver un problema del socio.',
                33 => 'Si no tengo la solución inmediata para un socio, prefiero dar una respuesta vaga antes que admitir que debo consultar con otra área.',
                34 => 'Hago seguimiento a las solicitudes pendientes de socios e invitados sin que tengan que recordármelo.',
                35 => 'Me resulta difícil proponer alternativas cuando el servicio o instalación que el socio quiere no está disponible.',
            ]],
            ['title' => 'P2 — Manejo de Socios e Invitados Difíciles', 'color' => 'rose', 'items' => [
                36 => 'Cuando un socio o invitado se comporta de forma agresiva o irrespetuosa, logro mantener la calma y el enfoque en encontrar una solución.',
                37 => 'Soy capaz de establecer un límite respetuoso con un socio que viola las normas del club sin deteriorar la relación ni escalar el conflicto.',
                38 => 'Me afecta emocionalmente durante el resto del turno cuando tuve una interacción muy tensa con un socio o invitado.',
                39 => 'Entiendo que la frustración de un socio frente a un problema del club generalmente no es algo personal hacia mí.',
                40 => 'Cuando un socio insiste en algo que va en contra de las políticas del club, me resulta difícil mantenerme firme sin sentirme culpable.',
            ]],
            ['title' => 'A1 — Actitud de Servicio y Proactividad', 'color' => 'amber', 'items' => [
                41 => 'Me siento genuinamente satisfecho cuando logro resolver bien el requerimiento de un socio o invitado, más allá de si era estrictamente parte de mis funciones.',
                42 => 'Cuando noto que un socio o invitado podría necesitar algo más de lo que pidió, se lo menciono aunque no sea exactamente mi área de trabajo.',
                43 => 'Considero que mi trabajo termina cuando entrego el servicio solicitado por el socio o invitado, no antes ni después.',
                44 => 'El bienestar y la satisfacción del socio o invitado son un motivador real para mí en el día a día, no solo una exigencia del puesto.',
                45 => 'Me resulta fácil mantener una actitud cálida y positiva con socios e invitados incluso cuando el turno ha sido muy exigente.',
            ]],
            ['title' => 'A2 — Tolerancia a la Presión y Regulación Emocional', 'color' => 'orange', 'items' => [
                46 => 'Cuando cometo un error en la atención de un socio o invitado, lo asumo, lo corrijo y sigo adelante sin que afecte la calidad de las atenciones siguientes.',
                47 => 'En temporadas de alta demanda (vacaciones, fines de semana largos, eventos especiales), mantengo el mismo nivel de servicio que en días tranquilos.',
                48 => 'El cansancio acumulado durante un turno intenso en el club se nota en cómo trato a los últimos socios o invitados del día.',
                49 => 'Tengo estrategias personales claras para recuperarme emocionalmente entre una interacción difícil con un huésped y la siguiente.',
                50 => 'Cuando ocurren situaciones fuera de mi control en el club (cortes de luz, caída de sistemas, falta de insumos), me cuesta mantener una actitud positiva con los socios.',
            ]],
            ['title' => 'Actitudes Generales de Servicio en el Club', 'color' => 'slate', 'items' => [
                51 => 'Prefiero resolver las necesidades de un socio directamente antes que derivarlo a otra área, cuando tengo la posibilidad de ayudar.',
                52 => 'Creo que la calidez en la atención a socios e invitados depende mucho del humor con el que uno llegue al trabajo.',
                53 => 'Me molesta cuando los socios hacen solicitudes que consideran urgentes pero que para mí son poco razonables o van contra el reglamento del club.',
                54 => 'Creo que brindar una experiencia excepcional al socio e invitado es responsabilidad de todos los empleados del club, sin importar el área.',
                55 => 'Cuando estoy ejecutando una tarea de mi área, prefiero no ser interrumpido aunque un socio o invitado necesite orientación.',
                56 => 'Considero que la primera impresión que tiene un socio o invitado del club depende directamente de cómo es atendido desde el primer contacto.',
                57 => 'Si un socio no queda satisfecho a pesar de mis mejores esfuerzos dentro de las posibilidades del club, lo asumo como un resultado fuera de mi control y sigo adelante con la misma disposición.',
                58 => 'Creo que hay socios que simplemente son imposibles de complacer y lo mejor es limitarse a cumplir el protocolo mínimo establecido.',
                59 => 'Me resulta natural agradecer a un socio su paciencia cuando tuvo que esperar por un servicio, aunque la demora no haya sido responsabilidad mía.',
                60 => 'Prefiero decirle a un socio lo que necesita escuchar, aunque no sea lo que quiere oír, si con eso realmente le ayudo o lo oriento mejor.',
            ]],
        ];

        $colorMap = [
            'teal'   => ['bg-teal-50',   'border-teal-200',   'text-teal-700'],
            'sky'    => ['bg-sky-50',    'border-sky-200',    'text-sky-700'],
            'violet' => ['bg-violet-50', 'border-violet-200', 'text-violet-700'],
            'rose'   => ['bg-rose-50',   'border-rose-200',   'text-rose-700'],
            'amber'  => ['bg-amber-50',  'border-amber-200',  'text-amber-700'],
            'orange' => ['bg-orange-50', 'border-orange-200', 'text-orange-700'],
            'slate'  => ['bg-slate-50',  'border-slate-200',  'text-slate-600'],
        ];
        @endphp

        <div class="space-y-6">
            @foreach($sections as $section)
            @php $c = $colorMap[$section['color']]; @endphp
            <div class="card border-slate-100 overflow-hidden">
                <div class="px-5 py-3 {{ $c[0] }} border-b {{ $c[1] }}">
                    <h3 class="text-xs font-bold {{ $c[2] }} uppercase tracking-wider">{{ $section['title'] }}</h3>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach($section['items'] as $num => $text)
                    <div class="px-4 py-3.5 likert-row" data-item="{{ $num }}">
                        <p class="text-sm text-slate-700 mb-3 leading-relaxed">
                            <span class="font-semibold text-slate-400 mr-1">{{ $num }}.</span>{{ $text }}
                        </p>
                        <div class="flex justify-between gap-1">
                            @foreach([1,2,3,4,5] as $val)
                            @php
                            $labelClasses = match($val) {
                                1 => 'has-[:checked]:bg-red-100 has-[:checked]:border-red-400 has-[:checked]:text-red-700',
                                2 => 'has-[:checked]:bg-orange-100 has-[:checked]:border-orange-400 has-[:checked]:text-orange-700',
                                3 => 'has-[:checked]:bg-slate-100 has-[:checked]:border-slate-400 has-[:checked]:text-slate-700',
                                4 => 'has-[:checked]:bg-brand-100 has-[:checked]:border-brand-400 has-[:checked]:text-brand-700',
                                5 => 'has-[:checked]:bg-emerald-100 has-[:checked]:border-emerald-400 has-[:checked]:text-emerald-700',
                            };
                            @endphp
                            <label class="flex-1 flex flex-col items-center gap-1 cursor-pointer py-2 px-1 rounded-lg border border-slate-100 hover:border-brand-200 hover:bg-brand-50/30 transition-all {{ $labelClasses }}">
                                <input type="radio"
                                       name="m2[{{ $num }}]"
                                       value="{{ $val }}"
                                       class="sr-only"
                                       required>
                                <span class="text-sm font-bold">{{ $val }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Barra inferior --}}
        <div class="sticky bottom-4 mt-6">
            <div class="bg-white border border-slate-200 rounded-xl shadow-lg p-4 flex items-center justify-between gap-4">
                <div class="text-sm text-slate-600">
                    <span id="answered-count2" class="font-bold text-brand-700">0</span>
                    <span class="text-slate-400"> / 40 respondidas</span>
                </div>
                <button type="submit" id="btn-submit2"
                        class="btn-primary btn-sm opacity-40 cursor-not-allowed"
                        disabled>
                    Continuar al Módulo 3
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>

    </form>
</div>

<script nonce="{{ app('csp-nonce') }}">
(function() {
    const total = 40;
    const counter = document.getElementById('answered-count2');
    const btn = document.getElementById('btn-submit2');
    const itemNums = [
        21,22,23,24,25,26,27,28,29,30,
        31,32,33,34,35,36,37,38,39,40,
        41,42,43,44,45,46,47,48,49,50,
        51,52,53,54,55,56,57,58,59,60
    ];

    function updateCount() {
        const answered = itemNums.filter(n =>
            document.querySelector(`input[name="m2[${n}]"]:checked`)
        ).length;
        counter.textContent = answered;
        const done = answered === total;
        btn.disabled = !done;
        btn.classList.toggle('opacity-40', !done);
        btn.classList.toggle('cursor-not-allowed', !done);
    }

    document.getElementById('form-m2').addEventListener('change', updateCount);
    updateCount();
})();
</script>
@endsection
