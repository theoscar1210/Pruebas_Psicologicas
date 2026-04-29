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
                'text' => 'Un socio llama muy molesto porque su habitación no fue preparada como solicitó: almohadas especiales y cuna para su bebé. Usted ya sabe cuál fue el error antes de que termine de hablar. ¿Qué hace primero?',
                'opts' => [
                    'A' => 'Lo interrumpe amablemente para ahorrar tiempo y explicarle que enviará a ama de llaves de inmediato.',
                    'B' => 'Lo escucha hasta que termina, valida su incomodidad y confirma los detalles del pedido antes de ofrecerle una solución.',
                    'C' => 'Le dice que entiende y lo transfiere directamente al área de habitaciones.',
                    'D' => 'Le pide su número de habitación para registrarlo en el sistema mientras él sigue hablando.',
                ],
            ],
            2 => [
                'dim'  => 'E1',
                'text' => 'Un invitado envía mensajes por WhatsApp sobre un problema con su reserva en el club. Ha escrito tres mensajes muy largos y contradictorios, y usted aún no tiene claro cuál es exactamente el inconveniente.',
                'opts' => [
                    'A' => 'Responde con lo que supone para no hacerle perder más tiempo al invitado.',
                    'B' => 'Le pide que sea más concreto y breve para poder ayudarle mejor.',
                    'C' => 'Resume en sus propias palabras lo que entendió y le pregunta si eso refleja correctamente su situación.',
                    'D' => 'Escala la conversación a un colega con más experiencia en reservas.',
                ],
            ],
            3 => [
                'dim'  => 'E1',
                'text' => 'Un socio mayor se queja de que el sistema de reservas en línea del club es muy complicado. Su tono es resignado y dice: "Esto no es para personas de mi edad. Ya no sé cómo hacer nada aquí."',
                'opts' => [
                    'A' => 'Le explica que en la página web del club hay tutoriales disponibles.',
                    'B' => 'Le responde que varios socios de su edad lo usan sin ningún problema.',
                    'C' => 'Valida su frustración, le ofrece hacer la reserva personalmente por él y le pregunta si desea que alguien le explique el sistema con calma.',
                    'D' => 'Le ofrece sus disculpas y le dice que reportará el problema al área de sistemas.',
                ],
            ],
            4 => [
                'dim'  => 'E2',
                'text' => 'Un socio pregunta por qué su invitado fue detenido en el acceso al club. La razón real tiene que ver con un error técnico en el sistema de validación de carnés que el socio probablemente no entenderá si se lo explica en términos técnicos.',
                'opts' => [
                    'A' => 'Le explica el error técnico usando los términos exactos del sistema para ser preciso.',
                    'B' => 'Le dice que los procedimientos de seguridad son internos y que ya está resuelto.',
                    'C' => 'Traduce el problema a un lenguaje sencillo, le explica qué ocurrió y le indica cómo evitar que se repita.',
                    'D' => 'Le sugiere hablar directamente con la dirección del club para mayor detalle.',
                ],
            ],
            5 => [
                'dim'  => 'E2',
                'text' => 'Debe informarle a un socio que la cancha de squash que reservó para hoy está fuera de servicio por un mantenimiento urgente no programado. Este socio ya tuvo un inconveniente similar el mes pasado por el mismo motivo.',
                'opts' => [
                    'A' => 'Le envía un mensaje de texto informando la cancelación con el mensaje estándar del club.',
                    'B' => 'Lo llama personalmente, reconoce el impacto de los inconvenientes acumulados, explica la causa sin excusas, ofrece una alternativa concreta y un crédito de cortesía.',
                    'C' => 'Espera a que el socio llegue al club para informarle en persona.',
                    'D' => 'Le notifica por el chat interno del club con el aviso habitual de mantenimiento.',
                ],
            ],
            6 => [
                'dim'  => 'E2',
                'text' => 'Un canje llama para hacer una reserva en el restaurante del club. Habla muy rápido, usa códigos y términos del convenio de su empresa que usted no conoce, y asume que usted tiene toda la información del acuerdo.',
                'opts' => [
                    'A' => 'Toma nota de lo que entiende y al final le pide un resumen breve.',
                    'B' => 'En el primer momento apropiado lo interrumpe con respeto, le indica que no tiene a la mano los detalles del convenio y le pide que le explique brevemente el contexto.',
                    'C' => 'Finge que entiende para no hacerle sentir que el convenio no está registrado en el sistema.',
                    'D' => 'Consulta el sistema de convenios mientras el canje habla para buscar la información.',
                ],
            ],
            7 => [
                'dim'  => 'P1',
                'text' => 'Un socio llama porque lleva dos semanas esperando la reparación de un daño en su locker del vestuario. Al revisar el sistema, usted descubre que la orden de trabajo fue cerrada por mantenimiento sin haberse ejecutado.',
                'opts' => [
                    'A' => 'Se disculpa y le dice que reabrirá la orden y que alguien lo llamará en los próximos días.',
                    'B' => 'Le informa con transparencia lo ocurrido, se disculpa, reabre la orden de forma urgente, le da un número de seguimiento, establece un plazo concreto y le ofrece su nombre como punto de contacto.',
                    'C' => 'Se disculpa ampliamente y escala el caso al jefe de mantenimiento para que lo gestione.',
                    'D' => 'Le dice que el sistema tuvo un problema técnico y que no fue responsabilidad directa del área.',
                ],
            ],
            8 => [
                'dim'  => 'P1',
                'text' => 'Un socio quiere reservar el salón principal del club para un evento familiar, pero está ocupado el día que necesita. Existen dos espacios alternativos del club que podrían funcionar perfectamente para lo que describe.',
                'opts' => [
                    'A' => 'Le informa que el salón no está disponible ese día y que lo lamenta.',
                    'B' => 'Le pregunta el propósito del evento y el número de personas esperadas y, entendida su necesidad, le presenta los dos espacios alternativos con sus ventajas y limitaciones.',
                    'C' => 'Le muestra los dos espacios alternativos sin preguntar más, para que él elija el que prefiera.',
                    'D' => 'Le dice que consultará con coordinación de eventos para ver si pueden hacer algún ajuste.',
                ],
            ],
            9 => [
                'dim'  => 'P1',
                'text' => 'Un socio regresa por tercera vez con el mismo problema: la temperatura del agua de su habitación nunca queda como la solicita. En las dos ocasiones anteriores se le dijo que estaba resuelto. Está visiblemente frustrado.',
                'opts' => [
                    'A' => 'Se disculpa nuevamente y aplica el mismo procedimiento de las veces anteriores.',
                    'B' => 'Reconoce abiertamente que el problema no se ha resuelto de raíz, investiga qué ocurrió en los intentos anteriores, coordina con mantenimiento para identificar la causa real y propone un plan de solución definitivo que le comunica con transparencia.',
                    'C' => 'Escala el caso al jefe de mantenimiento porque está claramente fuera de su alcance.',
                    'D' => 'Le ofrece el cambio a otra habitación como compensación mientras se soluciona el problema.',
                ],
            ],
            10 => [
                'dim'  => 'P2',
                'text' => 'Un socio llama furioso porque le aparece un cobro en su cuenta que dice no haber consumido. Su tono es muy agresivo. Usted ha revisado el sistema y el cobro corresponde a un consumo real registrado.',
                'opts' => [
                    'A' => 'Le dice firmemente que no acepta ese trato y pone en espera la llamada.',
                    'B' => 'Aguanta el tono en silencio hasta que el socio termine de hablar y luego responde.',
                    'C' => 'En tono calmado le dice: "Entiendo que está muy molesto y quiero aclarar esto con usted. Para poder ayudarle de la mejor manera, necesito que hablemos en un tono que nos permita trabajar juntos. ¿Le parece bien?"',
                    'D' => 'Le pide que llame más tarde cuando esté más tranquilo para revisar el caso juntos.',
                ],
            ],
            11 => [
                'dim'  => 'P2',
                'text' => 'Un invitado lleva más de 15 minutos discutiendo el cobro de un servicio de spa que, según la política del club, aplica a no-socios. El invitado insiste en que nadie le informó y amenaza con no volver al club.',
                'opts' => [
                    'A' => 'Le elimina el cobro para evitar el conflicto, aunque la política del club no lo permite.',
                    'B' => 'Repite la misma explicación sobre la política con mayor detalle y énfasis.',
                    'C' => 'Valida su perspectiva, confirma que el cobro aplica según las políticas del club explicando de forma sencilla por qué, reconoce su derecho a no estar de acuerdo, ofrece escalar con el supervisor si lo desea, y le indica cómo informarse sobre los servicios incluidos en futuras visitas.',
                    'D' => 'Le ofrece un descuento en el próximo servicio para destrabar la situación y mejorar su experiencia.',
                ],
            ],
            12 => [
                'dim'  => 'P2',
                'text' => 'Un socio insiste en que la piscina semiolímpica debería estar abierta a esa hora "porque siempre ha estado disponible". El horario cambió hace dos meses. El socio se molesta cuando usted le indica el horario actual.',
                'opts' => [
                    'A' => 'Le dice directamente que está equivocado y que el horario cambió hace tiempo.',
                    'B' => 'Evita la confrontación y le dice que "revisará" el tema para no generarle más molestia.',
                    'C' => 'Valida que el cambio de horario pudo haberse comunicado mejor, le muestra el horario vigente de forma respetuosa y le explica dónde puede consultar siempre los horarios actualizados del club.',
                    'D' => 'Le ofrece hablar con el coordinador de deportes para ver si pueden hacer una excepción.',
                ],
            ],
            13 => [
                'dim'  => 'A1',
                'text' => 'Un socio llama para preguntar el horario del restaurante del club. Mientras habla, menciona de pasada que ayer la toalla de su habitación estaba manchada y que "lo dejó pasar porque no quería molestar".',
                'opts' => [
                    'A' => 'Le da el horario del restaurante que pidió y cierra la llamada.',
                    'B' => 'Le da el horario y al final le pregunta si el inconveniente con la toalla fue atendido.',
                    'C' => 'Le da el horario, retoma el comentario de la toalla, le pide disculpas sinceras y le solicita el número de habitación para enviar ropa de cama fresca de inmediato.',
                    'D' => 'Le da el horario y le sugiere reportar lo de la toalla directamente al área de ama de llaves.',
                ],
            ],
            14 => [
                'dim'  => 'A1',
                'text' => 'Es un fin de semana de alta temporada en el club. Usted acaba de terminar su turno en recepción, pero hay tres socios esperando ser atendidos y su reemplazo llega en 10 minutos.',
                'opts' => [
                    'A' => 'Sale puntualmente de su turno como lo indica el procedimiento interno.',
                    'B' => 'Avisa a su supervisor de la situación y le pide instrucciones antes de hacer cualquier cosa.',
                    'C' => 'Inicia la atención del siguiente socio, le informa que tiene 10 minutos disponibles y atiende lo que puede, asegurando una transición ordenada al llegar su reemplazo.',
                    'D' => 'Avisa a los socios en espera que su reemplazo llegará pronto y que los atenderá.',
                ],
            ],
            15 => [
                'dim'  => 'A1',
                'text' => 'Un invitado se dirige al gimnasio a usar la bicicleta estática. Usted sabe por experiencia que esa máquina tiene el asiento que se suelta con cierta frecuencia, aunque no hay una orden de mantenimiento activa.',
                'opts' => [
                    'A' => 'Le entrega acceso al gimnasio sin mencionar nada. Si hay problema, que avise al instructor.',
                    'B' => 'Le menciona el posible problema de forma proactiva, le explica cómo ajustar el asiento si ocurre y le indica dónde está el instructor por si necesita asistencia.',
                    'C' => 'Le pregunta si ya ha usado esa bicicleta antes para ver si es necesario advertirle.',
                    'D' => 'Le sugiere usar otra bicicleta diferente que está en perfecto estado, sin explicar el porqué.',
                ],
            ],
            16 => [
                'dim'  => 'A2',
                'text' => 'Llevan cinco horas de turno de alta demanda en el restaurante del club. Usted acaba de anotar mal el pedido de una mesa de socios y ya lo envió a cocina. Aún quedan tres horas de turno.',
                'opts' => [
                    'A' => 'Se siente muy mal con el error y le cuesta concentrarse bien en el resto del turno.',
                    'B' => 'Corrige el pedido de inmediato con cocina, se disculpa con la mesa afectada, toma un momento breve para reagruparse y retoma la atención del resto de las mesas con la misma calidad.',
                    'C' => 'Pide al supervisor un descanso breve para recomponerse emocionalmente.',
                    'D' => 'Continúa trabajando normalmente pero avisa al supervisor del error para cubrirse ante cualquier reclamo.',
                ],
            ],
            17 => [
                'dim'  => 'A2',
                'text' => 'El sistema de validación de socios en el acceso al club se cayó justo cuando comienza el mayor flujo de ingreso del fin de semana. Los socios siguen llegando y usted no tiene acceso al sistema. Su supervisor no está disponible.',
                'opts' => [
                    'A' => 'Pide a los socios que esperen afuera o regresen cuando el sistema esté activo.',
                    'B' => 'Comunica con calma la situación a cada socio, ofrece alternativas (validar con carné físico, registrar datos manualmente), mantiene la compostura y organiza el ingreso de forma proactiva.',
                    'C' => 'Intenta validar de memoria quiénes son socios conocidos, lo que puede generar errores e inconvenientes.',
                    'D' => 'Detiene el acceso y busca al supervisor aunque tome tiempo encontrarlo.',
                ],
            ],
            18 => [
                'dim'  => 'A2',
                'text' => 'Usted tuvo un malentendido fuerte con un colega del área de mantenimiento justo antes de comenzar su turno en recepción. Dos minutos después debe atender a los primeros socios del día.',
                'opts' => [
                    'A' => 'Atiende a los socios, pero su tono es más seco y menos cálido de lo habitual.',
                    'B' => 'Toma un par de minutos para regularse emocionalmente y cuando comienza la atención lo hace con su calidad y calidez habitual.',
                    'C' => 'Atiende a los socios normalmente; la situación con el colega no interfiere con su desempeño.',
                    'D' => 'Le comenta al primer socio que tuvo una mañana complicada para explicar si algo se nota en su actitud.',
                ],
            ],
            19 => [
                'dim'  => 'A1',
                'text' => 'Un canje solicita hablar con "el gerente del club" sin dar una razón clara. Usted puede resolver perfectamente lo que necesita, pero el canje insiste en que solo hablará con el gerente.',
                'opts' => [
                    'A' => 'Lo transfiere de inmediato al gerente sin preguntar más ni intentar ayudarlo.',
                    'B' => 'Le pregunta qué desea resolver, le demuestra que puede ayudarlo en ese momento y si aun así insiste en hablar con el gerente, gestiona esa solicitud con respeto y sin tomarlo como algo personal.',
                    'C' => 'Le explica que el gerente está en reunión y que él tiene exactamente las mismas capacidades para atenderlo.',
                    'D' => 'Le dice que dejará el mensaje al gerente y que alguien le llamará más tarde.',
                ],
            ],
            20 => [
                'dim'  => 'P1',
                'text' => 'Un socio pregunta sobre la posibilidad de organizar un evento corporativo grande en las instalaciones del club, algo que está fuera de sus funciones habituales. Consultar con el coordinador de eventos tomará al menos 20 minutos.',
                'opts' => [
                    'A' => 'Le dice que eso no es de su área y le da el número de teléfono del departamento de eventos.',
                    'B' => 'Le ofrece encargarse personalmente de consultar con el área de eventos, le da un tiempo estimado realista y lo contacta cuando tiene la información completa.',
                    'C' => 'Intenta responder por su cuenta con lo que recuerda aunque no esté seguro de todos los detalles.',
                    'D' => 'Le pide que regrese o llame más tarde cuando pueda consultar con el coordinador de eventos.',
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
