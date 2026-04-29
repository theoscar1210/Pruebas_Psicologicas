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
        <p class="text-sm text-slate-500 mt-1">Indique qué tan de acuerdo está con cada afirmación. Responda con honestidad sobre cómo piensa y actúa realmente.</p>
    </div>

    {{-- Leyenda de escala --}}
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
                21 => 'Cuando un cliente está molesto, me aseguro de escucharlo completamente antes de comenzar a buscar una solución.',
                22 => 'Me resulta fácil ponerme en el lugar del cliente y entender cómo se siente con la situación que está viviendo.',
                23 => 'Cuando un cliente me da información confusa, prefiero actuar con lo que entendí antes que hacer demasiadas preguntas.',
                24 => 'Considero que validar las emociones del cliente es tan importante como resolver su problema.',
                25 => 'Me incomoda cuando los clientes se toman mucho tiempo para explicar algo que ya entendí.',
            ]],
            ['title' => 'E2 — Comunicación Efectiva con el Cliente', 'color' => 'sky', 'items' => [
                26 => 'Adapto mi forma de hablar según el perfil del cliente: no uso el mismo lenguaje con un experto que con alguien sin conocimientos técnicos.',
                27 => 'Antes de cerrar una interacción, me aseguro de que el cliente haya entendido la información que le di.',
                28 => 'Cuando tengo que dar malas noticias, tiendo a posponerlo o suavizarlo tanto que el mensaje pierde claridad.',
                29 => 'Soy capaz de comunicar limitaciones o negativas de forma respetuosa sin que el cliente se sienta rechazado.',
                30 => 'Prefiero dar respuestas rápidas aunque no sean completamente precisas, antes que hacer esperar al cliente.',
            ]],
            ['title' => 'P1 — Resolución de Problemas del Cliente', 'color' => 'violet', 'items' => [
                31 => 'Cuando un cliente tiene un problema recurrente, busco la causa raíz en lugar de aplicar siempre el mismo parche.',
                32 => 'Me siento cómodo tomando la iniciativa de resolver un problema aunque implique coordinar con otras áreas.',
                33 => 'Si no tengo la solución inmediata, prefiero dar una respuesta vaga a admitir que debo investigar más.',
                34 => 'Hago seguimiento a los casos que quedaron pendientes sin que el cliente tenga que recordármelo.',
                35 => 'Me resulta difícil proponer alternativas cuando la solución que el cliente quiere no está disponible.',
            ]],
            ['title' => 'P2 — Manejo de Clientes Difíciles y Quejas', 'color' => 'rose', 'items' => [
                36 => 'Cuando un cliente se comporta de forma agresiva, logro mantener la calma y el enfoque en encontrar una solución.',
                37 => 'Soy capaz de marcar un límite respetuoso con un cliente sin deteriorar la relación ni escalar el conflicto.',
                38 => 'Me afecta emocionalmente durante el resto del turno cuando una interacción con un cliente fue muy intensa.',
                39 => 'Entiendo que la frustración del cliente generalmente no es personal hacia mí, aunque lo parezca.',
                40 => 'Cuando un cliente insiste en algo que no puedo hacer, me resulta difícil mantenerme firme sin sentirme culpable.',
            ]],
            ['title' => 'A1 — Actitud de Servicio y Proactividad', 'color' => 'amber', 'items' => [
                41 => 'Me siento genuinamente satisfecho cuando logro resolver bien el problema de un cliente, más allá de si es parte de mis responsabilidades formales.',
                42 => 'Cuando noto que un cliente podría necesitar algo más que lo que pidió, se lo menciono aunque no sea mi función.',
                43 => 'Considero que mi trabajo termina cuando resuelvo lo que el cliente solicitó, no antes ni después.',
                44 => 'El bienestar del cliente es un motivador real para mí, no solo una exigencia del cargo.',
                45 => 'Me resulta fácil mantener una actitud positiva con los clientes incluso cuando el día ha sido muy exigente.',
            ]],
            ['title' => 'A2 — Tolerancia a la Presión y Regulación Emocional', 'color' => 'orange', 'items' => [
                46 => 'Cuando cometo un error con un cliente, lo asumo, lo corrijo y sigo adelante sin que afecte la calidad de las siguientes atenciones.',
                47 => 'En momentos de alta demanda, mantengo el mismo nivel de servicio que en momentos tranquilos.',
                48 => 'El estrés acumulado durante un turno intenso se nota en cómo trato a los últimos clientes del día.',
                49 => 'Tengo estrategias personales claras para recuperarme emocionalmente entre una interacción difícil y la siguiente.',
                50 => 'Cuando hay situaciones fuera de mi control (caídas de sistema, cambios de política), me cuesta mantener una actitud positiva con los clientes.',
            ]],
            ['title' => 'Actitudes Generales de Servicio', 'color' => 'slate', 'items' => [
                51 => 'Prefiero resolver problemas de clientes directamente antes que derivarlos a otro departamento siempre que sea posible.',
                52 => 'Creo que el trato amable con el cliente depende mucho del humor con el que uno llegue al trabajo.',
                53 => 'Me molesta cuando los clientes no leen las instrucciones o no entienden cosas que son evidentes.',
                54 => 'Pienso que un buen servicio al cliente es responsabilidad de todos en la empresa, no solo de las áreas de atención.',
                55 => 'Cuando estoy ocupado con un proceso administrativo, prefiero no ser interrumpido aunque un cliente necesite ayuda.',
                56 => 'Considero que la primera impresión que da una empresa depende directamente de cómo es atendido el cliente desde el primer contacto.',
                57 => 'Si un cliente no queda satisfecho después de mis mejores esfuerzos, lo asumo como un resultado fuera de mi control y sigo adelante.',
                58 => 'Creo que algunos clientes simplemente son imposibles de satisfacer y lo mejor es limitarse a cumplir el procedimiento.',
                59 => 'Me resulta natural agradecer a un cliente su paciencia cuando ha tenido que esperar, aunque no haya sido mi culpa.',
                60 => 'Prefiero decirle a un cliente lo que necesita escuchar, aunque no sea lo que quiere oír, si con eso realmente le ayudo.',
            ]],
        ];

        $colorMap = [
            'teal'   => ['bg-teal-50',   'border-teal-200',   'text-teal-700',   'bg-teal-600'],
            'sky'    => ['bg-sky-50',    'border-sky-200',    'text-sky-700',    'bg-sky-600'],
            'violet' => ['bg-violet-50', 'border-violet-200', 'text-violet-700', 'bg-violet-600'],
            'rose'   => ['bg-rose-50',   'border-rose-200',   'text-rose-700',   'bg-rose-600'],
            'amber'  => ['bg-amber-50',  'border-amber-200',  'text-amber-700',  'bg-amber-600'],
            'orange' => ['bg-orange-50', 'border-orange-200', 'text-orange-700', 'bg-orange-600'],
            'slate'  => ['bg-slate-50',  'border-slate-200',  'text-slate-600',  'bg-slate-500'],
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

<script>
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
