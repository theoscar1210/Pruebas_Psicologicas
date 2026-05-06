@extends('layouts.candidate')

@section('title', 'TTE-SL — Módulo 2')
@section('show-nav', true)

@section('nav-info')
    <span class="text-sm text-slate-700 font-medium truncate max-w-32 sm:max-w-none">{{ $candidate->name }}</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 sm:py-8">

    {{-- Progreso --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-xs font-semibold text-brand-700">Módulo 2 de 3 — Actitudes colaborativas</span>
            <span class="text-xs text-slate-400">40 ítems</span>
        </div>
        <div class="progress-track h-2">
            <div class="progress-bar bg-brand-600" style="width: 66%"></div>
        </div>
    </div>

    <div class="mb-5">
        <h1 class="text-lg font-bold text-slate-900">Módulo 2: Escala de Actitudes Colaborativas</h1>
        <p class="text-sm text-slate-500 mt-1">Indique qué tan de acuerdo está con cada afirmación sobre su forma de trabajar en equipo. Responda con honestidad sobre cómo piensa y actúa realmente, no sobre cómo le gustaría ser.</p>
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

    <form action="{{ route('candidate.tte-sl.module2.store', $assignment) }}" method="POST" id="form-m2">
        @csrf

        @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            Por favor, responda todos los ítems antes de continuar.
        </div>
        @endif

        @php
        $sections = [
            ['title' => 'C1 — Contribución y Participación Activa', 'color' => 'indigo', 'items' => [
                21 => 'Cuando trabajo en equipo, me aseguro de aportar mis ideas incluso si no estoy completamente seguro de que sean las mejores.',
                22 => 'Comparto información que podría ser útil para mis compañeros, aunque nadie me la haya pedido.',
                23 => 'Prefiero esperar a que me asignen tareas específicas antes de ofrecer mi ayuda en otras áreas.',
                24 => 'En las discusiones grupales, participo activamente aunque haya personas más expertas que yo en el tema.',
                25 => 'Me resulta incómodo participar cuando siento que mis ideas pueden ser rechazadas por el grupo.',
                26 => 'Cuando identifico que un colega está sobrecargado, me ofrezco a ayudarle antes de que me lo pida.',
            ]],
            ['title' => 'C2 — Escucha y Receptividad a Ideas Ajenas', 'color' => 'sky', 'items' => [
                27 => 'Cuando alguien presenta una idea diferente a la mía, me esfuerzo por entenderla completamente antes de evaluarla.',
                28 => 'He cambiado mi posición en un proyecto porque un compañero presentó argumentos que no había considerado.',
                29 => 'Me resulta difícil escuchar con paciencia cuando ya tengo clara la respuesta o la solución.',
                30 => 'Reconozco y menciono abiertamente cuando una idea de un colega es mejor que la mía.',
                31 => 'En reuniones grupales, suelo interrumpir cuando identifico un problema en lo que alguien está diciendo.',
                32 => 'Cuando no entiendo algo que un colega explica, pregunto para aclarar en lugar de asumir.',
            ]],
            ['title' => 'C3 — Apoyo y Soporte Interpersonal', 'color' => 'teal', 'items' => [
                33 => 'Celebro genuinamente los logros de mis compañeros de equipo, aunque no haya contribuido a ellos directamente.',
                34 => 'Cuando un colega enfrenta una dificultad, busco la forma de apoyarlo aunque suponga un esfuerzo adicional para mí.',
                35 => 'Considero que el apoyo entre compañeros debe ser recíproco: ayudo cuando sé que me van a devolver el favor.',
                36 => 'Me resulta fácil dar espacio y protagonismo a otros cuando el equipo lo necesita.',
                37 => 'Me molesta cuando un colega recibe más reconocimiento que yo por un trabajo en el que contribuí igual o más.',
                38 => 'Estoy dispuesto a sacrificar visibilidad personal si eso beneficia al resultado del equipo.',
            ]],
            ['title' => 'C4 — Gestión Constructiva del Conflicto', 'color' => 'rose', 'items' => [
                39 => 'Cuando tengo un desacuerdo con un colega, prefiero hablarlo directamente con él antes que comentarlo con otros.',
                40 => 'Soy capaz de expresar mi desacuerdo con alguien sin atacar a la persona ni deteriorar la relación.',
                41 => 'Prefiero evitar los conflictos dentro del equipo aunque eso implique no decir lo que pienso.',
                42 => 'Cuando hay una tensión grupal no resuelta, tiendo a nombrarla para que el equipo pueda trabajarla.',
                43 => 'Me cuesta mantener la calma y la objetividad cuando siento que alguien me critica injustamente.',
                44 => 'Veo los desacuerdos como oportunidades de mejorar los resultados, no como amenazas a la relación.',
            ]],
            ['title' => 'C5 — Responsabilidad Compartida y Rendición de Cuentas', 'color' => 'orange', 'items' => [
                45 => 'Cuando el equipo falla en una entrega, asumo mi parte de responsabilidad aunque mis tareas estuvieran al día.',
                46 => 'Si un colega no cumple con su compromiso y afecta al equipo, se lo digo directamente con respeto.',
                47 => 'Cuando algo sale mal en un proyecto grupal, mi primera reacción es pensar en qué hice yo que pudo haber contribuido al problema.',
                48 => 'Prefiero no señalar los incumplimientos de un colega para no generar conflicto en el equipo.',
                49 => 'Cumplo con mis compromisos hacia el equipo incluso cuando tengo prioridades personales que me presionan.',
                50 => 'Cuando cometo un error que afecta a otros, lo reconozco de inmediato y propongo cómo corregirlo.',
            ]],
            ['title' => 'C6 — Adaptabilidad al Estilo de los Demás', 'color' => 'violet', 'items' => [
                51 => 'Ajusto mi forma de comunicarme según las preferencias del compañero con quien estoy trabajando.',
                52 => 'Puedo trabajar cómodamente con personas que tienen estilos de trabajo muy diferentes al mío.',
                53 => 'Me resulta difícil cambiar mi forma habitual de hacer las cosas para adaptarme a cómo trabaja el equipo.',
                54 => 'Una vez que el equipo toma una decisión, la apoyo activamente aunque no haya sido mi opción preferida.',
                55 => 'Me molesta cuando los cambios en los procesos del equipo interrumpen la forma en que ya venía trabajando.',
                56 => 'Valoro trabajar con personas que piensan diferente a mí porque enriquece el resultado final.',
            ]],
            ['title' => 'C7 — Orientación al Objetivo Colectivo', 'color' => 'amber', 'items' => [
                57 => 'En situaciones de presión, priorizo el objetivo del equipo sobre mis intereses o reconocimientos personales.',
                58 => 'Cuando el equipo toma una decisión con la que no estoy de acuerdo, la ejecuto igual con el mismo nivel de compromiso.',
                59 => 'Me resulta difícil dejar de lado mis propias metas cuando entran en conflicto con las del equipo.',
                60 => 'Prefiero que el equipo tenga éxito a que yo tenga razón.',
            ]],
        ];

        $colorMap = [
            'indigo' => ['bg-indigo-50', 'border-indigo-200', 'text-indigo-700'],
            'sky'    => ['bg-sky-50',    'border-sky-200',    'text-sky-700'],
            'teal'   => ['bg-teal-50',   'border-teal-200',   'text-teal-700'],
            'rose'   => ['bg-rose-50',   'border-rose-200',   'text-rose-700'],
            'orange' => ['bg-orange-50', 'border-orange-200', 'text-orange-700'],
            'violet' => ['bg-violet-50', 'border-violet-200', 'text-violet-700'],
            'amber'  => ['bg-amber-50',  'border-amber-200',  'text-amber-700'],
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
