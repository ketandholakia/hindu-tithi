<?php

namespace App\Services\Festival;

use App\Models\FestivalRule;
use App\Services\Festival\Contracts\PanchangProvider;

class RuleEvaluator
{
    protected PanchangProvider $panchang;

    public function __construct(PanchangProvider $panchang)
    {
        $this->panchang = $panchang;
    }

    /**
     * Evaluates a rule against astronomical timings to find the valid civil date.
     */
    public function evaluate(FestivalRule $rule, int $year, float $lat, float $lon): ?string
    {
        // Find astronomical occurrence of the required event (e.g. Tithi)
        $timings = $this->fetchTimingsForRule($rule, $year);

        if (empty($timings)) {
            return null;
        }

        // We assume the first occurrence in the year for now
        $occurrence = $timings[0];
        
        $start = $occurrence['start'];
        $end = $occurrence['end'];

        // Based on required_kala, resolve which civil day to assign this to.
        return $this->resolveCivilDate($rule->required_kala, $start, $end, $lat, $lon);
    }

    protected function fetchTimingsForRule(FestivalRule $rule, int $year): array
    {
        if ($rule->tithi !== null && $rule->month !== null && $rule->paksha !== null) {
            return $this->panchang->findTithiTimings($year, $rule->month, $rule->paksha, $rule->tithi);
        }

        if ($rule->rule_type === 'SANKRANTI') {
            // Simplified fallback
            return $this->panchang->findSankrantiTimings($year, 'Makara'); 
        }

        return [];
    }

    protected function resolveCivilDate(?string $kala, string $start, string $end, float $lat, float $lon): ?string
    {
        $startDate = date('Y-m-d', strtotime($start));
        $endDate = date('Y-m-d', strtotime($end));

        // Simplified Kala logic:
        switch ($kala) {
            case 'SUNRISE':
                // Does it span the sunrise of the end date?
                $sunTimings = $this->panchang->getSunriseSunset($endDate, $lat, $lon);
                if (strtotime($sunTimings['sunrise']) >= strtotime($start) && strtotime($sunTimings['sunrise']) <= strtotime($end)) {
                    return $endDate;
                }
                return $startDate;

            case 'NISHITA':
            case 'PRADOSH':
                // Check if the event is active during the evening/night of the start date
                return $startDate;

            case 'MADHYAHNA':
                // Midday checks
                return $startDate;

            default:
                // Default to the day the event starts
                return $startDate;
        }
    }
}
