<?php

namespace Tests\Feature;

use App\Models\RegisteredAgent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use App\Events\RegisteredAgentAssigned;
use App\Models\Company;
use App\Notifications\AgentAssigned;
use App\Notifications\StateCapacityReached;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_company_with_registered_agent()
    {
        $user = User::factory()->create();

        $agent = RegisteredAgent::create([
            'state' => 'California',
            'name' => 'Agent X',
            'email' => 'agent@example.com',
            'capacity' => 10,
        ]);

        $response = $this->postJson('/api/companies', [
            'user_id' => $user->id,
            'name' => 'Test Company',
            'state' => 'California',
            'use_registered_agent_service' => true,
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                    'name' => 'Test Company',
                    'state' => 'California',
                    'registered_agent_type' => 'registered_agent',
                 ]);
    }

    public function test_user_can_create_company_as_own_agent()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/companies', [
            'user_id' => $user->id,
            'name' => 'My Company',
            'state' => 'Nevada',
            'use_registered_agent_service' => false,
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'name' => 'My Company',
                     'registered_agent_type' => 'user',
                     'registered_agent_id' => $user->id,
                 ]);
    }

    public function test_event_dispatched_when_agent_assigned()
    {
        Event::fake();

        $user = User::factory()->create();

        $agent = RegisteredAgent::create([
            'state' => 'Texas',
            'name' => 'Agente Z',
            'email' => 'z@example.com',
            'capacity' => 5,
        ]);

        $this->postJson('/api/companies', [
            'user_id' => $user->id,
            'name' => 'TestCorp Z',
            'state' => 'Texas',
            'use_registered_agent_service' => true,
        ]);

        Event::assertDispatched(RegisteredAgentAssigned::class);
    }

    public function test_admin_is_notified_when_capacity_reaches_90_percent()
    {
        Notification::fake();

        $user = User::factory()->create();

        $agent = RegisteredAgent::create([
            'state' => 'Georgia',
            'name' => 'Agente G',
            'email' => 'agente@example.com',
            'capacity' => 15,
        ]);

        for ($i = 0; $i < 14; $i++) {
            Company::create([
                'user_id' => $user->id,
                'name' => 'Fake Company ' . $i,
                'state' => 'Georgia',
                'registered_agent_type' => 'registered_agent',
                'registered_agent_id' => $agent->id,
            ]);
        }

        $this->postJson('/api/companies', [
            'user_id' => $user->id,
            'name' => 'Testing Company Inc',
            'state' => 'Georgia',
            'use_registered_agent_service' => true,
        ]);

        Notification::assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable,
            StateCapacityReached::class,
            function ($notification, $channels, $notifiable) {
                return $notifiable->routes['mail'] === 'admin@bizee.test';
            }
        );
    }

}
