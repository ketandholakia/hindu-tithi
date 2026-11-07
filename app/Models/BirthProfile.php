<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirthProfile extends Model
{
    protected $fillable = ['user_id', 'name', 'birth_date', 'birth_time', 'timezone', 'latitude', 'longitude', 'elevation_meters'];

    protected function casts(): array
    {
        return ['birth_date' => 'date', 'latitude' => 'float', 'longitude' => 'float', 'elevation_meters' => 'float'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
