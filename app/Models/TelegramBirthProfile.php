<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramBirthProfile extends Model
{
    protected $fillable = [
        'chat_id',
        'user_id',
        'birth_date',
        'birth_time',
        'timezone',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }
}
