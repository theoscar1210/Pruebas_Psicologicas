<?php

namespace App\Services;

class TscSlScoringService
{
    // SJT key: item_number => [option_letter => score]
    private const SJT_KEY = [
        1  => ['A'=>1,'B'=>3,'C'=>2,'D'=>1],
        2  => ['A'=>1,'B'=>2,'C'=>3,'D'=>1],
        3  => ['A'=>1,'B'=>0,'C'=>3,'D'=>2],
        4  => ['A'=>1,'B'=>1,'C'=>3,'D'=>1],
        5  => ['A'=>1,'B'=>3,'C'=>0,'D'=>1],
        6  => ['A'=>2,'B'=>3,'C'=>0,'D'=>2],
        7  => ['A'=>2,'B'=>3,'C'=>1,'D'=>0],
        8  => ['A'=>0,'B'=>3,'C'=>2,'D'=>1],
        9  => ['A'=>0,'B'=>3,'C'=>2,'D'=>1],
        10 => ['A'=>1,'B'=>1,'C'=>3,'D'=>2],
        11 => ['A'=>0,'B'=>1,'C'=>3,'D'=>2],
        12 => ['A'=>0,'B'=>0,'C'=>3,'D'=>1],
        13 => ['A'=>0,'B'=>2,'C'=>3,'D'=>1],
        14 => ['A'=>1,'B'=>2,'C'=>3,'D'=>2],
        15 => ['A'=>0,'B'=>3,'C'=>2,'D'=>1],
        16 => ['A'=>0,'B'=>3,'C'=>2,'D'=>1],
        17 => ['A'=>1,'B'=>3,'C'=>1,'D'=>1],
        18 => ['A'=>1,'B'=>3,'C'=>3,'D'=>0],
        19 => ['A'=>1,'B'=>3,'C'=>1,'D'=>2],
        20 => ['A'=>0,'B'=>3,'C'=>1,'D'=>1],
    ];

    // Inverted Likert items (score = 6 − original)
    private const INVERTED = [23,25,28,30,33,35,38,40,43,48,50,52,53,55,58];

    // Dimension → SJT item numbers
    private const DIM_SJT = [
        'E1' => [1,2,3],
        'E2' => [4,5,6],
        'P1' => [7,8,9,20],
        'P2' => [10,11,12],
        'A1' => [13,14,15,19],
        'A2' => [16,17,18],
    ];

    // Dimension → Likert item numbers (includes items 51-60 for dimension detail)
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
        'E1' => [],
        'E2' => [],
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
     * Returns ['total'=>int, 'item_scores'=>[1=>3, 2=>1,...]]
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
     * For total_score: only items 21-50 (30 main items, max 150).
     * For dimension_scores: includes items 51-60.
     * Returns ['total'=>int, 'item_scores'=>[21=>4, 22=>5,...]]
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

        // Total only from main 30 items (21-50)
        $total = 0;
        foreach (range(21, 50) as $item) {
            $total += $itemScores[$item] ?? 0;
        }

        return ['total' => $total, 'item_scores' => $itemScores];
    }

    /**
     * Compute dimension scores from already-scored item arrays and M3 scores.
     * m3ScoresArray: [1=>int, 2=>int, 3=>int] (BARS values)
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
     * Determine performance level from total score (0-225).
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
     * Returns array ready to save to session.
     */
    public function computeFinal(array $m1Answers, array $m2Answers, array $m3ScoresRaw): array
    {
        $m1 = $this->scoreM1($m1Answers);
        $m2 = $this->scoreM2($m2Answers);

        // Extract BARS numeric scores from admin input: {1=>int,just_1=>'...'}
        $m3NumericScores = [];
        foreach ([1,2,3] as $s) {
            $m3NumericScores[$s] = (int) ($m3ScoresRaw[(string)$s] ?? $m3ScoresRaw[$s] ?? 0);
        }
        $m3Total = array_sum($m3NumericScores);

        $dims  = $this->computeDimensions($m1['item_scores'], $m2['item_scores'], $m3NumericScores);
        $total = $m1['total'] + $m2['total'] + $m3Total;
        $level = $this->performanceLevel($total);

        return [
            'm1_score'        => $m1['total'],
            'm2_score'        => $m2['total'],
            'm3_score'        => $m3Total,
            'total_score'     => $total,
            'dimension_scores'=> $dims,
            'performance_level'=> $level,
        ];
    }

    /**
     * Returns the SJT answer key (for admin display).
     */
    public function getSjtKey(): array
    {
        return self::SJT_KEY;
    }

    /**
     * Returns items that are inverted.
     */
    public function getInvertedItems(): array
    {
        return self::INVERTED;
    }
}
