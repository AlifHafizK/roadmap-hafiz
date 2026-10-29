<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = [
        'title',
        'description',
        'remind_at',
        'notified_at',
        'status',
        'dispatched_at',
    ];

    protected function casts(): array
    {
        return [
            'remind_at' => 'datetime',
            'notified_at' => 'datetime',
            'dispatched_at' => 'datetime',
        ];
    }
}
