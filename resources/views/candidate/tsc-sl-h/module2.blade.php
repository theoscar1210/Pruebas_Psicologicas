@extends('layouts.candidate')

@section('title', 'TSC-SL Hospitalidad — Módulo 2')
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
        <p class="text-sm text-slate-500 mt-1">Indique qué tan de acuerdo está con cada afirmación sobre su forma de trabajar en servicio de mesa y hospitalidad. Responda con honestidad sobre cómo piensa y actúa realmente.</p>
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

    <form action="{{ route('candidate.tsc-sl-h.module2.store', $assignment) }}" method="POST" id="form-m2">
        @csrf

        @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            Por favor, responda todos los ítems antes de continuar.
        </div>
        @endif

        @php
        $sections = [
            ['title' => 'E1 — Empatía y Escucha Activa', 'color' => 'teal', 'items' => [
                21 => 'Cuando un cliente está frustrado porque su pedido tardó más de lo esperado, me aseguro de escucharlo completamente antes de explicarle lo que ocurrió.',
                22 => 'Me resulta fácil ponerme en el lugar del cliente e imaginar cómo se siente cuando el servicio no cumple sus expectativas.',
                23 => 'Cuando un cliente me da información confusa sobre lo que quiere ordenar, prefiero interpretarlo por mi cuenta antes que hacer demasiadas preguntas.',
                24 => 'Considero que reconocer el estado emocional del cliente es tan importante como resolver rápidamente su pedido.',
                25 => 'Me incomoda cuando los clientes se toman mucho tiempo para decidir qué ordenar, especialmente cuando hay muchas mesas que atender.',
            ]],
            ['title' => 'E2 — Comunicación Efectiva con el Cliente', 'color' => 'sky', 'items' => [
                26 => 'Adapto mi tono y lenguaje según el tipo de cliente: no me expreso igual con una familia que con un grupo corporativo.',
                27 => 'Antes de retirarme de una mesa, me aseguro de que el cliente haya comprendido bien lo que le comuniqué (tiempo de espera, cambio en el pedido, etc.).',
                28 => 'Cuando debo dar una mala noticia al cliente (plato agotado, demora en cocina, cobro adicional), tiendo a suavizarlo tanto que el mensaje no queda del todo claro.',
                29 => 'Soy capaz de comunicar una limitación del restaurante (política de cobro, cierre de cocina, ingrediente no disponible) de forma respetuosa sin que el cliente se sienta rechazado.',
                30 => 'Prefiero dar una respuesta rápida aunque no sea completamente exacta antes que hacer esperar al cliente mientras verifico la información.',
            ]],
            ['title' => 'P1 — Resolución de Problemas del Cliente', 'color' => 'violet', 'items' => [
                31 => 'Cuando un cliente reporta el mismo error en su pedido por segunda vez en la misma visita, busco entender qué falló en el proceso antes de simplemente corregirlo.',
                32 => 'Me siento cómodo coordinando con cocina, barra o caja para resolver un problema del cliente sin que él tenga que esperar más de lo necesario.',
                33 => 'Si no tengo la solución inmediata para un problema del cliente, prefiero dar una respuesta vaga antes que admitir que debo consultar con otra área.',
                34 => 'Hago seguimiento a los pedidos especiales o solicitudes pendientes de mis mesas sin que los clientes tengan que recordármelo.',
                35 => 'Me resulta difícil proponer alternativas cuando el plato o bebida que el cliente quiere no está disponible.',
            ]],
            ['title' => 'P2 — Manejo de Clientes Difíciles', 'color' => 'rose', 'items' => [
                36 => 'Cuando un cliente se comporta de manera agresiva o irrespetuosa, logro mantener la calma y el enfoque en encontrar una solución.',
                37 => 'Soy capaz de poner un límite respetuoso con un cliente que tiene un comportamiento inadecuado sin deteriorar el servicio ni escalar el conflicto.',
                38 => 'Me afecta emocionalmente durante el resto del turno cuando tuve una interacción muy tensa con un cliente difícil.',
                39 => 'Entiendo que la frustración de un cliente ante un error del servicio generalmente no es algo personal hacia mí.',
                40 => 'Cuando un cliente insiste en algo que va en contra de las políticas del restaurante, me resulta difícil mantenerme firme sin sentirme culpable.',
            ]],
            ['title' => 'A1 — Actitud de Servicio y Proactividad', 'color' => 'amber', 'items' => [
                41 => 'Me siento genuinamente satisfecho cuando logro que la experiencia de un cliente en el restaurante supere sus expectativas.',
                42 => 'Cuando noto que un cliente podría necesitar algo más de lo que pidió, lo menciono sin esperar a que lo solicite.',
                43 => 'Considero que mi trabajo termina cuando entrego el pedido al cliente; lo que ocurra después es responsabilidad de otra persona.',
                44 => 'La satisfacción del cliente es un motivador real para mí en el día a día, no solo una exigencia del cargo.',
                45 => 'Me resulta fácil mantener una actitud cálida y atenta con los clientes incluso cuando el turno ha sido muy intenso.',
            ]],
            ['title' => 'A2 — Tolerancia a la Presión y Regulación Emocional', 'color' => 'orange', 'items' => [
                46 => 'Cuando cometo un error en la atención (pedido equivocado, olvido de una mesa), lo asumo, lo corrijo y sigo adelante sin que afecte la calidad de las siguientes atenciones.',
                47 => 'En noches de alta demanda (viernes, sábados, días festivos), mantengo el mismo nivel de servicio que en momentos tranquilos.',
                48 => 'El cansancio acumulado durante un turno intenso se nota en la manera como trato a los últimos clientes del servicio.',
                49 => 'Tengo estrategias personales claras para recuperarme emocionalmente entre una interacción difícil con un cliente y la siguiente.',
                50 => 'Cuando ocurren situaciones fuera de mi control durante el turno (falla en el sistema de caja, corte de luz, escasez de insumos), me cuesta mantener una actitud positiva con los clientes.',
            ]],
            ['title' => 'Actitudes Generales de Servicio en Hospitalidad', 'color' => 'slate', 'items' => [
                51 => 'Prefiero resolver directamente las necesidades de un cliente antes que derivarlo a otra persona, cuando tengo la posibilidad de ayudar.',
                52 => 'Creo que la calidez en la atención al cliente depende mucho del humor con el que uno llegue al trabajo.',
                53 => 'Me molesta cuando los clientes hacen solicitudes que considero poco razonables o que van en contra de lo que ofrece el menú o las políticas del restaurante.',
                54 => 'Creo que brindar una experiencia excepcional al cliente es responsabilidad de todo el equipo del restaurante, sin importar el rol de cada uno.',
                55 => 'Cuando estoy ocupado con una tarea del servicio, prefiero no ser interrumpido aunque un cliente necesite orientación en ese momento.',
                56 => 'Considero que la primera impresión que tiene un cliente del restaurante depende directamente de cómo es recibido desde el primer momento.',
                57 => 'Si un cliente no queda satisfecho a pesar de mis mejores esfuerzos dentro de lo posible, lo asumo como un resultado fuera de mi control y sigo adelante con la misma disposición.',
                58 => 'Creo que hay clientes que simplemente son imposibles de complacer y lo mejor es limitarse a cumplir el protocolo mínimo.',
                59 => 'Me resulta natural pedirle disculpas a un cliente por una demora aunque no haya sido responsabilidad mía directamente.',
                60 => 'Prefiero decirle a un cliente lo que realmente está disponible o lo que puede esperar, aunque no sea lo que quiere escuchar, si eso le ayuda a tomar la mejor decisión.',
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
