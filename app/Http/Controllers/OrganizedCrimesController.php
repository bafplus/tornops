<?php

namespace App\Http\Controllers;

use App\Models\FactionSettings;
use App\Models\OrganizedCrime;
use App\Models\FactionMember;
use Illuminate\Support\Facades\View;

class OrganizedCrimesController extends Controller
{
    public function index()
    {
        $factionId = FactionSettings::value('faction_id');
        
        $ocs = OrganizedCrime::where('faction_id', $factionId)
            ->with('slots')
            ->orderByDesc('oc_created_at')
            ->limit(50)
            ->get();
        
        $playerIds = [];
        foreach ($ocs as $oc) {
            foreach ($oc->slots as $slot) {
                if ($slot->user_id) {
                    $playerIds[] = $slot->user_id;
                }
            }
        }
        
        $members = FactionMember::where('faction_id', $factionId)
            ->whereIn('player_id', array_unique($playerIds))
            ->get()
            ->keyBy('player_id');
        
        foreach ($ocs as $oc) {
            foreach ($oc->slots as $slot) {
                $slot->member_name = $members->get($slot->user_id)?->name ?? null;
            }
        }
        
        return view('organized-crimes.index', compact('ocs'));
    }
}
