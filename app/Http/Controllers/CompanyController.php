<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\RegisteredAgent;
use App\Models\User;
use App\Events\RegisteredAgentAssigned;
use App\Notifications\StateCapacityReached;
use Illuminate\Support\Facades\Notification;

class CompanyController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string',
            'state' => 'required|string',
            'use_registered_agent_service' => 'required|boolean',
        ]);

        if ($data['use_registered_agent_service']) {
            $agents = RegisteredAgent::where('state', $data['state'])->get();

            if ($agents->isEmpty()) {
                return response()->json(['message' => 'No registered agent available in this state'], 400);
            }

            $agent = $agents->sortBy(fn ($a) => $a->companies()->count())->first();

            if ($agent->companies()->count() >= $agent->capacity) {
                return response()->json(['message' => 'All agents in this state are at full capacity'], 400);
            }

            $company = Company::create([
                'user_id' => $data['user_id'],
                'name' => $data['name'],
                'state' => $data['state'],
                'registered_agent_type' => 'registered_agent',
                'registered_agent_id' => $agent->id,
            ]);

            event(new RegisteredAgentAssigned($company));

            // Verified 90%
            $agents = RegisteredAgent::where('state', $data['state'])->get();
            $total = $agents->sum('capacity');
            $used = $agents->sum(fn ($a) => $a->companies()->count());

            if (($used / $total) >= 0.9) {
                Notification::route('mail', 'admin@bizee.test')
                    ->notify(new StateCapacityReached($data['state'], round(($used / $total) * 100, 2)));
            }

            return response()->json($company, 201);
        } else {
            $company = Company::create([
                'user_id' => $data['user_id'],
                'name' => $data['name'],
                'state' => $data['state'],
                'registered_agent_type' => 'user',
                'registered_agent_id' => $data['user_id'],
            ]);

            return response()->json($company, 201);
        }
    }

    public function updateRegisteredAgent(Request $request, Company $company)
    {
        $data = $request->validate([
            'use_registered_agent_service' => 'required|boolean',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($company->user_id != $data['user_id']) {
            return response()->json(['message' => 'You can only update your own companies'], 403);
        }

        if ($data['use_registered_agent_service']) {
            $agents = RegisteredAgent::where('state', $company->state)->get();

            if ($agents->isEmpty()) {
                return response()->json(['message' => 'No agent available in this state'], 400);
            }

            $agent = $agents->sortBy(fn ($a) => $a->companies()->count())->first();

            if ($agent->companies()->count() >= $agent->capacity) {
                return response()->json(['message' => 'No capacity in this state'], 400);
            }

            $company->update([
                'registered_agent_type' => 'registered_agent',
                'registered_agent_id' => $agent->id,
            ]);

            return response()->json($company);
        } else {
            $company->update([
                'registered_agent_type' => 'user',
                'registered_agent_id' => $data['user_id'],
            ]);

            return response()->json($company);
        }
    }

    
}
