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
    // 1. BIG FIVE — IB5-SL v1.0 (Instrumento de Selección Laboral)
    //    50 ítems Likert · 5 dimensiones OCEAN · 10 ítems por dimensión
    //    (5 directos + 5 invertidos por dimensión)
    //    Referencia: Big5_Test_Seleccion_Laboral_1.md
    // ══════════════════════════════════════════════════════════════════════
    private function seedBigFive(): void
    {
        $test = Test::firstOrCreate(
            ['test_type' => 'big_five'],
            [
                'name'           => 'Big Five — Evaluación de Personalidad (IPIP-50)',
                'description'    => 'Instrumento de evaluación de personalidad laboral basado en el Modelo de los Cinco Grandes Rasgos (OCEAN). 50 ítems tipo Likert adaptados para contextos de selección de personal.',
                'instructions'   => "A continuación encontrará una serie de afirmaciones relacionadas con su forma habitual de pensar, sentir y actuar en contextos laborales y cotidianos.\n\nResponda con honestidad. No hay respuestas correctas ni incorrectas. Lo que importa es que sus respuestas reflejen cómo es usted realmente, no cómo le gustaría ser.\n\nUse la siguiente escala:\n1 = Totalmente en desacuerdo\n2 = En desacuerdo\n3 = Neutral / No sé\n4 = De acuerdo\n5 = Totalmente de acuerdo",
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

        // Si ya existen preguntas, las elimina para re-sembrar con el instrumento actualizado
        if ($test->questions()->exists()) {
            $test->questions()->each(function ($q) {
                $q->options()->delete();
                $q->delete();
            });
        }

        // Escala Likert según el instrumento IB5-SL
        $likertOptions = [
            ['text' => 'Totalmente en desacuerdo', 'value' => 1],
            ['text' => 'En desacuerdo',             'value' => 2],
            ['text' => 'Neutral / No sé',           'value' => 3],
            ['text' => 'De acuerdo',                'value' => 4],
            ['text' => 'Totalmente de acuerdo',     'value' => 5],
        ];

        // [texto del ítem, dimensión, reverse_scored]
        // Orden exacto del instrumento IB5-SL:
        //   Ítems  1-10 → Apertura a la Experiencia (O)
        //   Ítems 11-20 → Responsabilidad / Conciencia (C)
        //   Ítems 21-30 → Extraversión (E)
        //   Ítems 31-40 → Amabilidad (A)
        //   Ítems 41-50 → Neuroticismo (N)
        $items = [
            // ── DIMENSIÓN 1: APERTURA A LA EXPERIENCIA (O) ──────────────
            ['Me gusta explorar ideas nuevas y poco convencionales en mi trabajo.',                              'openness', false],
            ['Disfruto aprender sobre temas o áreas que desconozco.',                                           'openness', false],
            ['Tengo una imaginación activa que suelo aplicar en la resolución de problemas.',                   'openness', false],
            ['Me atrae trabajar en proyectos que requieren pensar de manera innovadora.',                       'openness', false],
            ['Me interesa comprender puntos de vista distintos al mío, aunque no los comparta.',               'openness', false],
            ['Prefiero hacer las cosas de la manera en que siempre se han hecho.',                             'openness', true],
            ['No me resulta atractivo involucrarme en actividades que exijan mucha creatividad.',               'openness', true],
            ['Me cuesta adaptarme cuando cambian significativamente los métodos de trabajo.',                   'openness', true],
            ['Prefiero tareas rutinarias a aquellas que implican cambios o improvisación.',                     'openness', true],
            ['Rara vez cuestiono las normas o procedimientos establecidos en mi entorno laboral.',              'openness', true],

            // ── DIMENSIÓN 2: RESPONSABILIDAD / CONCIENCIA (C) ───────────
            ['Cumplo mis compromisos laborales dentro de los plazos establecidos.',                             'conscientiousness', false],
            ['Soy una persona organizada y metódica al abordar mis tareas.',                                   'conscientiousness', false],
            ['Me preparo con anticipación antes de emprender una tarea importante.',                            'conscientiousness', false],
            ['Reviso mi trabajo con cuidado antes de entregarlo.',                                              'conscientiousness', false],
            ['Mantengo mis responsabilidades y espacios de trabajo bien estructurados.',                        'conscientiousness', false],
            ['Con frecuencia olvido responder mensajes o compromisos laborales pendientes.',                    'conscientiousness', true],
            ['Me cuesta mantener el orden en mis responsabilidades cuando hay mucha carga.',                    'conscientiousness', true],
            ['Suelo postergar tareas que podría resolver en el momento.',                                       'conscientiousness', true],
            ['En ocasiones entrego trabajos sin haberlos revisado lo suficiente.',                              'conscientiousness', true],
            ['Me resulta difícil seguir un plan de trabajo cuando las circunstancias se complican.',            'conscientiousness', true],

            // ── DIMENSIÓN 3: EXTRAVERSIÓN (E) ────────────────────────────
            ['Me siento cómodo/a siendo protagonista o vocero/a en reuniones de trabajo.',                     'extraversion', false],
            ['Disfruto trabajar en equipo y relacionarme activamente con mis colegas.',                         'extraversion', false],
            ['Tomo la iniciativa para iniciar conversaciones con personas que no conozco.',                     'extraversion', false],
            ['Me energiza participar en actividades grupales dentro del entorno laboral.',                      'extraversion', false],
            ['Me expreso con facilidad y seguridad frente a grupos de personas.',                               'extraversion', false],
            ['Prefiero trabajar de forma independiente antes que en equipo.',                                   'extraversion', true],
            ['Las interacciones sociales intensas en el trabajo me generan desgaste.',                          'extraversion', true],
            ['Tiendo a hablar poco en reuniones cuando no conozco bien a los participantes.',                   'extraversion', true],
            ['Prefiero comunicarme por escrito antes que hablar en persona o en público.',                      'extraversion', true],
            ['Me resulta difícil mostrar entusiasmo o energía frente a otros en el trabajo.',                   'extraversion', true],

            // ── DIMENSIÓN 4: AMABILIDAD (A) ──────────────────────────────
            ['Me interesa genuinamente el bienestar de mis compañeros de trabajo.',                             'agreeableness', false],
            ['Estoy dispuesto/a a ceder en mis posiciones cuando hay un conflicto de equipo.',                  'agreeableness', false],
            ['Trato de ser cordial y respetuoso/a con todas las personas de mi entorno laboral.',               'agreeableness', false],
            ['Cuando un colega tiene dificultades, trato de apoyarlo sin necesidad de que me lo pida.',         'agreeableness', false],
            ['Confío en la buena intención de las personas con quienes trabajo habitualmente.',                  'agreeableness', false],
            ['Me cuesta colaborar con personas que tienen estilos de trabajo muy distintos al mío.',            'agreeableness', true],
            ['En ocasiones actúo de manera fría o distante con mis colegas.',                                   'agreeableness', true],
            ['Cuando hay conflictos, tiendo a priorizar mis intereses antes que los del equipo.',               'agreeableness', true],
            ['A veces soy tan directo/a que puedo afectar negativamente los sentimientos de otros.',            'agreeableness', true],
            ['Me resulta difícil confiar en las intenciones de mis compañeros de trabajo.',                     'agreeableness', true],

            // ── DIMENSIÓN 5: NEUROTICISMO / INESTABILIDAD EMOCIONAL (N) ──
            ['Me pongo ansioso/a con facilidad cuando tengo demasiadas responsabilidades a la vez.',            'neuroticism', false],
            ['Los cambios inesperados en el trabajo me generan un nivel significativo de estrés.',              'neuroticism', false],
            ['Me preocupo constantemente por la posibilidad de cometer errores en mi trabajo.',                 'neuroticism', false],
            ['Me cuesta recuperarme emocionalmente cuando recibo críticas o retroalimentación negativa.',       'neuroticism', false],
            ['Siento que mis estados emocionales interfieren con mi rendimiento laboral.',                      'neuroticism', false],
            ['Me mantengo tranquilo/a incluso en situaciones de alta presión o exigencia.',                     'neuroticism', true],
            ['Generalmente mantengo el control emocional cuando surgen problemas inesperados en el trabajo.',   'neuroticism', true],
            ['Los fracasos o contratiempos laborales no me afectan emocionalmente de forma prolongada.',        'neuroticism', true],
            ['Puedo tomar decisiones con calma incluso cuando estoy bajo presión.',                             'neuroticism', true],
            ['Recupero mi equilibrio emocional con rapidez luego de situaciones laborales difíciles.',          'neuroticism', true],
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
    // 2. 16PF-SL v1.0 — 64 ítems Likert (4 por factor × 16 factores)
    //    2 directos + 2 invertidos por factor
    //    Referencia: 16PF_SL_Instrumento_Seleccion_Laboral.md
    // ══════════════════════════════════════════════════════════════════════
    private function seedSixteenPF(): void
    {
        $test = Test::firstOrCreate(
            ['test_type' => '16pf'],
            [
                'name'           => '16PF — 16 Factores de Personalidad (Selección Laboral)',
                'description'    => 'Instrumento 16PF-SL basado en el modelo de Raymond B. Cattell. 64 ítems tipo Likert que evalúan 16 factores de personalidad relevantes para el desempeño laboral.',
                'instructions'   => "A continuación encontrará una serie de afirmaciones relacionadas con su forma habitual de pensar, sentir y actuar en contextos laborales y cotidianos.\n\nResponda con honestidad — no hay respuestas correctas ni incorrectas. Lo que importa es que sus respuestas reflejen cómo es usted realmente, no cómo le gustaría ser.\n\nUse la siguiente escala:\n1 = Totalmente en desacuerdo\n2 = En desacuerdo\n3 = Neutral / No sé\n4 = De acuerdo\n5 = Totalmente de acuerdo",
                'module'         => 'personalidad',
                'test_type'      => '16pf',
                'evaluator_scored' => false,
                'scoring_method' => 'dimensional',
                'time_limit'     => 30,
                'passing_score'  => 0,
                'is_active'      => true,
                'created_by'     => $this->adminId,
            ]
        );

        // Elimina preguntas existentes para re-sembrar con el instrumento actualizado
        if ($test->questions()->exists()) {
            $test->questions()->each(function ($q) {
                $q->options()->delete();
                $q->delete();
            });
        }

        // Escala Likert según instrumento 16PF-SL
        $likertOptions = [
            ['text' => 'Totalmente en desacuerdo', 'value' => 1],
            ['text' => 'En desacuerdo',             'value' => 2],
            ['text' => 'Neutral / No sé',           'value' => 3],
            ['text' => 'De acuerdo',                'value' => 4],
            ['text' => 'Totalmente de acuerdo',     'value' => 5],
        ];

        // [texto del ítem, factor/dimensión, reverse_scored]
        // Orden exacto del instrumento 16PF-SL (ítems 1-64):
        //   Ítems  1- 4 → Factor A  (Calidez)
        //   Ítems  5- 8 → Factor B  (Razonamiento)
        //   Ítems  9-12 → Factor C  (Estabilidad Emocional)
        //   Ítems 13-16 → Factor E  (Dominancia)
        //   Ítems 17-20 → Factor F  (Animación)
        //   Ítems 21-24 → Factor G  (Atención a las Normas)
        //   Ítems 25-28 → Factor H  (Atrevimiento Social)
        //   Ítems 29-32 → Factor I  (Sensibilidad)
        //   Ítems 33-36 → Factor L  (Vigilancia)
        //   Ítems 37-40 → Factor M  (Abstracción)
        //   Ítems 41-44 → Factor N  (Privacidad)
        //   Ítems 45-48 → Factor O  (Aprensión)
        //   Ítems 49-52 → Factor Q1 (Apertura al Cambio)
        //   Ítems 53-56 → Factor Q2 (Autosuficiencia)
        //   Ítems 57-60 → Factor Q3 (Perfeccionismo)
        //   Ítems 61-64 → Factor Q4 (Tensión)
        $items = [
            // ── FACTOR A — Calidez / Afecto (Warmth) ─────────────────────
            ['Me resulta fácil relacionarme de manera cálida y cercana con las personas en mi entorno laboral.',          'factor_a', false],
            ['Disfruto generar un ambiente amigable y de apoyo mutuo con mis compañeros de trabajo.',                     'factor_a', false],
            ['Prefiero mantener distancia emocional con mis colegas para preservar la objetividad profesional.',          'factor_a', true],
            ['No considero necesario establecer vínculos personales estrechos dentro del trabajo.',                       'factor_a', true],

            // ── FACTOR B — Razonamiento (Reasoning) ──────────────────────
            ['Me resulta fácil comprender conceptos complejos o abstractos con rapidez.',                                 'factor_b', false],
            ['Aprendo nuevas habilidades y procedimientos de trabajo con facilidad.',                                     'factor_b', false],
            ['Prefiero instrucciones concretas y detalladas antes de comenzar una tarea nueva.',                          'factor_b', true],
            ['Me cuesta seguir razonamientos muy abstractos o teóricos en el contexto laboral.',                          'factor_b', true],

            // ── FACTOR C — Estabilidad Emocional (Emotional Stability) ───
            ['Me mantengo emocionalmente estable incluso cuando enfrento situaciones difíciles en el trabajo.',           'factor_c', false],
            ['Generalmente soy capaz de controlar mis emociones cuando surgen problemas laborales.',                      'factor_c', false],
            ['Con frecuencia me siento perturbado/a por situaciones que otros consideran menores.',                       'factor_c', true],
            ['Cuando algo sale mal en el trabajo, me cuesta recuperar la calma con rapidez.',                             'factor_c', true],

            // ── FACTOR E — Dominancia / Asertividad (Dominance) ──────────
            ['En situaciones grupales, tiendo a tomar el liderazgo y orientar al equipo.',                                'factor_e', false],
            ['Defiendo mis puntos de vista con firmeza, incluso cuando hay oposición.',                                   'factor_e', false],
            ['Prefiero seguir las instrucciones de otros antes que imponer mi propio criterio.',                          'factor_e', true],
            ['Me resulta difícil confrontar a otros cuando no estoy de acuerdo con sus decisiones.',                      'factor_e', true],

            // ── FACTOR F — Animación / Vivacidad (Liveliness) ────────────
            ['Las personas de mi entorno me describen como alguien entusiasta y lleno/a de energía.',                    'factor_f', false],
            ['Me gusta agregar dinamismo y buen ánimo a los ambientes de trabajo.',                                       'factor_f', false],
            ['Prefiero un estilo de trabajo serio y formal antes que uno desenfadado o espontáneo.',                      'factor_f', true],
            ['En el trabajo, tiendo a ser más reflexivo/a y cauteloso/a que espontáneo/a.',                               'factor_f', true],

            // ── FACTOR G — Atención a las Normas (Rule-Consciousness) ────
            ['Cumplo rigurosamente las normas y procedimientos establecidos en mi organización.',                         'factor_g', false],
            ['Considero que respetar las políticas y reglamentos de la empresa es fundamental.',                          'factor_g', false],
            ['En ocasiones omito algunos procedimientos cuando considero que son innecesarios.',                          'factor_g', true],
            ['Adapto las normas de trabajo según las circunstancias cuando lo creo más conveniente.',                     'factor_g', true],

            // ── FACTOR H — Atrevimiento Social (Social Boldness) ─────────
            ['Me siento cómodo/a hablando en público o frente a grupos numerosos.',                                       'factor_h', false],
            ['Iniciar conversaciones con personas desconocidas me resulta fácil y natural.',                              'factor_h', false],
            ['Me pongo nervioso/a cuando tengo que hablar o presentar frente a un grupo de personas.',                   'factor_h', true],
            ['Prefiero evitar situaciones en las que soy el centro de atención.',                                         'factor_h', true],

            // ── FACTOR I — Sensibilidad / Receptividad (Sensitivity) ─────
            ['Suelo guiarme por mis sentimientos e intuición al tomar decisiones laborales importantes.',                 'factor_i', false],
            ['Me afectan emocionalmente las situaciones injustas o conflictivas que ocurren en mi entorno.',              'factor_i', false],
            ['Prefiero basar mis decisiones en datos y hechos concretos antes que en emociones.',                         'factor_i', true],
            ['Me considero una persona práctica que no se deja llevar por sentimentalismos en el trabajo.',               'factor_i', true],

            // ── FACTOR L — Vigilancia / Desconfianza (Vigilance) ─────────
            ['Suelo cuestionar las motivaciones detrás de las acciones de mis compañeros de trabajo.',                   'factor_l', false],
            ['Me mantengo alerta ante posibles intenciones ocultas en los acuerdos y negociaciones laborales.',          'factor_l', false],
            ['En general, confío en que mis compañeros actúan de buena fe.',                                              'factor_l', true],
            ['No suelo sospechar de las intenciones de las personas con quienes trabajo habitualmente.',                  'factor_l', true],

            // ── FACTOR M — Abstracción / Imaginación (Abstractedness) ────
            ['Con frecuencia me pierdo en mis propios pensamientos o ideas mientras realizo tareas cotidianas.',         'factor_m', false],
            ['Prefiero pensar en posibilidades y escenarios futuros antes que en los detalles inmediatos.',               'factor_m', false],
            ['Me concentro fácilmente en las tareas prácticas del día a día sin distraerme con ideas abstractas.',       'factor_m', true],
            ['Prefiero ocuparme de lo concreto y tangible antes que de teorías o conceptos alejados de la realidad.',    'factor_m', true],

            // ── FACTOR N — Privacidad / Reserva (Privateness) ────────────
            ['Suelo compartir muy poco sobre mi vida personal con mis compañeros de trabajo.',                            'factor_n', false],
            ['Prefiero mantener una imagen estrictamente profesional y revelar solo lo necesario sobre mí mismo/a.',     'factor_n', false],
            ['Me resulta fácil abrirme y hablar sobre mis experiencias personales con mis colegas.',                     'factor_n', true],
            ['Soy una persona directa que expresa abiertamente lo que piensa y siente en el trabajo.',                   'factor_n', true],

            // ── FACTOR O — Aprensión / Autoculpa (Apprehension) ──────────
            ['Con frecuencia me preocupa que mis errores puedan tener consecuencias graves en el trabajo.',              'factor_o', false],
            ['Suelo sentirme inseguro/a respecto a si estoy desempeñando bien mis funciones.',                           'factor_o', false],
            ['Confío en mis capacidades profesionales y raramente me preocupo en exceso por mis errores.',               'factor_o', true],
            ['Me siento seguro/a de mí mismo/a en la mayoría de las situaciones laborales.',                             'factor_o', true],

            // ── FACTOR Q1 — Apertura al Cambio (Openness to Change) ──────
            ['Me entusiasma cuando la organización implementa nuevas formas de trabajar o de estructurarse.',            'factor_q1', false],
            ['Busco activamente nuevas maneras de mejorar los procesos y métodos en los que participo.',                 'factor_q1', false],
            ['Prefiero métodos probados y confiables antes que arriesgarme con enfoques desconocidos.',                  'factor_q1', true],
            ['Los cambios frecuentes en los procedimientos o estructuras de trabajo me generan incomodidad.',             'factor_q1', true],

            // ── FACTOR Q2 — Autosuficiencia / Independencia (Self-Reliance)
            ['Prefiero tomar decisiones de forma independiente sin necesitar la aprobación del grupo.',                  'factor_q2', false],
            ['Me siento más productivo/a cuando puedo trabajar de forma autónoma.',                                      'factor_q2', false],
            ['Prefiero consultar con otros antes de tomar decisiones importantes en el trabajo.',                         'factor_q2', true],
            ['Disfruto más trabajar en equipo que hacerlo de manera individual.',                                         'factor_q2', true],

            // ── FACTOR Q3 — Perfeccionismo / Autocontrol (Perfectionism) ─
            ['Me esfuerzo por entregar un trabajo impecable, revisando cada detalle con cuidado.',                       'factor_q3', false],
            ['Tengo altos estándares de calidad y me resulta difícil conformarme con resultados mediocres.',             'factor_q3', false],
            ['No me molesta entregar un trabajo que es "suficientemente bueno", aunque no sea perfecto.',                'factor_q3', true],
            ['Soy flexible con los estándares de calidad cuando las circunstancias o el tiempo lo requieren.',           'factor_q3', true],

            // ── FACTOR Q4 — Tensión / Frustración (Tension) ──────────────
            ['Con frecuencia me siento tenso/a o bajo presión, incluso sin una causa aparente.',                         'factor_q4', false],
            ['Me resulta difícil relajarme o desconectarme cuando hay tareas laborales pendientes.',                     'factor_q4', false],
            ['Generalmente me siento tranquilo/a y sereno/a, incluso cuando hay mucho trabajo por hacer.',               'factor_q4', true],
            ['Mantengo la calma con facilidad incluso cuando enfrento múltiples demandas simultáneas.',                  'factor_q4', true],
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
