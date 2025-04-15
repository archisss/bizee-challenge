<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class RegisteredAgent extends Model
{
    use Notifiable;
    protected $fillable = [
        'state',
        'name',
        'email',
        'capacity'
    ];
    public function companies()
    {
        return $this->hasMany(Company::class, 'registered_agent_id')->where('registered_agent_type', 'registered_agent');
    }

}
