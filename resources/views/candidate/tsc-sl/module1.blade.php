@extends('layouts.candidate')

@section('title', 'TSC-SL — Módulo 1')
@section('show-nav', true)

@section('nav-info')
    <span class="text-sm text-slate-700 font-medium truncate max-w-32 sm:max-w-none">{{ $candidate->name }}</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 sm:py-8">

    {{-- Progreso --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-xs font-semibold text-brand-700">Módulo 1 de 3 — Juicio Situacional</span>
            <span class="text-xs text-slate-400">20 ítems</span>
        </div>
        <div class="progress-track h-2">
            <div class="progress-bar bg-brand-600" style="width: 33%"></div>
        </div>
    </div>

    <div class="mb-5">
        <h1 class="text-lg font-bold text-slate-900">Módulo 1: Juicio Situacional</h1>
        <p class="text-sm text-slate-500 mt-1">Para cada situación, seleccione la opción que mejor describe cómo actuaría usted. Debe responder todos los ítems antes de continuar.</p>
    </div>

    <form action="{{ route('candidate.tsc-sl.module1.store', $assignment) }}" method="POST" id="form-m1">
        @csrf

        @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            Por favor, responda todos los ítems antes de continuar.
        </div>
        @endif

        @php
        $items = [
            1 => [
                'dim'  => 'E1',
                'text' => 'Un cliente llama muy alterado porque recibió un producto diferente al que ordenó. Antes de dejarle hablar más de 10 segundos, usted ya sabe cuál fue el error. ¿Qué hace primero?',
                'opts' => [
                    'A' => 'Le interrumpe amablemente para ahorrar tiempo y explicarle ya la solución.',
                    'B' => 'Lo escucha hasta que termina, valida su frustración y luego confirma que entendió bien el problema antes de ofrecer una solución.',
                    'C' => 'Le dice que entiende su molestia y de inmediato lo transfiere al área de logística.',
                    'D' => 'Le pide que le repita el número de orden para registrarlo en el sistema mientras él habla.',
                ],
            ],
            2 => [
                'dim'  => 'E1',
                'text' => 'Mientras atiende a un cliente por chat, este escribe mensajes muy largos y confusos. Usted ha leído tres veces y aún no está seguro de cuál es su queja exacta.',
                'opts' => [
                    'A' => 'Responde basándose en su mejor suposición para no hacer perder más tiempo al cliente.',
                    'B' => 'Le pide al cliente que sea más breve y concreto para poder ayudarle mejor.',
                    'C' => 'Resume lo que entendió en sus propias palabras y le pregunta si eso refleja correctamente su situación.',
                    'D' => 'Escala la conversación a un colega más experimentado.',
                ],
            ],
            3 => [
                'dim'  => 'E1',
                'text' => 'Un cliente mayor se queja de que la aplicación móvil es muy difícil de usar. Su tono es resignado y dice que "la tecnología de hoy no es para personas como él".',
                'opts' => [
                    'A' => 'Le explica que la app tiene muchos tutoriales disponibles en YouTube.',
                    'B' => 'Le responde que muchos clientes de su edad la usan sin problema.',
                    'C' => 'Valida su frustración, le ofrece una alternativa de atención telefónica y le pregunta si desearía que alguien le ayude a configurar la app.',
                    'D' => 'Le ofrece disculpas y le dice que lo reportará al área de diseño.',
                ],
            ],
            4 => [
                'dim'  => 'E2',
                'text' => 'Un cliente le pregunta por qué su solicitud de crédito fue rechazada. La razón real involucra términos técnicos del sistema de scoring que el cliente no entendería.',
                'opts' => [
                    'A' => 'Le explica el proceso de scoring usando los términos técnicos exactos para ser preciso.',
                    'B' => 'Le dice que los criterios de crédito son confidenciales y que puede volver a solicitarlo en 6 meses.',
                    'C' => 'Traduce los criterios técnicos a un lenguaje cotidiano, le explica qué aspectos influyen en la decisión y le orienta sobre qué podría mejorar.',
                    'D' => 'Le recomienda hablar con un asesor financiero externo.',
                ],
            ],
            5 => [
                'dim'  => 'E2',
                'text' => 'Usted debe informarle a un cliente que la entrega de su pedido se retrasará 5 días más por un problema en la cadena de suministro. El cliente ya había recibido una disculpa por un retraso anterior.',
                'opts' => [
                    'A' => 'Le envía un correo estándar informando el nuevo plazo de entrega.',
                    'B' => 'Lo llama personalmente, reconoce el impacto acumulado de los retrasos, explica la causa sin excusas, ofrece una compensación concreta y confirma el nuevo plazo con compromiso.',
                    'C' => 'Espera a que el cliente llame para evitar generar mayor molestia.',
                    'D' => 'Le notifica por mensaje de texto con el nuevo plazo.',
                ],
            ],
            6 => [
                'dim'  => 'E2',
                'text' => 'Está atendiendo a un cliente por teléfono que habla muy rápido, usa muchos tecnicismos de su industria y asume que usted conoce todos los antecedentes de su caso. Usted no los conoce.',
                'opts' => [
                    'A' => 'Toma nota de lo que entiende y al final pide un resumen.',
                    'B' => 'Lo interrumpe en el primer momento apropiado, se disculpa, le indica que no tiene los antecedentes a la mano y le pide que le explique brevemente el contexto.',
                    'C' => 'Finge que entiende para no hacer sentir al cliente que su caso no está documentado.',
                    'D' => 'Consulta al sistema mientras el cliente habla para buscar la información.',
                ],
            ],
            7 => [
                'dim'  => 'P1',
                'text' => 'Un cliente llama porque lleva 3 semanas esperando la respuesta a un reclamo que abrió. Al revisar el sistema, usted descubre que el reclamo fue cerrado por error sin resolverse.',
                'opts' => [
                    'A' => 'Se disculpa y le dice que reabrirá el caso y que alguien lo llamará en los próximos días.',
                    'B' => 'Le informa lo ocurrido con transparencia, se disculpa por el error, reabre el caso de forma urgente, le da un número de seguimiento, establece un plazo concreto y le ofrece su nombre como punto de contacto.',
                    'C' => 'Se disculpa ampliamente y escala el caso a su supervisor para que lo gestione.',
                    'D' => 'Le dice que el sistema tuvo un fallo y que no es responsabilidad del área.',
                ],
            ],
            8 => [
                'dim'  => 'P1',
                'text' => 'Un cliente solicita un servicio que usted no puede ofrecer exactamente como lo pide, pero existen dos alternativas que podrían satisfacer su necesidad real.',
                'opts' => [
                    'A' => 'Le informa que ese servicio no está disponible y lo lamenta.',
                    'B' => 'Le pregunta qué es lo que realmente necesita lograr con ese servicio y, entendida su necesidad, le presenta las dos alternativas con sus ventajas y limitaciones.',
                    'C' => 'Le ofrece las dos alternativas sin preguntar más, para que él elija.',
                    'D' => 'Le dice que elevará la solicitud a su equipo para ver si pueden adaptarlo.',
                ],
            ],
            9 => [
                'dim'  => 'P1',
                'text' => 'Un cliente regresa por tercera vez con el mismo problema que supuestamente fue resuelto en las dos ocasiones anteriores. Está visiblemente frustrado.',
                'opts' => [
                    'A' => 'Se disculpa nuevamente y aplica el mismo procedimiento de las veces anteriores.',
                    'B' => 'Reconoce que el problema no fue resuelto de raíz, investiga qué ocurrió en los intentos anteriores, identifica la causa real y busca una solución definitiva, comunicando con transparencia el plan.',
                    'C' => 'Escala el caso a su supervisor porque claramente hay algo que está fuera de su alcance.',
                    'D' => 'Le ofrece una compensación para mejorar su experiencia mientras se soluciona.',
                ],
            ],
            10 => [
                'dim'  => 'P2',
                'text' => 'Un cliente le grita por teléfono y utiliza lenguaje ofensivo. Usted no tiene la culpa de lo que ocurrió y considera que el trato es inaceptable.',
                'opts' => [
                    'A' => 'Le dice firmemente que no acepta ese trato y cuelga el teléfono.',
                    'B' => 'Aguanta en silencio hasta que el cliente termine y luego responde.',
                    'C' => 'En tono calmado, le dice: "Entiendo que está muy frustrado y quiero ayudarle. Para poder hacerlo efectivamente, necesito que hablemos en un tono que nos permita trabajar juntos. ¿Le parece bien?"',
                    'D' => 'Le pide que llame más tarde cuando esté más calmado.',
                ],
            ],
            11 => [
                'dim'  => 'P2',
                'text' => 'Un cliente lleva más de 20 minutos discutiendo sobre un cobro que, según la política de la empresa, es correcto. El cliente insiste en que es un error y amenaza con cancelar su cuenta.',
                'opts' => [
                    'A' => 'Cede en el cobro para evitar la cancelación, aunque la política no lo permite.',
                    'B' => 'Repite la misma explicación sobre la política con mayor detalle.',
                    'C' => 'Valida su perspectiva, confirma que el cobro es correcto explicando de forma sencilla por qué, reconoce su derecho a no estar de acuerdo, ofrece escalar a un supervisor si lo desea, y le muestra el impacto de cancelar su cuenta.',
                    'D' => 'Le ofrece una compensación diferente (descuento en próxima compra) para destrabar la situación.',
                ],
            ],
            12 => [
                'dim'  => 'P2',
                'text' => 'Usted atiende a un cliente que claramente está equivocado sobre cómo usar el producto y por eso está ocurriendo el problema, pero el cliente insiste en que el producto tiene un defecto.',
                'opts' => [
                    'A' => 'Le dice directamente que el problema es por mal uso de su parte.',
                    'B' => 'Finge que puede ser un defecto del producto para no confrontarlo.',
                    'C' => 'Valida que el resultado que está obteniendo es frustrante, le muestra de forma respetuosa el uso correcto paso a paso, y confirma que con ese ajuste el producto funciona como se espera.',
                    'D' => 'Le ofrece cambiar el producto para evitar más discusión.',
                ],
            ],
            13 => [
                'dim'  => 'A1',
                'text' => 'Un cliente llama para preguntar únicamente el horario de atención. Mientras habla, menciona de pasada que lleva 3 días intentando acceder a su cuenta online sin éxito.',
                'opts' => [
                    'A' => 'Le da el horario que pidió y cierra la llamada.',
                    'B' => 'Le da el horario y al final le pregunta si el problema con su cuenta está resuelto.',
                    'C' => 'Le da el horario, retoma el comentario sobre la cuenta, le ofrece ayudarle en ese momento y resuelve el problema en la misma llamada.',
                    'D' => 'Le da el horario y le sugiere llamar a la línea de soporte técnico para lo de la cuenta.',
                ],
            ],
            14 => [
                'dim'  => 'A1',
                'text' => 'Es fin de mes y hay una alta demanda. Usted acaba de terminar su turno, pero hay 4 clientes en espera y su reemplazo llega en 10 minutos.',
                'opts' => [
                    'A' => 'Sale de su turno puntualmente como lo indica el procedimiento.',
                    'B' => 'Avisa a su supervisor de la situación y le pide instrucciones.',
                    'C' => 'Inicia la atención del siguiente cliente, informa que tiene 10 minutos disponibles y atiende lo que puede antes de que llegue su reemplazo, asegurando una transición ordenada.',
                    'D' => 'Avisa a los clientes en espera que su reemplazo llegará pronto.',
                ],
            ],
            15 => [
                'dim'  => 'A1',
                'text' => 'Un cliente compra un producto que usted sabe por experiencia que, en un 30% de los casos, genera una dificultad técnica en la configuración inicial.',
                'opts' => [
                    'A' => 'Procesa la venta y no menciona nada. Si tiene el problema, que llame al soporte.',
                    'B' => 'Menciona el posible problema de forma proactiva, le da una guía rápida de cómo resolverlo si ocurre y le ofrece el contacto directo de soporte.',
                    'C' => 'Le pregunta si ya usó ese producto antes para ver si es necesario advertirle.',
                    'D' => 'Le sugiere comprar el modelo de mayor precio que no tiene ese problema.',
                ],
            ],
            16 => [
                'dim'  => 'A2',
                'text' => 'Llevan 4 horas de jornada de alta demanda. Usted acaba de cometer un error con un cliente anterior (le dio información incorrecta) y aún le quedan 3 horas de turno.',
                'opts' => [
                    'A' => 'Se siente muy mal y no logra concentrarse por el resto del turno.',
                    'B' => 'Resuelve el error cometido, toma 60 segundos para reagruparse mentalmente y retoma la atención con el siguiente cliente con el mismo nivel de calidad.',
                    'C' => 'Pide permiso para tomar un descanso breve para recomponerse.',
                    'D' => 'Continúa trabajando pero avisa a su supervisor del error para cubrirse.',
                ],
            ],
            17 => [
                'dim'  => 'A2',
                'text' => 'El sistema de gestión cayó en plena hora pico. Los clientes siguen llegando y usted no tiene acceso a la información. Su supervisor no está disponible.',
                'opts' => [
                    'A' => 'Pide a los clientes que esperen o que regresen cuando el sistema esté activo.',
                    'B' => 'Comunica con calma y transparencia la situación a cada cliente, ofrece alternativas (anotar los datos para llamarlos, atender en canal alterno), mantiene la compostura y organiza la fila de forma proactiva.',
                    'C' => 'Intenta resolver todo de memoria o con papel, lo que puede generar errores.',
                    'D' => 'Detiene la atención y busca al supervisor aunque lleve tiempo.',
                ],
            ],
            18 => [
                'dim'  => 'A2',
                'text' => 'Un colega suyo tuvo una discusión fuerte con usted antes de iniciar turno. Dos minutos después debe atender al primer cliente del día.',
                'opts' => [
                    'A' => 'Atiende al cliente, pero su tono es más frío de lo habitual.',
                    'B' => 'Pide un par de minutos para regularse emocionalmente, hace una pausa breve, y cuando entra a atender al cliente lo hace con su calidad habitual.',
                    'C' => 'Atiende al cliente normalmente; la situación personal no afecta su trabajo.',
                    'D' => 'Le comenta al cliente que tuvo una mañana difícil para explicar si algo se nota.',
                ],
            ],
            19 => [
                'dim'  => 'A1',
                'text' => 'Un cliente solicita hablar con "alguien de mayor jerarquía" sin dar una razón clara. Usted puede resolver lo que necesita, pero el cliente insiste.',
                'opts' => [
                    'A' => 'Lo transfiere de inmediato al supervisor sin preguntar más.',
                    'B' => 'Le pregunta qué desea resolver, le muestra que puede ayudarlo en ese momento, y si aun así insiste en hablar con un superior, lo gestiona con respeto y sin tomarlo personal.',
                    'C' => 'Le explica que su supervisor está ocupado y que él tiene las mismas capacidades.',
                    'D' => 'Le dice que anotará su solicitud y que un supervisor lo llamará más tarde.',
                ],
            ],
            20 => [
                'dim'  => 'P1',
                'text' => 'Un cliente le pide algo que está ligeramente fuera de su competencia pero dentro del alcance de la empresa. Consultar a otro departamento tomará al menos 20 minutos.',
                'opts' => [
                    'A' => 'Le dice que eso no es de su área y le da el número del departamento correspondiente.',
                    'B' => 'Le ofrece encargarse personalmente de obtener la respuesta, le da un tiempo estimado realista y lo llama o escribe cuando tiene la información.',
                    'C' => 'Intenta resolver por su cuenta aunque no esté seguro de la respuesta.',
                    'D' => 'Le pide que llame más tarde cuando pueda consultar con el área correspondiente.',
                ],
            ],
        ];

        $dimColors = [
            'E1' => 'bg-teal-50 text-teal-700 border-teal-200',
            'E2' => 'bg-sky-50 text-sky-700 border-sky-200',
            'P1' => 'bg-violet-50 text-violet-700 border-violet-200',
            'P2' => 'bg-rose-50 text-rose-700 border-rose-200',
            'A1' => 'bg-amber-50 text-amber-700 border-amber-200',
            'A2' => 'bg-orange-50 text-orange-700 border-orange-200',
        ];
        $dimNames = [
            'E1' => 'Empatía',
            'E2' => 'Comunicación',
            'P1' => 'Resolución',
            'P2' => 'Clientes difíciles',
            'A1' => 'Proactividad',
            'A2' => 'Regulación emocional',
        ];
        @endphp

        <div class="space-y-6" id="items-container">
            @foreach($items as $num => $item)
            <div class="card border-slate-100 item-card" id="item-{{ $num }}">
                <div class="card-body">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="flex-shrink-0 w-7 h-7 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-sm font-bold">{{ $num }}</span>
                        </div>
                        <span class="text-[10px] font-semibold border rounded-full px-2 py-0.5 {{ $dimColors[$item['dim']] }}">
                            {{ $dimNames[$item['dim']] }}
                        </span>
                    </div>
                    <p class="text-sm text-slate-800 mb-4 leading-relaxed">{{ $item['text'] }}</p>
                    <div class="space-y-2">
                        @foreach($item['opts'] as $letter => $text)
                        <label class="opt-label flex items-start gap-3 p-3 rounded-lg border border-slate-100 hover:border-brand-300 hover:bg-brand-50/40 cursor-pointer transition-all has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50">
                            <input type="radio"
                                   name="m1[{{ $num }}]"
                                   value="{{ $letter }}"
                                   class="mt-0.5 flex-shrink-0 text-brand-600 focus:ring-brand-500"
                                   required>
                            <span class="text-sm text-slate-700">
                                <span class="font-semibold text-brand-700">{{ $letter }}.</span> {{ $text }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Contador y botón --}}
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
