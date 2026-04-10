<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Siembra las baterías de pruebas psicológicas estandarizadas:
 *
 *  1. Big Five (IPIP-50) — Personalidad
 *  2. 16PF simplificado (32 ítems) — Personalidad
 *  3. Matrices de Raven (36 ítems) — Cognitivo
 *  4. Assessment Center (5 escenarios escritos) — Competencias
 *  5. Wartegg (8 indicadores para evaluador) — Proyectivo
 *  6. Entrevista STAR (15 preguntas evaluador) — Entrevista
 */
class PsychologicalTestsSeeder extends Seeder
{
    private int $adminId;

    public function run(): void
    {
        $this->adminId = User::first()->id;

        $this->seedBigFive();
        $this->seedSixteenPF();
        $this->seedRaven();
        $this->seedAssessmentCenter();
        $this->seedWartegg();
        $this->seedStarInterview();
    }

    // ══════════════════════════════════════════════════════════════════════
    // 1. BIG FIVE — IPIP-50 (Spanish)
    // ══════════════════════════════════════════════════════════════════════
    private function seedBigFive(): void
    {
        $test = Test::firstOrCreate(
            ['test_type' => 'big_five'],
            [
                'name'           => 'Big Five — Evaluación de Personalidad (IPIP-50)',
                'description'    => 'Inventario de personalidad basado en el modelo de los Cinco Grandes Factores (OCEAN). 50 ítems tipo Likert.',
                'instructions'   => 'A continuación encontrarás una serie de afirmaciones sobre tu forma de ser y comportarte. No hay respuestas correctas ni incorrectas. Responde con sinceridad según cómo eres habitualmente, no como te gustaría ser. Usa la siguiente escala: 1 = Muy en desacuerdo, 5 = Muy de acuerdo.',
                'module'         => 'personalidad',
                'test_type'      => 'big_five',
                'evaluator_scored' => false,
                'scoring_method' => 'dimensional',
                'time_limit'     => 20,
                'passing_score'  => 0,
                'is_active'      => true,
                'created_by'     => $this->adminId,
            ]
        );

        if ($test->questions()->exists()) return;

        $likertOptions = [
            ['text' => 'Muy en desacuerdo',           'value' => 1],
            ['text' => 'En desacuerdo',                'value' => 2],
            ['text' => 'Ni de acuerdo ni en desacuerdo','value'=> 3],
            ['text' => 'De acuerdo',                   'value' => 4],
            ['text' => 'Muy de acuerdo',               'value' => 5],
        ];

        // [texto, dimensión, reverse_scored]
        $items = [
            // Extraversión (E)
            ['Soy el alma de las reuniones y fiestas.',                              'extraversion', false],
            ['Prefiero mantenerme en un segundo plano en grupos.',                   'extraversion', true],
            ['Me siento cómodo/a hablando con personas desconocidas.',               'extraversion', false],
            ['Casi no intervengo en conversaciones de grupo.',                       'extraversion', true],
            ['Tomo la iniciativa para comenzar conversaciones.',                     'extraversion', false],
            ['Tengo poco que aportar en conversaciones grupales.',                   'extraversion', true],
            ['Me relaciono fácilmente con todo tipo de personas.',                   'extraversion', false],
            ['Prefiero pasar desapercibido/a en reuniones sociales.',               'extraversion', true],
            ['No me incomoda ser el centro de atención.',                            'extraversion', false],
            ['En ambientes nuevos tiendo a quedarme en silencio.',                   'extraversion', true],
            // Amabilidad (A)
            ['Genuinamente me importa el bienestar de las personas que me rodean.', 'agreeableness', false],
            ['En ocasiones digo cosas hirientes sin pensarlo.',                      'agreeableness', true],
            ['Me identifico fácilmente con los sentimientos ajenos.',                'agreeableness', false],
            ['Me cuesta preocuparme por los problemas de otras personas.',           'agreeableness', true],
            ['Soy compasivo/a con quienes pasan por dificultades.',                  'agreeableness', false],
            ['A veces insulto o critico duramente a las personas.',                  'agreeableness', true],
            ['Busco soluciones donde todos puedan ganar en los conflictos.',         'agreeableness', false],
            ['Prefiero anteponer mis intereses a los de los demás.',                 'agreeableness', true],
            ['Percibo y respondo a las emociones de quienes me rodean.',             'agreeableness', false],
            ['En discusiones, me cuesta ceder aunque el otro tenga razón.',          'agreeableness', true],
            // Responsabilidad (C)
            ['Siempre llego preparado/a a mis compromisos y reuniones.',             'conscientiousness', false],
            ['Con frecuencia pierdo u olvido mis pertenencias.',                     'conscientiousness', true],
            ['Pongo atención minuciosa a los detalles de mi trabajo.',               'conscientiousness', false],
            ['Suelo dejar tareas a medias sin terminarlas.',                         'conscientiousness', true],
            ['Me fijo metas claras y trabajo disciplinadamente para alcanzarlas.',   'conscientiousness', false],
            ['Olvido con facilidad las responsabilidades que tengo pendientes.',     'conscientiousness', true],
            ['Cuando asumo un compromiso, siempre lo cumplo.',                       'conscientiousness', false],
            ['Mi espacio de trabajo suele estar desordenado.',                       'conscientiousness', true],
            ['Establezco prioridades y las sigo con constancia.',                    'conscientiousness', false],
            ['Pospongo las tareas que me parecen difíciles o desagradables.',        'conscientiousness', true],
            // Neuroticismo (N)
            ['Me estreso con facilidad ante situaciones de presión.',                'neuroticism', false],
            ['Generalmente mantengo la calma incluso en situaciones difíciles.',     'neuroticism', true],
            ['Me preocupo en exceso por las cosas.',                                 'neuroticism', false],
            ['Rara vez me siento triste o deprimido/a sin razón aparente.',          'neuroticism', true],
            ['Me altero emocionalmente con más facilidad que otras personas.',       'neuroticism', false],
            ['Soy emocionalmente estable y equilibrado/a.',                          'neuroticism', true],
            ['Me irrito o enojo con relativa facilidad.',                            'neuroticism', false],
            ['En general me siento relajado/a y tranquilo/a.',                      'neuroticism', true],
            ['Los cambios inesperados me generan angustia o malestar.',              'neuroticism', false],
            ['Rara vez siento ansiedad o nerviosismo intenso.',                      'neuroticism', true],
            // Apertura (O)
            ['Tengo una imaginación muy activa y creativa.',                         'openness', false],
            ['Me cuesta entender conceptos abstractos o filosóficos.',               'openness', true],
            ['Disfruto debatir ideas complejas e innovadoras.',                      'openness', false],
            ['Prefiero lo concreto y práctico sobre lo teórico y abstracto.',        'openness', true],
            ['Con frecuencia genero ideas originales y poco convencionales.',        'openness', false],
            ['Rara vez fantaseo o imagino escenarios alternativos.',                 'openness', true],
            ['Aprendo y comprendo nuevas ideas con rapidez.',                        'openness', false],
            ['Evito pensar en temas filosóficos o de profundidad intelectual.',      'openness', true],
            ['Disfruto la complejidad y la profundidad en los problemas.',           'openness', false],
            ['Prefiero la rutina y la certeza por encima de lo nuevo e incierto.',   'openness', true],
        ];

        foreach ($items as $order => [$text, $dimension, $reverse]) {
            $question = Question::create([
                'test_id'        => $test->id,
                'text'           => $text,
                'type'           => 'likert',
                'points'         => 5,
                'order'          => $order + 1,
                'is_required'    => true,
                'dimension'      => $dimension,
                'reverse_scored' => $reverse,
            ]);

            foreach ($likertOptions as $i => $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'text'        => $opt['text'],
                    'value'       => $opt['value'],
                    'is_correct'  => false,
                    'order'       => $i + 1,
                ]);
            }
        }
    }

    // ══════════════════════════════════════════════════════════════════════
    // 2. 16PF SIMPLIFICADO — 32 ítems (2 por factor)
    // ══════════════════════════════════════════════════════════════════════
    private function seedSixteenPF(): void
    {
        $test = Test::firstOrCreate(
            ['test_type' => '16pf'],
            [
                'name'           => '16PF — 16 Factores de Personalidad (Simplificado)',
                'description'    => 'Versión abreviada del cuestionario 16PF de Cattell. 32 ítems de elección múltiple (a/b/c).',
                'instructions'   => 'Lee cada enunciado y elige la opción que mejor te describe. Responde con sinceridad. Evita la opción "b" (intermedia) a menos que realmente no puedas elegir entre las otras dos.',
                'module'         => 'personalidad',
                'test_type'      => '16pf',
                'evaluator_scored' => false,
                'scoring_method' => 'dimensional',
                'time_limit'     => 25,
                'passing_score'  => 0,
                'is_active'      => true,
                'created_by'     => $this->adminId,
            ]
        );

        if ($test->questions()->exists()) return;

        // [texto, opciones[a,b,c], valores[a,b,c], dimension]
        $items = [
            // A – Afabilidad (Calidez)
            ['Al trabajar con otras personas, prefiero…',
             ['Colaborar en equipo y apoyar a mis colegas', 'Depende de la situación', 'Trabajar de forma independiente'],
             [2, 1, 0], 'factor_A'],
            ['Con personas que acabo de conocer, usualmente…',
             ['Me acerco con facilidad y busco conocerlas', 'Depende de quién sea', 'Espero a que ellas tomen la iniciativa'],
             [2, 1, 0], 'factor_A'],
            // B – Razonamiento
            ['Ante un problema complejo, tiendo a…',
             ['Analizarlo desde varios ángulos antes de decidir', 'Buscar ayuda o información adicional', 'Elegir la primera solución que parece funcionar'],
             [2, 1, 0], 'factor_B'],
            ['Cuando aprendo algo nuevo, prefiero…',
             ['Entender los principios subyacentes', 'Mezclar teoría con práctica', 'Aprender haciendo directamente'],
             [2, 1, 0], 'factor_B'],
            // C – Estabilidad emocional
            ['Ante contratiempos inesperados, generalmente…',
             ['Me recupero rápido y busco soluciones', 'Me afecta un poco pero sigo adelante', 'Me cuesta recuperarme emocionalmente'],
             [2, 1, 0], 'factor_C'],
            ['Cuando tengo muchas responsabilidades al mismo tiempo…',
             ['Las organizo y las manejo con calma', 'Me estreso algo pero funciono', 'Me siento abrumado/a con facilidad'],
             [2, 1, 0], 'factor_C'],
            // E – Dominancia
            ['En situaciones de desacuerdo, suelo…',
             ['Defender mi punto de vista con firmeza', 'Buscar un punto intermedio', 'Ceder para evitar conflictos'],
             [2, 1, 0], 'factor_E'],
            ['Al liderar un grupo, me resulta natural…',
             ['Tomar decisiones y marcar el rumbo', 'Consultar a los demás antes de decidir', 'Seguir las decisiones del grupo'],
             [2, 1, 0], 'factor_E'],
            // F – Animación
            ['En una reunión social, tiendo a…',
             ['Ser entusiasta y animar el ambiente', 'Participar según el momento', 'Mantenerme tranquilo/a y discreto/a'],
             [2, 1, 0], 'factor_F'],
            ['Al afrontar tareas nuevas, normalmente siento…',
             ['Entusiasmo y ganas de empezar', 'Algo de motivación', 'Cautela y preferencia por lo conocido'],
             [2, 1, 0], 'factor_F'],
            // G – Atención a normas
            ['Respecto a las reglas y normas establecidas, yo…',
             ['Las sigo cuidadosamente aunque no entienda el motivo', 'Las sigo cuando tienen sentido', 'Cuestiono las que me parecen innecesarias'],
             [2, 1, 0], 'factor_G'],
            ['Cuando cometo un error, generalmente…',
             ['Me esfuerzo por corregirlo y evitar que se repita', 'Lo corrijo si es importante', 'Tiendo a minimizarlo'],
             [2, 1, 0], 'factor_G'],
            // H – Atrevimiento social
            ['Al hablar en público, me siento…',
             ['Cómodo/a y confiado/a', 'Algo nervioso/a pero puedo manejarlo', 'Muy nervioso/a e inseguro/a'],
             [2, 1, 0], 'factor_H'],
            ['En situaciones de riesgo social (evaluar o ser evaluado), yo…',
             ['Me siento seguro/a de mí mismo/a', 'Un poco ansioso/a', 'Muy incómodo/a'],
             [2, 1, 0], 'factor_H'],
            // I – Sensibilidad
            ['Ante el sufrimiento ajeno, generalmente…',
             ['Me conmuevo profundamente y busco ayudar', 'Me preocupo pero mantengo distancia', 'Prefiero no involucrarme emocionalmente'],
             [2, 1, 0], 'factor_I'],
            ['Al tomar decisiones importantes, me guío más por…',
             ['Mis valores y sentimientos', 'Una mezcla de razón y emoción', 'La lógica y los datos objetivos'],
             [2, 1, 0], 'factor_I'],
            // L – Vigilancia
            ['En el entorno laboral, mi actitud es…',
             ['Confiar en los demás hasta que demuestren lo contrario', 'Ser cauto/a con gente que no conozco bien', 'Ser muy precavido/a con la mayoría de personas'],
             [2, 1, 0], 'factor_L'],
            ['Cuando algo sale mal en el trabajo, pienso que…',
             ['Puede deberse a causas diversas, no siempre a personas', 'A veces hay responsables individuales', 'Generalmente hay alguien que no cumplió'],
             [2, 1, 0], 'factor_L'],
            // M – Abstracción
            ['En mis horas libres, prefiero…',
             ['Reflexionar sobre ideas, leer o crear', 'Mezclar actividades intelectuales y prácticas', 'Actividades concretas y físicas'],
             [2, 1, 0], 'factor_M'],
            ['Al resolver problemas, me enfoco más en…',
             ['El panorama general y las implicaciones futuras', 'Una combinación de detalles y visión global', 'Los detalles prácticos del momento'],
             [2, 1, 0], 'factor_M'],
            // N – Privacidad
            ['Con personas que no conozco mucho, comparto…',
             ['Solo lo estrictamente necesario', 'Información general sin entrar en detalles', 'Abiertamente mis experiencias y pensamientos'],
             [2, 1, 0], 'factor_N'],
            ['En conversaciones grupales, suelo…',
             ['Guardar mis opiniones personales para mí', 'Compartirlas selectivamente', 'Expresar mis puntos de vista libremente'],
             [2, 1, 0], 'factor_N'],
            // O – Aprensión
            ['Sobre mi desempeño en el trabajo, siento…',
             ['Con frecuencia preocupación por si lo hago bien', 'Algo de incertidumbre ocasional', 'Generalmente seguridad y confianza'],
             [2, 1, 0], 'factor_O'],
            ['Cuando recibo críticas, mi primera reacción es…',
             ['Sentirme inseguro/a y cuestionarme', 'Analizarla objetivamente con algo de incomodidad', 'Evaluarla sin que afecte mi autoestima'],
             [2, 1, 0], 'factor_O'],
            // Q1 – Apertura al cambio
            ['Ante cambios organizacionales, mi reacción típica es…',
             ['Verlos como oportunidades y adaptarme rápido', 'Adaptarme aunque me cueste un poco', 'Preferir las rutinas establecidas'],
             [2, 1, 0], 'factor_Q1'],
            ['Respecto a las ideas tradicionales, yo…',
             ['Cuestiono lo establecido y busco nuevas perspectivas', 'Mezclo lo nuevo con lo que funciona', 'Valoro lo tradicional y probado'],
             [2, 1, 0], 'factor_Q1'],
            // Q2 – Autosuficiencia
            ['Al tomar decisiones, prefiero…',
             ['Reflexionar y decidir por mí mismo/a', 'Consultar y luego decidir', 'Buscar consenso con otros antes de actuar'],
             [2, 1, 0], 'factor_Q2'],
            ['Trabajar solo/a o en grupo, me resulta más productivo…',
             ['Solo/a, con total autonomía', 'Depende de la tarea', 'En grupo, con respaldo del equipo'],
             [2, 1, 0], 'factor_Q2'],
            // Q3 – Perfeccionismo / Autocontrol
            ['Respecto a mis objetivos y estándares, yo…',
             ['Me exijo cumplir mis metas con alta precisión', 'Me esfuerzo pero acepto resultados razonables', 'Me conformo con lo suficientemente bueno'],
             [2, 1, 0], 'factor_Q3'],
            ['Mi nivel de organización personal es…',
             ['Muy ordenado/a y metódico/a', 'Organizado/a en lo esencial', 'Bastante flexible e informal'],
             [2, 1, 0], 'factor_Q3'],
            // Q4 – Tensión
            ['En términos generales, me siento…',
             ['Con frecuencia tenso/a o con energía reprimida', 'A veces tenso/a según la situación', 'Generalmente relajado/a y sin tensión acumulada'],
             [2, 1, 0], 'factor_Q4'],
            ['Cuando no puedo controlar los resultados de algo importante, siento…',
             ['Mucha ansiedad e inquietud', 'Algo de preocupación', 'Relativa tranquilidad y aceptación'],
             [2, 1, 0], 'factor_Q4'],
        ];

        $factorNames = [
            'factor_A' => 'Afabilidad', 'factor_B' => 'Razonamiento',
            'factor_C' => 'Estabilidad emocional', 'factor_E' => 'Dominancia',
            'factor_F' => 'Animación', 'factor_G' => 'Atención a normas',
            'factor_H' => 'Atrevimiento social', 'factor_I' => 'Sensibilidad',
            'factor_L' => 'Vigilancia', 'factor_M' => 'Abstracción',
            'factor_N' => 'Privacidad', 'factor_O' => 'Aprensión',
            'factor_Q1'=> 'Apertura al cambio', 'factor_Q2'=> 'Autosuficiencia',
            'factor_Q3'=> 'Perfeccionismo', 'factor_Q4'=> 'Tensión',
        ];

        foreach ($items as $order => [$text, $options, $values, $dimension]) {
            $question = Question::create([
                'test_id'     => $test->id,
                'text'        => $text,
                'type'        => 'multiple_choice',
                'points'      => 2,
                'order'       => $order + 1,
                'is_required' => true,
                'dimension'   => $dimension,
            ]);

            foreach ($options as $i => $optText) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'text'        => $optText,
                    'value'       => $values[$i],
                    'is_correct'  => $values[$i] === 2,
                    'order'       => $i + 1,
                ]);
            }
        }
    }

    // ══════════════════════════════════════════════════════════════════════
    // 3. MATRICES PROGRESIVAS DE RAVEN — 36 ítems (Sets A–D, 9 c/u)
    // ══════════════════════════════════════════════════════════════════════
    private function seedRaven(): void
    {
        $test = Test::firstOrCreate(
            ['test_type' => 'raven'],
            [
                'name'           => 'Matrices Progresivas de Raven',
                'description'    => 'Prueba de inteligencia no verbal. Mide capacidad de razonamiento abstracto y lógico. 36 matrices de dificultad progresiva (Sets A, B, C, D).',
                'instructions'   => 'En cada ejercicio verás una matriz con una pieza faltante. Debajo encontrarás 6 opciones numeradas. Selecciona la opción que completa correctamente el patrón. Trabaja con calma; si una respuesta te cuesta, continúa y regresa después.',
                'module'         => 'cognitivo',
                'test_type'      => 'raven',
                'evaluator_scored' => false,
                'scoring_method' => 'dimensional',
                'time_limit'     => 45,
                'passing_score'  => 50,
                'is_active'      => true,
                'created_by'     => $this->adminId,
            ]
        );

        if ($test->questions()->exists()) return;

        // Estructura: [texto descriptivo, set/categoria, opcion_correcta(1-6), descripcion_opciones]
        // En producción, image_path apuntaría a /storage/tests/raven/set_X_item_Y.png
        $sets = [
            'set_a' => [
                ['Completa el patrón: figuras que aumentan de tamaño de izquierda a derecha. Fila inferior.',         3],
                ['Patrón de sombras: la figura va oscureciendo en diagonal. Selecciona la pieza faltante.',           5],
                ['Patrón de líneas horizontales que se cruzan con verticales. ¿Cuál continúa la secuencia?',          2],
                ['Serie de formas que rotan 45° en cada casilla. Identifica la pieza de la esquina inferior derecha.',1],
                ['Las figuras cambian de sólido a hueco siguiendo un patrón. ¿Cuál completa la matriz?',              4],
                ['Patrón con puntos: cada fila suma el mismo número de puntos. Selecciona la pieza correcta.',        6],
                ['Las formas internas varían en número de acuerdo a una regla. ¿Cuál es la pieza faltante?',          2],
                ['Patrón de ángulos que giran progresivamente. Completa la secuencia.',                               5],
                ['Combinación de dos atributos (tamaño y relleno) en progresión sistemática. ¿Cuál falta?',          3],
            ],
            'set_b' => [
                ['Las figuras en cada fila se combinan para formar la tercera. Selecciona la pieza faltante.',        4],
                ['Cada fila contiene tres figuras que, al superponerse, forman la última. Elige la correcta.',        1],
                ['Patrón de analogía: la relación entre la primera y segunda figura es igual a la de la tercera y…', 6],
                ['Las líneas internas se suman entre columnas para producir la figura central.',                      3],
                ['Aplica la regla: el total de lados en cada fila es constante. Completa la serie.',                  2],
                ['Identifica la figura que completa la secuencia: cada elemento añade un rasgo adicional.',           5],
                ['Las formas se fusionan de fila en fila siguiendo una ley de adición. ¿Cuál falta?',                1],
                ['Patrón de simetría: cada columna tiene simetría vertical. Selecciona la pieza correcta.',          4],
                ['Regla de diferencia: las figuras de cada fila difieren en un único atributo. Completa.',            6],
            ],
            'set_c' => [
                ['Las figuras evolucionan según dos reglas simultáneas (forma y relleno). ¿Cuál completa?',           2],
                ['Cada celda es resultado de combinar la fila anterior con la columna izquierda.',                    5],
                ['Los elementos de la matriz aumentan en complejidad. Identifica la pieza final.',                    3],
                ['Patrón de rotación + reflejo progresivo en 3×3. Selecciona la pieza de la posición [3,3].',        1],
                ['Las figuras eliminan atributos compartidos por dos de ellas (XOR visual). Completa.',               6],
                ['Cada fila aplica una transformación diferente (rotar, reflejar, invertir). Identifica cuál.',       4],
                ['Patrón de degradación progresiva: elementos desaparecen según una secuencia. ¿Cuál falta?',        2],
                ['La tercera figura de cada fila es la superposición exclusiva de las dos primeras.',                 5],
                ['Combina las reglas de las filas y columnas para encontrar la pieza de intersección.',               3],
            ],
            'set_d' => [
                ['Las figuras cambian de posición dentro de la celda siguiendo un patrón de traslación.',             4],
                ['En cada fila hay exactamente 3 atributos distintos distribuidos. Completa la matriz.',              6],
                ['Las formas giran 90° en sentido horario en cada columna. Identifica la pieza [3,3].',               1],
                ['Patrón numérico codificado visualmente: las puntas de las figuras siguen la serie 2-4-6.',          3],
                ['Cada fila contiene exactamente los mismos elementos en diferente orden. ¿Cuál falta?',              5],
                ['Las figuras se construyen por adición de partes. La tercera tiene todo lo de las dos primeras.',    2],
                ['Aplica dos reglas: simetría y complementariedad de relleno. Selecciona la pieza correcta.',         4],
                ['Regla: si los elementos son iguales en fila y columna, la celda está vacía. De lo contrario, tiene forma.', 6],
                ['Patrón de inversión + rotación doble. Es el ítem de mayor dificultad del set.',                     1],
            ],
        ];

        $globalOrder = 1;
        foreach ($sets as $setKey => $items) {
            foreach ($items as $idx => [$text, $correctPos]) {
                $question = Question::create([
                    'test_id'     => $test->id,
                    'text'        => "Set " . strtoupper(substr($setKey, -1)) . " — Ítem " . ($idx + 1) . ": {$text}",
                    'type'        => 'multiple_choice',
                    'points'      => 1,
                    'order'       => $globalOrder++,
                    'is_required' => true,
                    'dimension'   => 'raven_total',
                    'category'    => $setKey,
                    'image_path'  => "raven/{$setKey}_item_" . ($idx + 1) . ".png",
                ]);

                for ($opt = 1; $opt <= 6; $opt++) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'text'        => "Opción {$opt}",
                        'value'       => $opt === $correctPos ? 1 : 0,
                        'is_correct'  => $opt === $correctPos,
                        'order'       => $opt,
                    ]);
                }
            }
        }
    }

    // ══════════════════════════════════════════════════════════════════════
    // 4. ASSESSMENT CENTER — 5 escenarios escritos
    // ══════════════════════════════════════════════════════════════════════
    private function seedAssessmentCenter(): void
    {
        $test = Test::firstOrCreate(
            ['test_type' => 'assessment_center'],
            [
                'name'           => 'Assessment Center — Evaluación de Competencias Laborales',
                'description'    => 'Simulación de situaciones laborales reales. El candidato responde por escrito; el evaluador califica las competencias demostradas.',
                'instructions'   => 'A continuación se presentan situaciones laborales reales. Lee cada escenario con atención y responde de manera detallada explicando qué harías, por qué y cómo. No hay respuestas únicas correctas; se evalúa tu capacidad de análisis, criterio y orientación a resultados.',
                'module'         => 'competencias',
                'test_type'      => 'assessment_center',
                'evaluator_scored' => false,
                'scoring_method' => 'dimensional',
                'time_limit'     => 60,
                'passing_score'  => 60,
                'is_active'      => true,
                'created_by'     => $this->adminId,
            ]
        );

        if ($test->questions()->exists()) return;

        $scenarios = [
            [
                'dimension' => 'liderazgo',
                'text'      => "ESCENARIO 1 — Liderazgo bajo presión\n\nEres el/la responsable de un equipo de 6 personas que debe entregar un proyecto importante en 48 horas. A 24 horas del plazo, dos miembros clave del equipo han tenido un conflicto personal que está afectando el trabajo de todos. El ambiente es tenso, la productividad ha bajado y el plazo está en riesgo.\n\n¿Qué harías concretamente? Describe paso a paso tus acciones, priorizando tanto la entrega como el bienestar del equipo.",
            ],
            [
                'dimension' => 'trabajo_equipo',
                'text'      => "ESCENARIO 2 — Trabajo en equipo y colaboración\n\nFormas parte de un comité interdisciplinario para rediseñar un proceso de atención al cliente. Tu equipo tiene diferentes criterios sobre la solución: algunos priorizan la eficiencia operativa, otros la experiencia del usuario. Las discusiones han llegado a un punto muerto después de dos reuniones sin acuerdo.\n\n¿Cómo contribuirías para destrabar la situación y llegar a una propuesta concreta? ¿Qué rol asumirías?",
            ],
            [
                'dimension' => 'orientacion_cliente',
                'text'      => "ESCENARIO 3 — Orientación al cliente\n\nUn cliente importante llama furioso porque recibió su pedido incompleto por tercera vez consecutiva. Exige hablar con el gerente y amenaza con cancelar su contrato anual. Tú eres el/la ejecutivo/a de cuenta a cargo y el gerente está en una reunión inaccesible.\n\n¿Cómo manejarías esta situación? ¿Qué le dirías al cliente? ¿Qué acciones tomarías de inmediato y cuáles a mediano plazo?",
            ],
            [
                'dimension' => 'toma_decisiones',
                'text'      => "ESCENARIO 4 — Toma de decisiones bajo incertidumbre\n\nDebes aprobar el lanzamiento de un nuevo servicio. Cuentas con datos positivos de un grupo piloto pequeño, pero el mercado objetivo es diez veces más grande. Tu director exige una decisión en 2 horas. El equipo de desarrollo dice que está listo; el de marketing pide más tiempo para validar. La empresa necesita ingresos este trimestre.\n\n¿Qué factores considerarías para decidir? ¿Lanzarías el servicio, lo pospondrías, o tomarías una tercera opción? Justifica tu respuesta.",
            ],
            [
                'dimension' => 'adaptabilidad',
                'text'      => "ESCENARIO 5 — Adaptabilidad al cambio\n\nLa empresa acaba de anunciar que tu área migrará a un sistema de gestión completamente nuevo en 30 días. Esto implica aprender una nueva plataforma, redefinir flujos de trabajo y gestionar la resistencia de tu equipo (algunos llevan más de 10 años con el sistema actual). Adicionalmente, debes mantener los indicadores de desempeño habituales durante la transición.\n\n¿Cómo gestionarías esta transición? ¿Qué harías para que tú y tu equipo se adapten con éxito?",
            ],
        ];

        foreach ($scenarios as $order => $item) {
            Question::create([
                'test_id'     => $test->id,
                'text'        => $item['text'],
                'type'        => 'open',
                'points'      => 20,
                'order'       => $order + 1,
                'is_required' => true,
                'dimension'   => $item['dimension'],
            ]);
        }
    }

    // ══════════════════════════════════════════════════════════════════════
    // 5. WARTEGG — Plantilla para evaluador (8 indicadores)
    // ══════════════════════════════════════════════════════════════════════
    private function seedWartegg(): void
    {
        Test::firstOrCreate(
            ['test_type' => 'wartegg'],
            [
                'name'           => 'Test de Wartegg — Evaluación Proyectiva',
                'description'    => 'Prueba proyectiva gráfica (Wartegg Drawing Completion Test). El candidato completa 8 campos con dibujos; el psicólogo evalúa los indicadores.',
                'instructions'   => 'PRUEBA ADMINISTRADA POR EL PSICÓLOGO. El candidato completa a mano los 8 campos del cuadernillo Wartegg. Esta vista es para el registro de resultados por parte del evaluador.',
                'module'         => 'proyectivo',
                'test_type'      => 'wartegg',
                'evaluator_scored' => true,
                'scoring_method' => 'evaluator',
                'time_limit'     => null,
                'passing_score'  => 0,
                'is_active'      => true,
                'created_by'     => $this->adminId,
            ]
        );
        // El Wartegg no tiene preguntas en el sistema (es evaluación del psicólogo)
    }

    // ══════════════════════════════════════════════════════════════════════
    // 6. ENTREVISTA STAR — 15 preguntas estructuradas por competencias
    // ══════════════════════════════════════════════════════════════════════
    private function seedStarInterview(): void
    {
        Test::firstOrCreate(
            ['test_type' => 'star_interview'],
            [
                'name'           => 'Entrevista Estructurada por Competencias (Método STAR)',
                'description'    => 'Entrevista psicológica obligatoria. 15 preguntas conductuales basadas en el método STAR (Situación, Tarea, Acción, Resultado). Calificada por el evaluador.',
                'instructions'   => 'ENTREVISTA CONDUCIDA POR EL PSICÓLOGO. Esta prueba es de carácter obligatorio para todos los candidatos. El evaluador registra el desempeño del candidato ante cada pregunta STAR usando la rúbrica de competencias.',
                'module'         => 'entrevista',
                'test_type'      => 'star_interview',
                'evaluator_scored' => true,
                'scoring_method' => 'evaluator',
                'time_limit'     => null,
                'passing_score'  => 60,
                'is_active'      => true,
                'created_by'     => $this->adminId,
            ]
        );
        // La entrevista STAR tampoco tiene preguntas en el sistema de candidatos
    }
}
