<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        // ── Prueba 1: Servicio al Cliente ──────────────────────────────────
        $test1 = Test::create([
            'name' => 'Test de Servicio al Cliente',
            'description' => 'Evalúa las competencias del candidato en atención y servicio al cliente.',
            'instructions' => 'Lea cada pregunta con calma y seleccione la respuesta que mejor refleja su comportamiento habitual. No hay respuestas incorrectas, sea honesto.',
            'time_limit' => 30,
            'passing_score' => 65,
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        $this->createMultipleChoiceQuestion($test1, 1,
            '¿Qué hace cuando un cliente se queja del tiempo de espera?',
            [
                ['text' => 'Me disculpo y explico la situación ofreciendo una solución', 'value' => 3, 'is_correct' => true],
                ['text' => 'Le digo que el problema no es mi culpa', 'value' => 0],
                ['text' => 'Ignoro el comentario y sigo trabajando', 'value' => 0],
                ['text' => 'Le ofrezco un descuento sin consultar al supervisor', 'value' => 1],
            ]
        );

        $this->createMultipleChoiceQuestion($test1, 2,
            'Un cliente le pide algo que no está en el menú. ¿Qué hace?',
            [
                ['text' => 'Le digo que no es posible y sigo atendiendo otras mesas', 'value' => 0],
                ['text' => 'Consulto con el chef si se puede preparar algo similar', 'value' => 3, 'is_correct' => true],
                ['text' => 'Le ofrezco el plato más parecido del menú explicando sus características', 'value' => 2],
                ['text' => 'Le digo que vuelva otro día', 'value' => 0],
            ]
        );

        $this->createLikertQuestion($test1, 3,
            'Me siento cómodo atendiendo múltiples mesas al mismo tiempo bajo presión.',
            5
        );

        $this->createLikertQuestion($test1, 4,
            'Mantengo una actitud positiva incluso cuando los clientes son difíciles.',
            5
        );

        // ── Prueba 2: Trabajo en Equipo ────────────────────────────────────
        $test2 = Test::create([
            'name' => 'Test de Trabajo en Equipo',
            'description' => 'Mide la capacidad de colaboración y comunicación en entornos de trabajo grupal.',
            'instructions' => 'Responda todas las preguntas. En las escalas, 1 = Nunca y 5 = Siempre.',
            'time_limit' => 20,
            'passing_score' => 60,
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        $this->createLikertQuestion($test2, 1, 'Comparto información útil con mis compañeros sin que me lo pidan.', 5);
        $this->createLikertQuestion($test2, 2, 'Acepto las críticas constructivas con actitud positiva.', 5);
        $this->createLikertQuestion($test2, 3, 'Me ofrezco a ayudar cuando un compañero tiene exceso de trabajo.', 5);

        $this->createMultipleChoiceQuestion($test2, 4,
            'Si hay un conflicto con un compañero, ¿qué prefiere hacer?',
            [
                ['text' => 'Ignorar el problema y esperar que se resuelva solo', 'value' => 0],
                ['text' => 'Hablarlo directamente con el compañero de forma respetuosa', 'value' => 3, 'is_correct' => true],
                ['text' => 'Quejarse con otros compañeros', 'value' => 0],
                ['text' => 'Reportarlo inmediatamente al supervisor sin intentar resolverlo', 'value' => 1],
            ]
        );

        // Asociar pruebas a cargos
        $mesero = Position::where('name', 'Mesero')->first();
        $recepcion = Position::where('name', 'Recepcionista')->first();
        $cajero = Position::where('name', 'Cajero')->first();

        if ($mesero) {
            $mesero->tests()->attach([$test1->id => ['order' => 1], $test2->id => ['order' => 2]]);
        }
        if ($recepcion) {
            $recepcion->tests()->attach([$test1->id => ['order' => 1], $test2->id => ['order' => 2]]);
        }
        if ($cajero) {
            $cajero->tests()->attach([$test1->id => ['order' => 1]]);
        }
    }

    private function createMultipleChoiceQuestion(Test $test, int $order, string $text, array $options): Question
    {
        $question = Question::create([
            'test_id' => $test->id,
            'text' => $text,
            'type' => 'multiple_choice',
            'points' => 3,
            'order' => $order,
            'is_required' => true,
        ]);

        foreach ($options as $i => $opt) {
            QuestionOption::create([
                'question_id' => $question->id,
                'text' => $opt['text'],
                'value' => $opt['value'],
                'is_correct' => $opt['is_correct'] ?? false,
                'order' => $i + 1,
            ]);
        }

        return $question;
    }

    private function createLikertQuestion(Test $test, int $order, string $text, int $maxValue = 5): Question
    {
        $question = Question::create([
            'test_id' => $test->id,
            'text' => $text,
            'type' => 'likert',
            'points' => $maxValue,
            'order' => $order,
            'is_required' => true,
        ]);

        $labels = [1 => 'Nunca', 2 => 'Casi nunca', 3 => 'A veces', 4 => 'Casi siempre', 5 => 'Siempre'];

        for ($i = 1; $i <= $maxValue; $i++) {
            QuestionOption::create([
                'question_id' => $question->id,
                'text' => $labels[$i] ?? (string)$i,
                'value' => $i,
                'is_correct' => false,
                'order' => $i,
            ]);
        }

        return $question;
    }
}
