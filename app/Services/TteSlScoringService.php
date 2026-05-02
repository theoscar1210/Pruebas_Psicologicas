<?php

namespace App\Services;

class TteSlScoringService
{
    // SJT key: item_number => [option_letter => score]
    private const SJT_KEY = [
        1  => ['A'=>0,'B'=>1,'C'=>3,'D'=>1],  // C1
        2  => ['A'=>0,'B'=>3,'C'=>1,'D'=>1],  // C1
        3  => ['A'=>0,'B'=>3,'C'=>1,'D'=>2],  // C1
        4  => ['A'=>0,'B'=>3,'C'=>1,'D'=>0],  // C2
        5  => ['A'=>0,'B'=>3,'C'=>0,'D'=>1],  // C2
        6  => ['A'=>1,'B'=>3,'C'=>1,'D'=>1],  // C2
        7  => ['A'=>1,'B'=>3,'C'=>1,'D'=>1],  // C3
        8  => ['A'=>1,'B'=>3,'C'=>1,'D'=>0],  // C3
        9  => ['A'=>1,'B'=>0,'C'=>3,'D'=>2],  // C3
        10 => ['A'=>1,'B'=>3,'C'=>1,'D'=>0],  // C4
        11 => ['A'=>0,'B'=>3,'C'=>0,'D'=>2],  // C4
        12 => ['A'=>0,'B'=>3,'C'=>2,'D'=>1],  // C4
        13 => ['A'=>0,'B'=>3,'C'=>1,'D'=>0],  // C5
        14 => ['A'=>1,'B'=>3,'C'=>1,'D'=>1],  // C5
        15 => ['A'=>0,'B'=>3,'C'=>1,'D'=>1],  // C5
        16 => ['A'=>0,'B'=>3,'C'=>1,'D'=>2],  // C6
        17 => ['A'=>0,'B'=>3,'C'=>1,'D'=>1],  // C6
        18 => ['A'=>0,'B'=>3,'C'=>1,'D'=>2],  // C6
        19 => ['A'=>1,'B'=>3,'C'=>1,'D'=>0],  // C7
        20 => ['A'=>0,'B'=>3,'C'=>2,'D'=>1],  // C7
    ];

    // Inverted Likert items (score = 6 − original)
    private const INVERTED = [23,25,29,31,35,37,41,43,48,53,55,59];

    // Dimension → SJT item numbers
    private const DIM_SJT = [
        'C1' => [1,2,3],
        'C2' => [4,5,6],
        'C3' => [7,8,9],
        'C4' => [10,11,12],
        'C5' => [13,14,15],
        'C6' => [16,17,18],
        'C7' => [19,20],
    ];

    // Dimension → Likert item numbers (all 40 items count toward total)
    private const DIM_LIKERT = [
        'C1' => [21,22,23,24,25,26],
        'C2' => [27,28,29,30,31,32],
        'C3' => [33,34,35,36,37,38],
        'C4' => [39,40,41,42,43,44],
        'C5' => [45,46,47,48,49,50],
        'C6' => [51,52,53,54,55,56],
        'C7' => [57,58,59,60],
    ];

    // M3 scenarios that contribute to each dimension
    private const DIM_M3 = [
        'C1' => [],
        'C2' => [2],
        'C3' => [],
        'C4' => [1,2],
        'C5' => [1,3],
        'C6' => [2],
        'C7' => [3],
    ];

    // Performance levels by total score (max 275)
    private const LEVELS = [
        ['min'=>234,'max'=>275,'code'=>'sobresaliente'],
        ['min'=>193,'max'=>233,'code'=>'alto'],
        ['min'=>152,'max'=>192,'code'=>'adecuado'],
        ['min'=>110,'max'=>151,'code'=>'en_desarrollo'],
        ['min'=>0,  'max'=>109,'code'=>'por_debajo'],
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
     * All 40 items (21–60) count toward total (max 200).
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

        $total = array_sum($itemScores);

        return ['total' => $total, 'item_scores' => $itemScores];
    }

    /**
     * Compute dimension scores from scored item arrays and M3 scores.
     */
    public function computeDimensions(
        array $m1ItemScores,
        array $m2ItemScores,
        array $m3ScoresArray = []
    ): array {
        $dims = [];

        foreach (['C1','C2','C3','C4','C5','C6','C7'] as $dim) {
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
     * Determine performance level from total score (0–275).
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
}
