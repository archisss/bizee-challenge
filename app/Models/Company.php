<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'state',
        'registered_agent_type',
        'registered_agent_id',
    ];
}
