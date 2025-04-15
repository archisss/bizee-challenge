<?php

namespace Database\Seeders;

use App\Helpers\States;
use App\Models\RegisteredAgent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegisteredAgentsTableSeeder extends Seeder
{
    public function run(): void
    {
        $states = States::all();

        foreach ($states as $state) {
            if ($state === 'Illinois') {
                continue; // No agents
            }

            $agentsCount = match ($state) {
                'California', 'Texas' => 2,
                default => 1,
            };

            for ($i = 1; $i <= $agentsCount; $i++) {
                RegisteredAgent::create([
                    'state' => $state,
                    'name' => fake()->name(),
                    'email' => fake()->unique()->safeEmail(),
                    'capacity' => rand(5, 15),
                ]);
            }
        }
    }
}
