<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FestivalOccurrenceResource extends JsonResource
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
            'festival' => new FestivalResource($this->whenLoaded('definition')),
            'date' => $this->date->format('Y-m-d'),
            'location_id' => $this->location_id,
            'calendar_system' => $this->calendar_system,
            'details' => [
                'tithi' => $this->tithi,
                'nakshatra' => $this->nakshatra,
                'kala' => $this->kala,
                'tradition' => $this->tradition,
            ],
            'start_time' => $this->start_time ? $this->start_time->toIso8601String() : null,
            'end_time' => $this->end_time ? $this->end_time->toIso8601String() : null,
        ];
    }
}
