<?php

namespace App\Listeners;

use App\Events\RegisteredAgentAssigned;
use App\Models\RegisteredAgent;
use App\Notifications\AgentAssigned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyRegisteredAgent
{
    public function __construct()
    {
        //
    }

    public function handle(RegisteredAgentAssigned $event): void
    {
        if ($event->company->registered_agent_type === 'registered_agent') {
            $agent = RegisteredAgent::find($event->company->registered_agent_id);
            $agent?->notify(new AgentAssigned($event->company));
        }
    }
}
