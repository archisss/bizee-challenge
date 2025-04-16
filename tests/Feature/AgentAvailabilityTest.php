<?php

namespace Tests\Feature;

use App\Models\RegisteredAgent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AgentAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_availability_true_if_capacity_remaining()
    {
        $agent = RegisteredAgent::create([
            'state' => 'Florida',
            'name' => 'Agent F',
            'email' => 'agentf@example.com',
            'capacity' => 10,
        ]);

        $response = $this->getJson('/api/agent-availability/Florida');

        $response->assertStatus(200)
                 ->assertJson([
                     'available' => true,
                 ]);
    }

    public function test_availability_false_if_no_agents()
    {
        $response = $this->getJson('/api/agent-availability/Illinois');

        $response->assertStatus(200)
                 ->assertJson([
                     'available' => false,
                 ]);
    }
}
