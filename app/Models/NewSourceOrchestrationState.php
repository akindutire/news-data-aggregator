<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewSourceOrchestrationState extends Model
{
    protected $fillable = [
        'source',
        'last_fetched_page',
        'last_fetched_at',
    ];
}
