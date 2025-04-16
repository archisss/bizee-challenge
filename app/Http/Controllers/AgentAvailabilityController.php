<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RegisteredAgent;
class AgentAvailabilityController extends Controller
{
    public function check($state)
    {   
        $agents = RegisteredAgent::where('state', $state)->get();
        
        if ($agents->isEmpty()) {
            return response()->json(['available' => false, 'message' => 'No agents available in this state']);
        }

        $totalCapacity = $agents->sum('capacity');
        $currentLoad = $agents->sum(fn ($agent) => $agent->companies()->count());
        
        $available = $currentLoad < $totalCapacity;

        return response()->json([
            'available' => $available,
            'used_capacity_percent' => round(($currentLoad / $totalCapacity) * 100, 2),
        ]);
    }

}
