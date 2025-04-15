<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\RegisteredAgent;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $companiesCount = rand(1, 3);

            for ($i = 0; $i < $companiesCount; $i++) {
                $state = fake()->state();

                // 50% probability use registeres agent or user
                if (rand(0, 1)) {
                    // Use registered agent if available
                    $agent = RegisteredAgent::where('state', $state)->inRandomOrder()->first();
                    if ($agent) {
                        Company::create([
                            'user_id' => $user->id,
                            'name' => fake()->company(),
                            'state' => $state,
                            'registered_agent_type' => 'registered_agent',
                            'registered_agent_id' => $agent->id,
                        ]);
                        continue;
                    }
                }

                // Use user as agente
                Company::create([
                    'user_id' => $user->id,
                    'name' => fake()->company(),
                    'state' => $state,
                    'registered_agent_type' => 'user',
                    'registered_agent_id' => $user->id,
                ]);
            }
        }
    }
}
