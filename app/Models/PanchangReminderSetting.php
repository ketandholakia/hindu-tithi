<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PanchangReminderSetting extends Model
{
    protected $fillable = ['user_id', 'enabled', 'send_time', 'timezone', 'latitude', 'longitude', 'elevation_meters', 'notify_ekadashi', 'notify_purnima', 'notify_amavasya', 'notify_festivals', 'last_sent_on'];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean', 'notify_ekadashi' => 'boolean', 'notify_purnima' => 'boolean',
            'notify_amavasya' => 'boolean', 'notify_festivals' => 'boolean',
            'latitude' => 'float', 'longitude' => 'float', 'elevation_meters' => 'float',
            'last_sent_on' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
