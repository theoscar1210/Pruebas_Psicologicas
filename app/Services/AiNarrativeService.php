<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\PsychologicalReport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiNarrativeService
{
    private const MODEL      = 'llama-3.3-70b-versatile';
    private const MAX_TOKENS = 600;
    private const ENDPOINT   = 'https://api.groq.com/openai/v1/chat/completions';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.groq.key', '');
    }

    /**
     * Generate a comprehensive AI full report for the entire psychological profile.
     * Returns ['report' => string, 'recommendation' => 'apto'|'apto_con_reservas'|'no_apto'].
     */
    public function generateFullReport(PsychologicalReport $report, Candidate $candidate): array
    {
        $prompt = $this->fullReportPrompt($report, $candidate);

        $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])
            ->timeout(60)
            ->retry(2, 800)
            ->post(self::ENDPOINT, [
                'model'       => self::MODEL,
                'max_tokens'  => 1800,
                'temperature' => 0.65,
                'messages'    => [
                    ['role' => 'system', 'content' => $this->fullReportSystemPrompt()],
                    ['role' => 'user',   'content' => $prompt],
                ],
            ]);

        if (!$response->successful()) {
            Log::error('Groq full report error', [
                'status'       => $response->status(),
                'candidate_id' => $candidate->id,
            ]);
            throw new \RuntimeException('El servicio de generación de informe no está disponible.');
        }

        $content = strip_tags(trim($response->json('choices.0.message.content', '')));

        if (empty($content)) {
            throw new \RuntimeException('El modelo devolvió una respuesta vacía.');
        }

        // Extraer la recomendación de la última línea con el marcador
        $recommendation = 'apto_con_reservas';
        if (preg_match('/RECOMENDACIÓN_FINAL:\s*(APTO_CON_RESERVAS|NO_APTO|APTO)/i', $content, $m)) {
            $recommendation = strtolower($m[1]);
        }

        // Limpiar el marcador del texto visible
        $reportText = trim(preg_replace('/\n*RECOMENDACIÓN_FINAL:.*$/i', '', $content));

        if (strlen($reportText) > 8000) {
            $reportText = mb_substr($reportText, 0, 8000);
        }

        return ['report' => $reportText, 'recommendation' => $recommendation];
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
            ])
            ->timeout(30)
            ->retry(2, 500)
            ->post(self::ENDPOINT, [
                'model'       => self::MODEL,
                'max_tokens'  => self::MAX_TOKENS,
                'temperature' => 0.7,
                'messages'    => [
                    ['role' => 'system', 'content' => $this->systemPrompt()],
                    ['role' => 'user',   'content' => $prompt],
                ],
            ]);

        if (!$response->successful()) {
            Log::error('Groq API error', [
                'status'       => $response->status(),
                'candidate_id' => $candidate->id,
                'section'      => $section,
            ]);
            throw new \RuntimeException('El servicio de generación de narrativa no está disponible temporalmente.');
        }

        $content = strip_tags(trim($response->json('choices.0.message.content', '')));

        // Validar respuesta antes de persistir
        if (strlen($content) > 5000) {
            Log::warning('Groq response exceeds max length', ['candidate_id' => $candidate->id, 'length' => strlen($content)]);
            $content = mb_substr($content, 0, 5000);
        }

        if (empty($content)) {
            throw new \RuntimeException('El modelo devolvió una respuesta vacía.');
        }

        return $content;
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
        $cargo  = $candidate->position?->name ?? 'el cargo';

        // Pseudónimo determinístico — el nombre real NUNCA se envía a la API externa.
        // Se deriva del ID + APP_KEY: no reversible, consistente por candidato.
        $nombre = $this->pseudonym($candidate->id);

        return match ($section) {
            'personality'  => $this->personalityPrompt($report, $nombre, $cargo),
            'cognitive'    => $this->cognitivePrompt($report, $nombre, $cargo),
            'competencies' => $this->competenciesPrompt($report, $nombre, $cargo),
            'projective'   => $this->projectivePrompt($report, $nombre, $cargo),
            'interview'    => $this->interviewPrompt($report, $nombre, $cargo),
            default        => throw new \InvalidArgumentException("Sección desconocida: {$section}"),
        };
    }

    private function pseudonym(int $candidateId): string
    {
        $hash = hash('sha256', $candidateId . config('app.key'));
        return 'Candidato ' . strtoupper(substr($hash, 0, 6));
    }

    // Sanitiza texto libre antes de incluirlo en el prompt.
    // Previene prompt injection desde campos de observaciones del evaluador.
    private function sanitizeObservation(?string $text): string
    {
        if (empty($text)) {
            return 'Sin observaciones registradas.';
        }

        $text = mb_substr(trim($text), 0, 500);

        $patterns = [
            '/ignora?\s+(las?\s+)?instrucciones?\s+anteriores?/i',
            '/ignore\s+(previous|all|prior)\s+instructions?/i',
            '/system\s*prompt/i',
            '/\[INST\]/i',
            '/<<SYS>>/i',
            '/eres\s+ahora/i',
            '/you\s+are\s+now/i',
            '/act\s+as/i',
            '/HUMAN:/i',
            '/ASSISTANT:/i',
        ];

        return preg_replace($patterns, '[REDACTED]', $text) ?? $text;
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
            return <<<PROMPT
Candidato: {$nombre} — Cargo aspirado: {$cargo}

Puntuaciones de competencias (Assessment Center): No se han registrado puntuaciones aún.

Redacta un párrafo breve indicando que la evaluación de competencias conductuales está pendiente
para este candidato y que el perfil de competencias será completado una vez se realice el
Assessment Center correspondiente.
PROMPT;
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
        $observations = $this->sanitizeObservation($report->projective_observations);

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
        $observations = $this->sanitizeObservation($report->interview_observations);

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

    private function fullReportSystemPrompt(): string
    {
        return <<<'PROMPT'
Eres un psicólogo organizacional experto en evaluación de selección de personal.
Redacta informes técnicos, objetivos y profesionales en español colombiano formal.
Usa terminología psicológica apropiada. Integra todos los datos del perfil de forma coherente.
Interpreta cualitativamente; no repitas los números de forma literal.
Nunca inventes datos que no estén en el contexto. Sé claro en la recomendación final.
Al final del informe escribe EXACTAMENTE en una línea separada:
RECOMENDACIÓN_FINAL: APTO  o  RECOMENDACIÓN_FINAL: APTO_CON_RESERVAS  o  RECOMENDACIÓN_FINAL: NO_APTO
PROMPT;
    }

    private function fullReportPrompt(PsychologicalReport $report, Candidate $candidate): string
    {
        $cargo  = $candidate->position?->name ?? 'el cargo';
        $nombre = $this->pseudonym($candidate->id);

        // ── Personalidad ─────────────────────────────────────────────────────
        $bfSection = 'No evaluado.';
        if ($report->bf_openness !== null) {
            $bfSection = implode(', ', [
                'Apertura ' . round((float)$report->bf_openness) . '%',
                'Responsabilidad ' . round((float)$report->bf_conscientiousness) . '%',
                'Extraversión ' . round((float)$report->bf_extraversion) . '%',
                'Amabilidad ' . round((float)$report->bf_agreeableness) . '%',
                'Neuroticismo ' . round((float)$report->bf_neuroticism) . '%',
            ]);
        }

        // ── Cognitivo ────────────────────────────────────────────────────────
        $cogSection = 'No evaluado.';
        if ($report->cognitive_score !== null) {
            $cogSection = round((float)$report->cognitive_score) . '/100'
                . ' — Nivel: ' . ($report->cognitive_level ?? 'N/D')
                . ' — Percentil: ' . ($report->cognitive_percentile ?? 'N/D');
        }

        // ── Competencias ─────────────────────────────────────────────────────
        $compSection = 'No evaluado.';
        if (!empty($report->competency_scores)) {
            $compSection = collect($report->competency_scores)
                ->filter(fn($v) => is_numeric($v))
                ->map(fn($v, $k) => "{$k}: " . round((float)$v) . '%')
                ->implode(', ');
        }

        // ── Proyectivo ───────────────────────────────────────────────────────
        $projScore = $report->wartegg_score !== null ? round((float)$report->wartegg_score) . '/100' : 'N/D';
        $projObs   = $this->sanitizeObservation($report->projective_observations);

        // ── Entrevista ───────────────────────────────────────────────────────
        $intScore = $report->interview_score !== null ? round((float)$report->interview_score) . '/100' : 'N/D';
        $intObs   = $this->sanitizeObservation($report->interview_observations);
        $intComp  = '';
        if (!empty($report->interview_competencies)) {
            $intComp = ' Competencias STAR: ' . collect($report->interview_competencies)
                ->filter(fn($v) => is_numeric($v))
                ->map(fn($v, $k) => "{$k}: " . round((float)$v))
                ->implode(', ') . '.';
        }

        // ── Riesgos y narrativas previas ─────────────────────────────────────
        $risks = '';
        if (!empty($report->labor_risks)) {
            $risks = "\nRiesgos identificados: " . implode(', ', $report->labor_risks) . '.';
        }

        $narratives = '';
        foreach (['narrative_personality', 'narrative_cognitive', 'narrative_competencies', 'narrative_projective', 'narrative_interview'] as $field) {
            if (!empty($report->$field)) {
                $label = str_replace(['narrative_', '_'], ['', ' '], $field);
                $narratives .= "\n[Narrativa {$label}]: " . mb_substr($report->$field, 0, 300) . '…';
            }
        }

        return <<<PROMPT
DATOS DEL CANDIDATO
Pseudónimo: {$nombre} | Cargo aspirado: {$cargo}

PERSONALIDAD (Big Five): {$bfSection}
COGNITIVO (Raven): {$cogSection}
COMPETENCIAS (AC): {$compSection}
PROYECTIVO (Wartegg): Puntuación {$projScore}. Observaciones: {$projObs}
ENTREVISTA STAR: Puntuación {$intScore}.{$intComp} Observaciones: {$intObs}{$risks}
{$narratives}

---
Redacta un INFORME PSICOLÓGICO INTEGRAL (500-700 palabras) con las siguientes secciones:
1. Presentación del candidato y contexto de la evaluación
2. Perfil de personalidad y estilo conductual
3. Capacidad cognitiva y potencial de aprendizaje
4. Competencias laborales y desempeño en entrevista
5. Indicadores proyectivos y dinámica emocional
6. Fortalezas clave y áreas de desarrollo
7. Ajuste al cargo y recomendación

Al final escribe en una línea separada:
RECOMENDACIÓN_FINAL: APTO  (si cumple el perfil)
RECOMENDACIÓN_FINAL: APTO_CON_RESERVAS  (si cumple con observaciones)
RECOMENDACIÓN_FINAL: NO_APTO  (si no cumple el perfil)
PROMPT;
    }
}
