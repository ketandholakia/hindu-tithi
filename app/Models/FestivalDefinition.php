<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FestivalDefinition extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name_en',
        'name_gu',
        'name_hi',
        'category',
        'description',
        'enabled',
    ];

    public function rules()
    {
        return $this->hasMany(FestivalRule::class, 'festival_id');
    }

    public function aliases()
    {
        return $this->hasMany(FestivalAlias::class, 'festival_id');
    }

    public function occurrences()
    {
        return $this->hasMany(FestivalOccurrence::class, 'festival_id');
    }
}
