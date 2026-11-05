<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledJobError extends Model
{
    protected $fillable = [
        'command',
        'error_message',
        'failed_at',
    ];
}