<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\PsychologicalReport;
use Illuminate\Support\Facades\Http;

class AiNarrativeService
{
    private const MODEL      = 'llama-3.1-70b-versatile';
    private const MAX_TOKENS = 600;
    private const ENDPOINT   = 'https://api.groq.com/openai/v1/chat/completions';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.groq.key', '');
    }

    /**
     * Generate a narrative paragraph for one report section.
     *
     * @param  string  $section  personality|cognitive|competencies|projective|interview
     */
    public function generate(PsychologicalReport $report, Candidate $candidate, string $section): string
    {
        $prompt = $this->buildPrompt($report, $candidate, $section);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])->post(self::ENDPOINT, [
            'model'      => self::MODEL,
            'max_tokens' => self::MAX_TOKENS,
            'temperature' => 0.7,
            'messages'   => [
                ['role' => 'system', 'content' => $this->systemPrompt()],
                ['role' => 'user',   'content' => $prompt],
            ],
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Error al contactar la API de IA: ' . $response->body());
        }

        return trim($response->json('choices.0.message.content', ''));
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Eres un psicólogo organizacional experto en redacción de informes de selección de personal.
Redacta párrafos técnicos, objetivos y profesionales en español colombiano formal.
Usa terminología psicológica apropiada. Sé conciso (máximo 5 oraciones por párrafo).
No uses listas, solo prosa continua. No repitas los datos numéricos de forma literal;
interprétalos cualitativamente. Nunca inventes datos que no estén en el contexto proporcionado.
PROMPT;
    }

    private function buildPrompt(PsychologicalReport $report, Candidate $candidate, string $section): string
    {
        $cargo     = $candidate->position?->name ?? 'el cargo';
        $nombre    = $candidate->name;

        return match ($section) {
            'personality'   => $this->personalityPrompt($report, $nombre, $cargo),
            'cognitive'     => $this->cognitivePrompt($report, $nombre, $cargo),
            'competencies'  => $this->competenciesPrompt($report, $nombre, $cargo),
            'projective'    => $this->projectivePrompt($report, $nombre, $cargo),
            'interview'     => $this->interviewPrompt($report, $nombre, $cargo),
            default         => throw new \InvalidArgumentException("Sección desconocida: {$section}"),
        };
    }

    private function personalityPrompt(PsychologicalReport $report, string $nombre, string $cargo): string
    {
        $bf = [
            'Apertura'        => round((float) $report->bf_openness),
            'Responsabilidad' => round((float) $report->bf_conscientiousness),
            'Extraversión'    => round((float) $report->bf_extraversion),
            'Amabilidad'      => round((float) $report->bf_agreeableness),
            'Neuroticismo'    => round((float) $report->bf_neuroticism),
        ];

        $bfLines = collect($bf)->map(fn ($v, $k) => "  - {$k}: {$v}/100")->implode("\n");

        $pf16 = '';
        if (!empty($report->pf16_scores)) {
            $pf16Lines = collect($report->pf16_scores)
                ->map(fn ($v, $k) => "  - Factor {$k}: " . round((float)$v))
                ->implode("\n");
            $pf16 = "\n\nFactores 16PF:\n{$pf16Lines}";
        }

        return <<<PROMPT
Candidato: {$nombre} — Cargo aspirado: {$cargo}

Resultados Big Five (escala 0-100):
{$bfLines}{$pf16}

Redacta un párrafo narrativo sobre el perfil de personalidad de este candidato para el cargo mencionado.
Interpreta cómo estas dimensiones favorecen o dificultan su desempeño en el puesto.
PROMPT;
    }

    private function cognitivePrompt(PsychologicalReport $report, string $nombre, string $cargo): string
    {
        $score      = round((float) $report->cognitive_score);
        $percentile = $report->cognitive_percentile ?? 'N/D';
        $level      = $report->cognitive_level ?? 'No evaluado';

        return <<<PROMPT
Candidato: {$nombre} — Cargo aspirado: {$cargo}

Resultados cognitivos (Test de Matrices de Raven):
  - Puntuación normalizada: {$score}/100
  - Percentil: {$percentile}
  - Nivel: {$level}

Redacta un párrafo narrativo sobre las capacidades cognitivas de este candidato, indicando
su nivel de razonamiento abstracto, lógico y su adecuación para el cargo.
PROMPT;
    }

    private function competenciesPrompt(PsychologicalReport $report, string $nombre, string $cargo): string
    {
        if (empty($report->competency_scores)) {
            return "Candidato: {$nombre} — Cargo: {$cargo}\n\nNo hay datos de competencias disponibles aún.";
        }

        $lines = collect($report->competency_scores)
            ->map(fn ($v, $k) => "  - {$k}: " . round((float)$v) . '/100')
            ->implode("\n");

        return <<<PROMPT
Candidato: {$nombre} — Cargo aspirado: {$cargo}

Puntuaciones de competencias (Assessment Center):
{$lines}

Redacta un párrafo narrativo sobre el perfil de competencias conductuales de este candidato,
destacando sus fortalezas y áreas de desarrollo en relación con el cargo.
PROMPT;
    }

    private function projectivePrompt(PsychologicalReport $report, string $nombre, string $cargo): string
    {
        $score        = round((float) $report->wartegg_score);
        $observations = $report->projective_observations ?? 'Sin observaciones registradas.';

        return <<<PROMPT
Candidato: {$nombre} — Cargo aspirado: {$cargo}

Técnica proyectiva Wartegg:
  - Puntuación global del evaluador: {$score}/100
  - Observaciones clínicas del evaluador: {$observations}

Redacta un párrafo narrativo sobre los hallazgos proyectivos de este candidato,
integrando la puntuación y las observaciones del evaluador de forma clínica y objetiva.
PROMPT;
    }

    private function interviewPrompt(PsychologicalReport $report, string $nombre, string $cargo): string
    {
        $score        = round((float) $report->interview_score);
        $observations = $report->interview_observations ?? 'Sin observaciones registradas.';

        $compLines = '';
        if (!empty($report->interview_competencies)) {
            $compLines = "\nPuntuaciones por competencia:\n" .
                collect($report->interview_competencies)
                    ->map(fn ($v, $k) => "  - {$k}: " . round((float)$v) . '/100')
                    ->implode("\n");
        }

        return <<<PROMPT
Candidato: {$nombre} — Cargo aspirado: {$cargo}

Entrevista por competencias (metodología STAR):
  - Puntuación global: {$score}/100{$compLines}
  - Observaciones del evaluador: {$observations}

Redacta un párrafo narrativo sobre el desempeño del candidato en la entrevista por competencias,
interpretando su nivel de desarrollo conductual y su idoneidad para el cargo.
PROMPT;
    }
}
