<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FestivalRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'festival_id',
        'rule_type',
        'month',
        'paksha',
        'tithi',
        'nakshatra',
        'weekday',
        'required_kala',
        'priority',
        'tradition',
        'region',
    ];

    public function definition()
    {
        return $this->belongsTo(FestivalDefinition::class, 'festival_id');
    }
}
