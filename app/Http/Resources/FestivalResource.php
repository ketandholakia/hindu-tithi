<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FestivalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'names' => [
                'en' => $this->name_en,
                'gu' => $this->name_gu,
                'hi' => $this->name_hi,
            ],
            'category' => $this->category,
            'description' => $this->description,
            'rules' => $this->whenLoaded('rules', function () {
                return $this->rules->map(function ($rule) {
                    return [
                        'rule_type' => $rule->rule_type,
                        'month' => $rule->month,
                        'paksha' => $rule->paksha,
                        'tithi' => $rule->tithi,
                        'nakshatra' => $rule->nakshatra,
                        'required_kala' => $rule->required_kala,
                        'priority' => $rule->priority,
                    ];
                });
            }),
            'regional_variants' => $this->whenLoaded('aliases', function () {
                return $this->aliases->pluck('region');
            }),
        ];
    }
}
