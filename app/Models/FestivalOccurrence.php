<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FestivalOccurrence extends Model
{
    use HasFactory;

    protected $fillable = [
        'festival_id',
        'date',
        'location_id',
        'calendar_system',
        'rule_id',
        'tithi',
        'nakshatra',
        'start_time',
        'end_time',
        'kala',
        'tradition',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function definition()
    {
        return $this->belongsTo(FestivalDefinition::class, 'festival_id');
    }

    public function rule()
    {
        return $this->belongsTo(FestivalRule::class, 'rule_id');
    }
}
