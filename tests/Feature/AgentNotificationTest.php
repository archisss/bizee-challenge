<?php

namespace Tests\Feature;

use App\Events\RegisteredAgentAssigned;
use App\Models\Company;
use App\Models\RegisteredAgent;
use App\Models\User;
use App\Notifications\AgentAssigned;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AgentNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_receives_notification_when_assigned()
    {
        Notification::fake();

        $user = User::factory()->create();

        $agent = RegisteredAgent::create([
            'state' => 'Florida',
            'name' => 'Fake Agente JC',
            'email' => 'agentea@example.com',
            'capacity' => 5,
        ]);

        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'New Florida LLC',
            'state' => 'Florida',
            'registered_agent_type' => 'registered_agent',
            'registered_agent_id' => $agent->id,
        ]);

        event(new RegisteredAgentAssigned($company));

        Notification::assertSentTo($agent, AgentAssigned::class);
    }
}
