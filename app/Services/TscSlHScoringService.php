<?php

namespace App\Services;

class TscSlHScoringService
{
    // SJT scoring key — hospitality/F&B variant
    // 3 = best choice, 2 = good, 1 = acceptable, 0 = ineffective
    private const SJT_KEY = [
        1  => ['A'=>1,'B'=>3,'C'=>1,'D'=>2],  // E1 — empathy before solution
        2  => ['A'=>2,'B'=>3,'C'=>1,'D'=>0],  // E1 — patience with elderly guest
        3  => ['A'=>2,'B'=>3,'C'=>1,'D'=>0],  // E1 — acknowledge wait on arrival
        4  => ['A'=>1,'B'=>3,'C'=>1,'D'=>2],  // E2 — communicate unavailable dish
        5  => ['A'=>0,'B'=>3,'C'=>0,'D'=>1],  // E2 — allergy: verify before serving
        6  => ['A'=>1,'B'=>3,'C'=>0,'D'=>2],  // E2 — foreign guests, confirm order
        7  => ['A'=>1,'B'=>3,'C'=>2,'D'=>0],  // P1 — wrong drink, own it immediately
        8  => ['A'=>2,'B'=>3,'C'=>1,'D'=>1],  // P1 — birthday table multiple errors
        9  => ['A'=>1,'B'=>3,'C'=>0,'D'=>1],  // P1 — billing error at checkout
        10 => ['A'=>0,'B'=>3,'C'=>2,'D'=>0],  // P1 — two tables waiting 35+ min
        11 => ['A'=>0,'B'=>3,'C'=>1,'D'=>1],  // P2 — noisy intoxicated customer
        12 => ['A'=>0,'B'=>3,'C'=>1,'D'=>1],  // P2 — reservation dispute, full house
        13 => ['A'=>0,'B'=>3,'C'=>1,'D'=>2],  // P2 — rude customer throughout meal
        14 => ['A'=>0,'B'=>3,'C'=>1,'D'=>2],  // A1 — child about to pull tablecloth
        15 => ['A'=>0,'B'=>3,'C'=>1,'D'=>1],  // A1 — guests confused with wine list
        16 => ['A'=>0,'B'=>3,'C'=>2,'D'=>1],  // A1 — refill water without being asked
        17 => ['A'=>2,'B'=>3,'C'=>0,'D'=>1],  // A1 — overheard birthday comment
        18 => ['A'=>1,'B'=>3,'C'=>2,'D'=>1],  // A2 — spilled wine on guest
        19 => ['A'=>0,'B'=>3,'C'=>1,'D'=>2],  // A2 — overwhelmed Friday night peak
        20 => ['A'=>0,'B'=>3,'C'=>2,'D'=>0],  // A2 — tension after kitchen argument
    ];

    // Inverted Likert items (score = 6 − raw value)
    private const INVERTED = [23,25,28,30,33,35,38,40,43,48,50,52,53,55,58];

    // Dimension → SJT item numbers
    private const DIM_SJT = [
        'E1' => [1,2,3],
        'E2' => [4,5,6],
        'P1' => [7,8,9,10],
        'P2' => [11,12,13],
        'A1' => [14,15,16,17],
        'A2' => [18,19,20],
    ];

    // Dimension → Likert item numbers (items 51-60 distributed by affinity)
    private const DIM_LIKERT = [
        'E1' => [21,22,23,24,25,53],
        'E2' => [26,27,28,29,30,56,59],
        'P1' => [31,32,33,34,35,51,60],
        'P2' => [36,37,38,39,40,58],
        'A1' => [41,42,43,44,45,54,55],
        'A2' => [46,47,48,49,50,52,57],
    ];

    // M3 scenarios that contribute to each dimension
    private const DIM_M3 = [
        'E1' => [1],
        'E2' => [2],
        'P1' => [2],
        'P2' => [1,3],
        'A1' => [],
        'A2' => [3],
    ];

    // Performance levels by total score (max 225)
    private const LEVELS = [
        ['min'=>191,'max'=>225,'code'=>'sobresaliente'],
        ['min'=>158,'max'=>190,'code'=>'alto'],
        ['min'=>124,'max'=>157,'code'=>'adecuado'],
        ['min'=>90, 'max'=>123,'code'=>'en_desarrollo'],
        ['min'=>0,  'max'=>89, 'code'=>'por_debajo'],
    ];

    /**
     * Score Module 1 (SJT). Answers: {"1":"B","2":"C",...}
     */
    public function scoreM1(array $answers): array
    {
        $itemScores = [];
        $total = 0;

        foreach (self::SJT_KEY as $item => $key) {
            $chosen = strtoupper($answers[(string)$item] ?? '');
            $score  = $key[$chosen] ?? 0;
            $itemScores[$item] = $score;
            $total += $score;
        }

        return ['total' => $total, 'item_scores' => $itemScores];
    }

    /**
     * Score Module 2 (Likert). Answers: {"21":4,"22":3,...}
     * Total score uses only items 21-50 (30 items, max 150).
     */
    public function scoreM2(array $answers): array
    {
        $itemScores = [];

        foreach (range(21, 60) as $item) {
            $raw   = (int) ($answers[(string)$item] ?? 0);
            $raw   = max(1, min(5, $raw));
            $score = in_array($item, self::INVERTED) ? (6 - $raw) : $raw;
            $itemScores[$item] = $score;
        }

        $total = 0;
        foreach (range(21, 50) as $item) {
            $total += $itemScores[$item] ?? 0;
        }

        return ['total' => $total, 'item_scores' => $itemScores];
    }

    /**
     * Compute dimension scores from scored item arrays and M3 evaluator scores.
     */
    public function computeDimensions(
        array $m1ItemScores,
        array $m2ItemScores,
        array $m3ScoresArray = []
    ): array {
        $dims = [];

        foreach (['E1','E2','P1','P2','A1','A2'] as $dim) {
            $sjt = array_sum(array_intersect_key($m1ItemScores, array_flip(self::DIM_SJT[$dim])));
            $lkt = array_sum(array_intersect_key($m2ItemScores, array_flip(self::DIM_LIKERT[$dim])));
            $m3  = 0;
            foreach (self::DIM_M3[$dim] as $scenario) {
                $m3 += $m3ScoresArray[$scenario] ?? 0;
            }
            $dims[$dim] = $sjt + $lkt + $m3;
        }

        return $dims;
    }

    /**
     * Determine performance level from total score (0–225).
     */
    public function performanceLevel(int $total): string
    {
        foreach (self::LEVELS as $level) {
            if ($total >= $level['min'] && $total <= $level['max']) {
                return $level['code'];
            }
        }
        return 'por_debajo';
    }

    /**
     * Full scoring after M3 evaluator submission.
     */
    public function computeFinal(array $m1Answers, array $m2Answers, array $m3ScoresRaw): array
    {
        $m1 = $this->scoreM1($m1Answers);
        $m2 = $this->scoreM2($m2Answers);

        $m3NumericScores = [];
        foreach ([1,2,3] as $s) {
            $m3NumericScores[$s] = (int) ($m3ScoresRaw[(string)$s] ?? $m3ScoresRaw[$s] ?? 0);
        }
        $m3Total = array_sum($m3NumericScores);

        $dims  = $this->computeDimensions($m1['item_scores'], $m2['item_scores'], $m3NumericScores);
        $total = $m1['total'] + $m2['total'] + $m3Total;
        $level = $this->performanceLevel($total);

        return [
            'm1_score'         => $m1['total'],
            'm2_score'         => $m2['total'],
            'm3_score'         => $m3Total,
            'total_score'      => $total,
            'dimension_scores' => $dims,
            'performance_level'=> $level,
        ];
    }

    public function getSjtKey(): array
    {
        return self::SJT_KEY;
    }

    public function getInvertedItems(): array
    {
        return self::INVERTED;
    }
}
