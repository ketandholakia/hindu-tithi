<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PanchangLocation extends Model
{
    protected $fillable = ['user_id', 'name', 'latitude', 'longitude', 'elevation_meters', 'timezone'];

    protected function casts(): array
    {
        return ['latitude' => 'float', 'longitude' => 'float', 'elevation_meters' => 'float'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
