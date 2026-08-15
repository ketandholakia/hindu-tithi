<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FestivalAlias extends Model
{
    use HasFactory;

    protected $fillable = [
        'festival_id',
        'region',
        'language',
        'name',
    ];

    public function definition()
    {
        return $this->belongsTo(FestivalDefinition::class, 'festival_id');
    }
}
