<?php

namespace App\Services\Festival;

use App\Models\FestivalDefinition;
use App\Models\FestivalOccurrence;
use App\Services\Festival\Contracts\PanchangProvider;

class FestivalEngine
{
    protected PanchangProvider $panchang;
    protected RuleEvaluator $evaluator;

    public function __construct(PanchangProvider $panchang)
    {
        $this->panchang = $panchang;
        $this->evaluator = new RuleEvaluator($panchang);
    }

    /**
     * Calculates and saves festival occurrences for a specific year and location.
     */
    public function calculateForYear(int $year, float $lat, float $lon, string $calendarSystem = 'Amanta', ?int $locationId = null): void
    {
        $definitions = FestivalDefinition::with('rules')->where('enabled', true)->get();

        foreach ($definitions as $definition) {
            foreach ($definition->rules as $rule) {
                $civilDate = $this->evaluator->evaluate($rule, $year, $lat, $lon);

                if ($civilDate) {
                    FestivalOccurrence::updateOrCreate(
                        [
                            'festival_id' => $definition->id,
                            'rule_id' => $rule->id,
                            'location_id' => $locationId,
                            'date' => $civilDate,
                        ],
                        [
                            'calendar_system' => $calendarSystem,
                            'tithi' => $rule->tithi,
                            'nakshatra' => $rule->nakshatra,
                            'kala' => $rule->required_kala,
                            'tradition' => $rule->tradition,
                        ]
                    );
                }
            }
        }
    }
}
