@extends('layouts.candidate')

@section('title', 'TSC-SL Hospitalidad — Módulo 1')
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

    <form action="{{ route('candidate.tsc-sl-h.module1.store', $assignment) }}" method="POST" id="form-m1">
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
                'text' => 'Un cliente pidió su filete término medio y al recibirlo está bien cocido. Lleva más de 40 minutos esperando desde que llegó al restaurante y se le ve visiblemente hambriento. Al comunicarle que debe devolver el plato a cocina, ¿qué hace usted primero?',
                'opts' => [
                    'A' => 'Le explica de inmediato que enviará el plato de regreso a cocina y que tardará unos minutos más.',
                    'B' => 'Se disculpa sinceramente, valida que la espera ya ha sido larga, le pregunta si desea algo para tomar mientras espera y le informa el tiempo estimado.',
                    'C' => 'Lleva el plato a cocina primero y luego regresa a informarle al cliente.',
                    'D' => 'Le ofrece otro plato diferente que esté listo de inmediato para evitar una espera adicional.',
                ],
            ],
            2 => [
                'dim'  => 'E1',
                'text' => 'Un cliente mayor está leyendo el menú con dificultad. Ha pedido que le repitan dos veces los platos del día y sigue sin decidir. Hay otras mesas que también esperan su atención.',
                'opts' => [
                    'A' => 'Le hace un resumen rápido de los tres platos más populares para agilizar la decisión.',
                    'B' => 'Le pregunta con calma qué tipo de comida le apetece, le describe con paciencia las opciones que podrían gustarle y le da tiempo suficiente para decidir sin presionarlo.',
                    'C' => 'Le deja el menú y le dice que regresará en unos minutos para no presionarlo.',
                    'D' => 'Atiende las otras mesas y vuelve cuando él levante la mano.',
                ],
            ],
            3 => [
                'dim'  => 'E1',
                'text' => 'Una pareja llega al restaurante para celebrar su aniversario. Llevan 10 minutos sentados y ningún mesero los ha saludado aún. Al acercarse, nota que el ambiente entre ellos está algo tenso por la espera.',
                'opts' => [
                    'A' => 'Los saluda, toma la orden de bebidas y les dice que regresará con el menú completo.',
                    'B' => 'Los saluda con calidez, reconoce con una breve disculpa que tardaron en ser atendidos y los hace sentir bienvenidos antes de proceder con el servicio.',
                    'C' => 'Les entrega el menú y les pide disculpas por la demora sin mayor detalle.',
                    'D' => 'Los atiende con normalidad; una espera breve en un restaurante concurrido es algo que los clientes deben comprender.',
                ],
            ],
            4 => [
                'dim'  => 'E2',
                'text' => 'Un cliente acaba de ordenar un plato que, sin que usted lo supiera, se agotó hace 20 minutos. Debe informarle después de que ya esperó su pedido.',
                'opts' => [
                    'A' => 'Le informa que el plato está agotado y le ofrece el menú para que escoja otro.',
                    'B' => 'Le informa con claridad y honestidad que el plato se agotó, se disculpa por no haberlo comunicado antes, le sugiere dos alternativas similares disponibles y le describe brevemente qué tiene cada una.',
                    'C' => 'Le dice que el plato se demoraría demasiado y le sugiere una alternativa sin dar más explicaciones.',
                    'D' => 'Le pide disculpas, consulta con cocina si pueden prepararlo de alguna forma y regresa con una respuesta.',
                ],
            ],
            5 => [
                'dim'  => 'E2',
                'text' => 'Un cliente con alergia al gluten le pregunta cuáles platos son seguros para él. El menú no tiene marcados los ingredientes y usted no conoce todos los detalles de preparación de cada plato.',
                'opts' => [
                    'A' => 'Le indica los platos que, a su juicio, probablemente no contengan gluten.',
                    'B' => 'Le explica con honestidad que no tiene información precisa sobre todos los ingredientes, consulta directamente con cocina para darle datos exactos y seguros, y lo hace antes de tomar el pedido.',
                    'C' => 'Le dice que consulte la página web del restaurante donde debería estar esa información.',
                    'D' => 'Le recomienda pedir solo ensaladas o platos simples para mayor seguridad.',
                ],
            ],
            6 => [
                'dim'  => 'E2',
                'text' => 'Un grupo de turistas extranjeros llega al restaurante. Hablan poco español e intentan hacer el pedido señalando el menú con dificultad. Ninguno en su turno habla inglés con fluidez.',
                'opts' => [
                    'A' => 'Llama a un compañero que hable inglés aunque se demore unos minutos en llegar.',
                    'B' => 'Usa gestos, señala las fotos del menú, utiliza palabras simples en el idioma del cliente si las conoce y confirma cada ítem antes de anotar la orden para asegurarse de haber entendido bien.',
                    'C' => 'Les entrega el menú y les da más tiempo para que decidan solos.',
                    'D' => 'Les muestra el menú en su celular con el traductor para facilitarles la comunicación.',
                ],
            ],
            7 => [
                'dim'  => 'P1',
                'text' => 'Un cliente dice que su trago no es lo que ordenó: pidió un ron con cola y le trajeron un whisky con agua. Usted no tomó el pedido; lo hizo su compañero.',
                'opts' => [
                    'A' => 'Le explica que no fue usted quien tomó el pedido, pero que lo resolverá de inmediato.',
                    'B' => 'Se disculpa sin entrar en quién cometió el error, retira el trago, confirma exactamente lo que ordenó el cliente y lo reemplaza de inmediato.',
                    'C' => 'Lleva el trago al bar y le pide al bartender que verifique el pedido original.',
                    'D' => 'Le dice que revisará con su compañero para confirmar qué fue lo que se pidió.',
                ],
            ],
            8 => [
                'dim'  => 'P1',
                'text' => 'Una mesa de cumpleaños con 10 personas acaba de recibir los platos: 4 llegaron fríos, 2 son incorrectos y los otros 4 están bien. El festejado mira el desorden con cara de decepción.',
                'opts' => [
                    'A' => 'Se disculpa con el grupo y lleva de regreso a cocina los platos incorrectos y fríos de inmediato.',
                    'B' => 'Reconoce el problema abiertamente, se dirige primero al festejado para disculparse, coordina con cocina la corrección con prioridad, confirma con cada comensal cuál es su pedido y mantiene informado al grupo sobre el progreso.',
                    'C' => 'Va a cocina a reportar los errores y pide que repongan los platos lo antes posible.',
                    'D' => 'Le ofrece al grupo una ronda de bebidas de cortesía mientras cocina corrige los platos.',
                ],
            ],
            9 => [
                'dim'  => 'P1',
                'text' => 'Al momento de pagar, un cliente revisa la cuenta y nota que le cobraron dos veces el mismo plato. Usted fue quien tomó el pedido y presentó la cuenta.',
                'opts' => [
                    'A' => 'Le pide que espere mientras consulta con el cajero si el cobro está correcto.',
                    'B' => 'Revisa la cuenta en el momento, confirma el error, se disculpa, corrige en caja de inmediato y ofrece una disculpa sincera antes de que el cliente pague.',
                    'C' => 'Le dice que probablemente fue un error del sistema y que lo revisará.',
                    'D' => 'Llama al supervisor para que maneje la corrección de la cuenta.',
                ],
            ],
            10 => [
                'dim'  => 'P1',
                'text' => 'Dos de sus mesas llevan más de 35 minutos esperando sus platos principales. La cocina está desbordada por un evento grande. Los clientes empiezan a impacientarse y a mirar hacia la barra.',
                'opts' => [
                    'A' => 'Espera a que los platos salgan de cocina y los lleva tan pronto estén listos.',
                    'B' => 'Se acerca a cada mesa, informa con honestidad la situación de alta demanda, da un tiempo estimado realista, ofrece algo de cortesía mientras esperan (agua, pan, aperitivo) y mantiene la comunicación durante la espera.',
                    'C' => 'Va a cocina a presionar para que prioricen esas mesas cuanto antes.',
                    'D' => 'Informa solo a la mesa que lleva más tiempo esperando y le ofrece cancelar el pedido si lo prefiere.',
                ],
            ],
            11 => [
                'dim'  => 'P2',
                'text' => 'Un cliente ha consumido varias copas de vino y su comportamiento se ha vuelto ruidoso e inapropiado. Otras mesas cercanas están incómodas y uno de los comensales de otra mesa ya lo mira con molestia.',
                'opts' => [
                    'A' => 'Ignora la situación mientras el cliente no le dirija la palabra directamente a usted.',
                    'B' => 'Se acerca al cliente de forma discreta, en tono bajo y respetuoso le hace saber que el nivel de ruido está afectando a otras mesas, le ofrece agua y algo para comer, y si la situación no mejora, avisa a su supervisor.',
                    'C' => 'Le dice directamente que debe bajar la voz porque está molestando a los demás clientes.',
                    'D' => 'Se lo comenta a su supervisor para que tome él la decisión.',
                ],
            ],
            12 => [
                'dim'  => 'P2',
                'text' => 'Una pareja llega sin reserva un viernes en la noche con el restaurante lleno. Insisten en que tienen reserva a nombre de otra persona del grupo que no está presente y que "alguien les confirmó por WhatsApp". No hay ningún registro en el sistema.',
                'opts' => [
                    'A' => 'Les dice que sin reserva registrada en el sistema no puede asignarles mesa.',
                    'B' => 'Verifica el sistema con calma, reconoce que puede haber habido un malentendido, explica la situación con respeto, anota sus datos en lista de espera, les ofrece un lugar para esperar con una bebida de cortesía y les da un tiempo estimado realista.',
                    'C' => 'Les sugiere que llamen a la persona que supuestamente hizo la reserva para aclarar la situación.',
                    'D' => 'Les asigna el lugar menos conveniente del restaurante para no dejarlos sin ninguna opción.',
                ],
            ],
            13 => [
                'dim'  => 'P2',
                'text' => 'Un cliente ha sido cortante y poco amable con usted durante toda la comida: no le da las gracias, chasquea los dedos para llamarle y critica el tiempo de respuesta. Usted se siente frustrado.',
                'opts' => [
                    'A' => 'Atiende solo lo mínimo necesario para no tener que acercarse más de lo indispensable.',
                    'B' => 'Mantiene el mismo nivel de servicio y calidez independientemente del trato recibido, sin confrontar al cliente pero sin deteriorar la calidad de la atención.',
                    'C' => 'Al final, con educación, le hace saber que le hubiera gustado que el trato fuera más amable.',
                    'D' => 'Le pide a su compañero que se haga cargo de esa mesa para que él no tenga que seguir interactuando con ese cliente.',
                ],
            ],
            14 => [
                'dim'  => 'A1',
                'text' => 'Mientras lleva platos a otra mesa, nota que un niño pequeño en una mesa cercana está a punto de jalar el mantel con toda la vajilla encima.',
                'opts' => [
                    'A' => 'Termina de entregar los platos primero y luego va a revisar la situación en la mesa del niño.',
                    'B' => 'Actúa de inmediato: deja los platos con seguridad o los pasa a un colega cercano y se dirige a la mesa para prevenir el accidente antes de que ocurra, con calma y sin alarmar a la familia.',
                    'C' => 'Le hace una señal a la madre del niño para que ella lo controle.',
                    'D' => 'Se lo menciona rápidamente a un colega que esté más cerca de esa mesa.',
                ],
            ],
            15 => [
                'dim'  => 'A1',
                'text' => 'Al pasar por una mesa que no es suya, nota que los clientes llevan varios minutos mirando la carta de vinos con cara de confusión. Nadie les ha ofrecido orientación.',
                'opts' => [
                    'A' => 'Continúa con su recorrido ya que no son su mesa asignada.',
                    'B' => 'Se acerca de forma natural, les pregunta si desean orientación con los vinos, hace preguntas breves sobre sus preferencias y les sugiere dos o tres opciones acordes a sus gustos y al menú que ordenaron.',
                    'C' => 'Les dice que el mesero encargado de esa mesa los atenderá en un momento.',
                    'D' => 'Les pregunta si necesitan ayuda y si dicen que no, continúa con su trabajo sin más.',
                ],
            ],
            16 => [
                'dim'  => 'A1',
                'text' => 'Durante su ronda habitual, nota que en dos de sus mesas los vasos de agua están vacíos. Los clientes no han pedido nada pero se los ve secándose los labios.',
                'opts' => [
                    'A' => 'Espera a que los clientes levanten la mano para pedir agua.',
                    'B' => 'Toma la iniciativa, va al servicio de agua de inmediato y rellena los vasos sin que los clientes tengan que solicitarlo.',
                    'C' => 'Les pregunta si desean más agua antes de ir a buscarla.',
                    'D' => 'Le pide a un colega que llene los vasos mientras usted atiende otras prioridades del turno.',
                ],
            ],
            17 => [
                'dim'  => 'A1',
                'text' => 'Mientras toma el pedido en una mesa, escucha que uno de los comensales le dice al otro: "Hoy es mi cumpleaños, pero no le dije a nadie para no hacer tanto lío."',
                'opts' => [
                    'A' => 'Respeta su privacidad y no hace ningún comentario al respecto durante el servicio.',
                    'B' => 'Al momento de servir el postre, coordina con el equipo para llevar un pequeño detalle o cantar el feliz cumpleaños de forma discreta y cálida, sin convertirlo en un espectáculo que él no quería.',
                    'C' => 'Aprovecha la ocasión para anunciarle al restaurante entero que es su cumpleaños.',
                    'D' => 'Le pregunta directamente si desea que el restaurante haga algo especial por su cumpleaños.',
                ],
            ],
            18 => [
                'dim'  => 'A2',
                'text' => 'Al servir una copa de vino tinto, derrama accidentalmente un poco sobre la manga de la camisa de una clienta. Ella se sobresalta y lo mira con cara de molestia.',
                'opts' => [
                    'A' => 'Se disculpa brevemente y va rápido a buscar servilletas.',
                    'B' => 'Se disculpa de inmediato con sinceridad, le ofrece servilletas limpias y húmedas, le pregunta si hay algo más que pueda hacer por el inconveniente y cierra el episodio con calma antes de continuar el servicio.',
                    'C' => 'Se disculpa y le dice que el restaurante puede ofrecer servicio de tintorería si la prenda lo requiere.',
                    'D' => 'Se disculpa y le ofrece de inmediato que el restaurante asuma el costo de la tintorería.',
                ],
            ],
            19 => [
                'dim'  => 'A2',
                'text' => 'Es un viernes por la noche y el restaurante está a máxima capacidad. Lleva cuatro horas seguidas de servicio intenso, tiene seis mesas activas y cocina acaba de avisarle que habrá demoras en varios platos. En ese momento llega un cliente nuevo que debe sentar.',
                'opts' => [
                    'A' => 'Siente que no puede más y le pide a su supervisor que le asigne ese cliente a un compañero.',
                    'B' => 'Respira, prioriza mentalmente las tareas más urgentes, recibe al nuevo cliente con la misma calma y calidez de siempre y reorganiza sus tiempos para mantener el nivel de servicio en todas las mesas.',
                    'C' => 'Atiende al nuevo cliente pero de forma más rápida y con menos atención al detalle que de costumbre.',
                    'D' => 'Le pide al cliente que espere en la barra mientras termina de atender lo más urgente.',
                ],
            ],
            20 => [
                'dim'  => 'A2',
                'text' => 'Tuvo una discusión fuerte con el chef en cocina porque devolvió un plato de una manera que al chef le pareció irrespetuosa. La situación quedó tensa. Dos minutos después debe atender una mesa nueva que acaba de sentarse.',
                'opts' => [
                    'A' => 'Atiende la mesa pero su tono es más seco y menos cálido de lo habitual.',
                    'B' => 'Toma unos segundos para respirar, suelta la tensión de la discusión y llega a la mesa con su actitud habitual de servicio, consciente de que el cliente no tiene nada que ver con lo ocurrido en cocina.',
                    'C' => 'Atiende la mesa con normalidad; las discusiones internas del trabajo no suelen afectarle mientras está en servicio.',
                    'D' => 'Le comenta brevemente al cliente que "está siendo un día complicado" para contextualizar si algo se nota en su actitud.',
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
