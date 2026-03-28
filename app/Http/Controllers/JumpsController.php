<?php

namespace App\Http\Controllers;

use App\Services\TornApiService;
use App\Models\GymStatsHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class JumpsController extends Controller
{
    public function index(TornApiService $tornApi)
    {
        $user = Auth::user();
        $apiKey = $user->torn_api_key;
        $playerId = $user->torn_player_id;

        if (!$apiKey || !$playerId) {
            return view('jumps.index', [
                'error' => 'No API key or player ID found. Please add your Torn API key in Settings.',
                'bars' => null,
                'stats' => null,
                'gym' => null,
            ]);
        }

        // Fetch user bars (happy, energy) using V2
        $bars = $tornApi->getUserBars($apiKey);
        
        // Fetch user gym and battle stats using V1 (same as Gym Assistant)
        try {
            $response = Http::timeout(10)->get('https://api.torn.com/user/' . $playerId, [
                'key' => $apiKey,
                'selections' => 'gym,battlestats'
            ]);

            if ($response->failed()) {
                return view('jumps.index', [
                    'error' => 'Failed to fetch user data from Torn API.',
                    'bars' => null,
                    'stats' => null,
                    'gym' => null,
                ]);
            }

            $data = $response->json();

            if (isset($data['error'])) {
                $errorMsg = is_array($data['error']) ? ($data['error']['error'] ?? json_encode($data['error'])) : $data['error'];
                return view('jumps.index', [
                    'error' => 'API Error: ' . $errorMsg,
                    'bars' => null,
                    'stats' => null,
                    'gym' => null,
                ]);
            }
        } catch (\Exception $e) {
            return view('jumps.index', [
                'error' => 'Error: ' . $e->getMessage(),
                'bars' => null,
                'stats' => null,
                'gym' => null,
            ]);
        }

        // Extract battle stats
        $strength = $data['strength'] ?? 0;
        $defense = $data['defense'] ?? 0;
        $speed = $data['speed'] ?? 0;
        $dexterity = $data['dexterity'] ?? 0;
        $totalStats = $strength + $defense + $speed + $dexterity;

        // Extract gym info
        $gymId = $data['active_gym'] ?? null;
        $gymName = $this->getGymName($gymId);

        // Extract bars
        $happy = $bars['happy'] ?? [];
        $energy = $bars['energy'] ?? [];
        $currentHappy = $happy['current'] ?? 0;
        $maxHappy = $happy['maximum'] ?? 0;

        return view('jumps.index', [
            'error' => null,
            'bars' => $bars,
            'stats' => [
                'strength' => $strength,
                'defense' => $defense,
                'speed' => $speed,
                'dexterity' => $dexterity,
            ],
            'current_happy' => $currentHappy,
            'max_happy' => $maxHappy,
            'current_energy' => $energy['current'] ?? 0,
            'max_energy' => $energy['maximum'] ?? 0,
            'strength' => $strength,
            'defense' => $defense,
            'speed' => $speed,
            'dexterity' => $dexterity,
            'total_stats' => $totalStats,
            'gym_name' => $gymName,
            'gym_id' => $gymId,
        ]);
    }

    private function getGymName(?int $gymId): string
    {
        if (!$gymId) return 'No Gym';
        
        $gymNames = [
            1 => 'Premier Fitness',
            2 => 'Average Joes',
            3 => "Woody's Workout",
            4 => 'Beach Bods',
            5 => 'Silver Gym',
            6 => 'Pour Femme',
            7 => 'Davies Den',
            8 => 'Global Gym',
            9 => 'Knuckle Heads',
            10 => 'Pioneer Fitness',
            11 => 'Anabolic Anomalies',
            12 => 'Core',
            13 => 'Racing Fitness',
            14 => 'Complete Cardio',
            15 => 'Legs, Bums and Tums',
            16 => 'Deep Burn',
            17 => 'Apollo Gym',
            18 => 'Gun Shop',
            19 => 'Force Training',
            20 => "Cha Cha's",
            21 => 'Atlas',
            22 => 'Last Round',
            23 => 'The Edge',
            24 => "George's",
            25 => 'Balboas Gym',
        ];
        
        return $gymNames[$gymId] ?? 'Unknown Gym';
    }
}
