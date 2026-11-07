<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramReminder extends Model
{
    protected $fillable = [
        'chat_id',
        'type',
        'send_time',
        'timezone',
        'latitude',
        'longitude',
        'language',
        'last_sent_on',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'last_sent_on' => 'date',
        ];
    }
}
