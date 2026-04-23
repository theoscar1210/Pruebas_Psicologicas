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
        $test = Test::updateOrCreate(
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
        $test = Test::updateOrCreate(
            ['test_type' => '16pf'],
            [
                'name'             => '16PF — 16 Factores de Personalidad (Selección Laboral)',
                'description'      => 'Instrumento 16PF-SL basado en el modelo de Raymond B. Cattell. 64 ítems tipo Likert que evalúan 16 factores de personalidad relevantes para el desempeño laboral.',
                'instructions'     => "A continuación encontrará una serie de afirmaciones relacionadas con su forma habitual de pensar, sentir y actuar en contextos laborales y cotidianos.\n\nResponda con honestidad — no hay respuestas correctas ni incorrectas. Lo que importa es que sus respuestas reflejen cómo es usted realmente, no cómo le gustaría ser o cómo cree que debería responder.\n\nUse la siguiente escala:\n1 = Totalmente en desacuerdo — Esta afirmación NO me describe en absoluto.\n2 = En desacuerdo — Esta afirmación me describe pocas veces o en raras ocasiones.\n3 = Neutral / No sé — No estoy seguro/a si esta afirmación me describe o no.\n4 = De acuerdo — Esta afirmación me describe frecuentemente o en la mayoría de contextos.\n5 = Totalmente de acuerdo — Esta afirmación me describe de manera precisa y consistente.",
                'module'           => 'personalidad',
                'evaluator_scored' => false,
                'scoring_method'   => 'dimensional',
                'time_limit'       => 30,
                'passing_score'    => 0,
                'is_active'        => true,
                'created_by'       => $this->adminId,
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
    // 3. MPR-SL — Matrices Progresivas de Raven (Selección Laboral) v1.0
    //    20 ítems · 3 sets (A×7 + B×7 + C×6) · 6 opciones A–F
    //    Referencia: MPR_SL_Guia_Tecnica_Raven.md
    // ══════════════════════════════════════════════════════════════════════
    private function seedRaven(): void
    {
        $test = Test::updateOrCreate(
            ['test_type' => 'raven'],
            [
                'name'           => 'MPR-SL — Matrices Progresivas de Raven (Selección Laboral)',
                'description'    => 'Medida de inteligencia fluida (Gf) y razonamiento abstracto. 20 ítems de dificultad progresiva organizados en tres sets (A: fácil, B: medio, C: difícil). Cada ítem presenta una matriz con una pieza faltante y 6 opciones de respuesta (A–F).',
                'instructions'   => "A continuación encontrará una serie de matrices (figuras con un patrón lógico) en las que falta una pieza.\n\nObserve con atención el patrón de cada matriz y seleccione, entre las seis opciones (A – F), la que completa correctamente la figura.\n\nIndicaciones importantes:\n• No hay penalización por respuestas incorrectas.\n• Si un ítem le resulta difícil, continúe y regrese al final si le queda tiempo.\n• Trabaje con calma pero sin demorarse demasiado en un solo ítem.\n• Dispone de 20 minutos para completar los 20 ítems.",
                'module'         => 'cognitivo',
                'test_type'      => 'raven',
                'evaluator_scored' => false,
                'scoring_method' => 'percentage',
                'time_limit'     => 20,
                'passing_score'  => 55,
                'is_active'      => true,
                'created_by'     => $this->adminId,
            ]
        );

        // Elimina ítems anteriores para re-sembrar con el instrumento MPR-SL v1.0
        if ($test->questions()->exists()) {
            $test->questions()->each(function ($q) {
                $q->options()->delete();
                $q->delete();
            });
        }

        // Estructura: [texto del ítem, set (categoría), letra_correcta (A–F), dificultad]
        // La letra correcta proviene de la clave de respuestas del instrumento MPR-SL.
        // image_path apuntará a /storage/tests/raven/{set}_item_{n}.png cuando se carguen las imágenes.
        $items = [
            // ── SET A — Fácil (ítems 1–7) ─────────────────────────────────
            // Regla única por ítem: progresión, rotación, posición, ciclo de formas.
            [
                'text'       => "Set A — Ítem 1\nObserve la matriz 3×3. El fondo de cada figura cambia gradualmente de claro a oscuro de izquierda a derecha y de arriba a abajo. ¿Cuál de las seis opciones (A–F) completa correctamente el patrón de la casilla faltante?",
                'set'        => 'set_a', 'correct' => 'A', 'difficulty' => 1,
            ],
            [
                'text'       => "Set A — Ítem 2\nEn esta matriz las figuras aumentan progresivamente de tamaño siguiendo una dirección definida. Identifique la opción (A–F) que ocupa la posición faltante respetando la regla de tamaño.",
                'set'        => 'set_a', 'correct' => 'A', 'difficulty' => 1,
            ],
            [
                'text'       => "Set A — Ítem 3\nCada fila de la matriz contiene un número creciente de elementos. Seleccione la opción (A–F) que mantiene la progresión de conteo en la casilla faltante.",
                'set'        => 'set_a', 'correct' => 'C', 'difficulty' => 1,
            ],
            [
                'text'       => "Set A — Ítem 4\nUn triángulo gira en cada casilla siguiendo una secuencia de rotación. ¿Cuál opción (A–F) muestra la orientación correcta del triángulo en la posición faltante?",
                'set'        => 'set_a', 'correct' => 'A', 'difficulty' => 2,
            ],
            [
                'text'       => "Set A — Ítem 5\nUna figura se desplaza de una posición a otra dentro de la cuadrícula 3×3 siguiendo un patrón regular. ¿Qué opción (A–F) indica la posición correcta de la figura en la casilla que falta?",
                'set'        => 'set_a', 'correct' => 'B', 'difficulty' => 1,
            ],
            [
                'text'       => "Set A — Ítem 6\nLas formas geométricas de la matriz se suceden en un ciclo repetitivo (p. ej. círculo → triángulo → cuadrado → …). Identifique la opción (A–F) que continúa el ciclo en la casilla faltante.",
                'set'        => 'set_a', 'correct' => 'C', 'difficulty' => 2,
            ],
            [
                'text'       => "Set A — Ítem 7\nUna flecha cambia de dirección en cada casilla siguiendo una secuencia lógica. ¿Cuál opción (A–F) muestra la dirección correcta de la flecha en la posición que falta?",
                'set'        => 'set_a', 'correct' => 'D', 'difficulty' => 2,
            ],

            // ── SET B — Medio (ítems 8–14) ────────────────────────────────
            // Dos reglas simultáneas por ítem: combinaciones de tamaño, relleno,
            // adición, eliminación, rotación y conteo.
            [
                'text'       => "Set B — Ítem 8\nEn esta matriz dos atributos varían al mismo tiempo: el tamaño de la figura y su nivel de relleno (vacío → sombreado → relleno). Seleccione la opción (A–F) que satisface ambas reglas en la casilla faltante.",
                'set'        => 'set_b', 'correct' => 'E', 'difficulty' => 3,
            ],
            [
                'text'       => "Set B — Ítem 9\nCada fila aplica una regla de adición: la tercera figura es el resultado de combinar los elementos de las dos primeras. ¿Cuál opción (A–F) es la combinación correcta?",
                'set'        => 'set_b', 'correct' => 'A', 'difficulty' => 3,
            ],
            [
                'text'       => "Set B — Ítem 10\nEn esta matriz los elementos se eliminan progresivamente fila a fila siguiendo una secuencia lógica. ¿Qué opción (A–F) refleja el estado correcto de eliminación en la casilla faltante?",
                'set'        => 'set_b', 'correct' => 'C', 'difficulty' => 3,
            ],
            [
                'text'       => "Set B — Ítem 11\nUna línea interna rota 30° en el sentido de las agujas del reloj en cada casilla. Seleccione la opción (A–F) con el ángulo de rotación correcto para la posición faltante.",
                'set'        => 'set_b', 'correct' => 'A', 'difficulty' => 3,
            ],
            [
                'text'       => "Set B — Ítem 12\nEl relleno de la figura de la primera columna se copia en la figura de la tercera columna de la misma fila. ¿Qué opción (A–F) aplica correctamente esta regla de copia en la casilla faltante?",
                'set'        => 'set_b', 'correct' => 'B', 'difficulty' => 4,
            ],
            [
                'text'       => "Set B — Ítem 13\nEl patrón de relleno sigue una diagonal: las casillas se rellenan en orden diagonal de izquierda a derecha. Identifique la opción (A–F) que completa el patrón diagonal en la casilla que falta.",
                'set'        => 'set_b', 'correct' => 'C', 'difficulty' => 4,
            ],
            [
                'text'       => "Set B — Ítem 14\nEl número de elementos dentro de cada figura sigue una regla de multiplicación entre filas y columnas. ¿Cuál opción (A–F) tiene el número correcto de elementos en la casilla faltante?",
                'set'        => 'set_b', 'correct' => 'E', 'difficulty' => 4,
            ],

            // ── SET C — Difícil (ítems 15–20) ─────────────────────────────
            // Múltiples reglas independientes: ciclos dobles, figuras anidadas,
            // diferencia booleana, triple atributo, posición y cuadrado latino.
            [
                'text'       => "Set C — Ítem 15\nDos ciclos se desarrollan simultáneamente: la forma exterior cambia en secuencia mientras el relleno interior sigue su propio ciclo independiente. Seleccione la opción (A–F) que satisface ambos ciclos en la casilla faltante.",
                'set'        => 'set_c', 'correct' => 'C', 'difficulty' => 5,
            ],
            [
                'text'       => "Set C — Ítem 16\nLas figuras se anidan de mayor a menor, pero en orden inverso al de las filas superiores. ¿Cuál opción (A–F) respeta la regla de anidamiento inverso en la casilla que falta?",
                'set'        => 'set_c', 'correct' => 'D', 'difficulty' => 5,
            ],
            [
                'text'       => "Set C — Ítem 17\nLa tercera figura de cada fila contiene solo los elementos que NO son compartidos por las dos primeras figuras (diferencia booleana / XOR visual). ¿Qué opción (A–F) aplica correctamente esta regla?",
                'set'        => 'set_c', 'correct' => 'B', 'difficulty' => 6,
            ],
            [
                'text'       => "Set C — Ítem 18\nTres atributos (forma, tamaño y relleno) cambian de manera independiente en cada casilla. Las nueve casillas de la matriz contienen cada combinación exactamente una vez. Identifique la opción (A–F) que completa el conjunto sin repetir combinaciones.",
                'set'        => 'set_c', 'correct' => 'D', 'difficulty' => 6,
            ],
            [
                'text'       => "Set C — Ítem 19\nDos reglas posicionales actúan de forma independiente: una rige la posición horizontal del elemento y otra la posición vertical. ¿Qué opción (A–F) satisface ambas reglas posicionales en la casilla faltante?",
                'set'        => 'set_c', 'correct' => 'A', 'difficulty' => 7,
            ],
            [
                'text'       => "Set C — Ítem 20\nLa matriz sigue un cuadrado latino: cada símbolo aparece exactamente una vez en cada fila y en cada columna, combinado además con un atributo de relleno que también sigue la misma restricción. Seleccione la opción (A–F) que completa la matriz correctamente.",
                'set'        => 'set_c', 'correct' => 'E', 'difficulty' => 7,
            ],
        ];

        // Mapa de letra → índice 0-based (A=0, B=1, C=2, D=3, E=4, F=5)
        $letterIndex = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4, 'F' => 5];
        $labels      = ['A', 'B', 'C', 'D', 'E', 'F'];

        foreach ($items as $order => $item) {
            $correctIdx = $letterIndex[$item['correct']];

            $question = Question::create([
                'test_id'     => $test->id,
                'text'        => $item['text'],
                'type'        => 'multiple_choice',
                'points'      => 1,
                'order'       => $order + 1,
                'is_required' => true,
                'dimension'   => 'raven_total',
                'category'    => $item['set'],
                'image_path'  => "raven/{$item['set']}_item_" . ($order + 1) . ".png",
            ]);

            foreach ($labels as $i => $label) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'text'        => $label,
                    'value'       => $i === $correctIdx ? 1 : 0,
                    'is_correct'  => $i === $correctIdx,
                    'order'       => $i + 1,
                ]);
            }
        }
    }

    // ══════════════════════════════════════════════════════════════════════
    // 4. AC-SL — Assessment Center para Selección Laboral v1.0
    //    8 competencias conductuales · 3 clústeres · Escala BARS 1-5
    //    Calificado exclusivamente por assessors — evaluator_scored = true
    //    Referencia: ACSL_Assessment_Center_Seleccion_Laboral.md
    // ══════════════════════════════════════════════════════════════════════
    private function seedAssessmentCenter(): void
    {
        Test::updateOrCreate(
            ['test_type' => 'assessment_center'],
            [
                'name'             => 'AC-SL — Assessment Center para Selección Laboral',
                'description'      => 'Evaluación integral de 8 competencias conductuales organizadas en 3 clústeres (Liderazgo y Gestión, Relaciones Interpersonales, Desempeño y Resultados). Calificado por assessors certificados mediante escala BARS 1-5. Técnicas: In-Basket, Role Play, Caso Grupal, Presentación e Entrevista Conductual (BEI).',
                'instructions'     => 'EVALUACIÓN ADMINISTRADA POR EL ASSESSOR. El candidato participa en los ejercicios del Assessment Center (In-Basket, Role Play, Caso Grupal, Presentación, BEI). Los assessors observan conductas directamente observables y califican según la escala BARS 1-5 por competencia. Esta vista corresponde al registro de resultados del evaluador tras la sesión de integración (wash-up).',
                'module'           => 'competencias',
                'evaluator_scored' => true,
                'scoring_method'   => 'evaluator',
                'time_limit'       => null,
                'passing_score'    => 60,
                'is_active'        => true,
                'created_by'       => $this->adminId,
            ]
        );
        // El AC-SL es evaluado directamente por assessors mediante EvaluatorAssessment.
        // No requiere preguntas en el sistema de candidatos.
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
