<?php

namespace App\Console\Commands;

use App\Models\FactionSettings;
use App\Models\FactionMember;
use App\Services\TornApiService;
use Illuminate\Console\Command;

class SyncFactionMembers extends Command
{
    protected $signature = 'torn:sync-members {faction_id?}';
    protected $description = 'Sync faction members from Torn API';

    public function handle(TornApiService $tornApi): int
    {
        $factionId = $this->argument('faction_id') ?? FactionSettings::value('faction_id');

        if (!$factionId) {
            $this->error('No faction ID provided or configured.');
            return Command::FAILURE;
        }

        $this->info("Syncing members for faction {$factionId}...");

        $data = $tornApi->getFactionMembers($factionId);

        if (!$data || !isset($data['members'])) {
            $this->error('Failed to fetch faction members.');
            return Command::FAILURE;
        }

        $count = 0;
        foreach ($data['members'] as $playerId => $member) {
            FactionMember::updateOrCreate(
                [
                    'faction_id' => $factionId,
                    'player_id' => $playerId,
                ],
                [
                    'name' => $member['name'] ?? null,
                    'level' => $member['level'] ?? 1,
                    'rank' => $member['rank'] ?? null,
                    'days_in_faction' => $member['days_in_faction'] ?? null,
                    'data' => $member,
                    'last_synced_at' => now(),
                ]
            );
            $count++;
        }

        $this->info("Synced {$count} members.");
        return Command::SUCCESS;
    }
}
