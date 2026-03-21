<?php

namespace App\Console\Commands;

use App\Models\FactionSettings;
use App\Models\RankedWar;
use App\Services\TornApiService;
use Illuminate\Console\Command;

class SyncRankedWars extends Command
{
    protected $signature = 'torn:sync-wars {faction_id?}';
    protected $description = 'Sync ranked wars from Torn API';

    public function handle(TornApiService $tornApi): int
    {
        $factionId = $this->argument('faction_id') ?? FactionSettings::value('faction_id');

        if (!$factionId) {
            $this->error('No faction ID provided or configured.');
            return Command::FAILURE;
        }

        $this->info("Syncing ranked wars for faction {$factionId}...");

        $data = $tornApi->getRankedWars($factionId);

        if (!$data || !isset($data['rankedwars'])) {
            $this->error('Failed to fetch ranked wars.');
            return Command::FAILURE;
        }

        $count = 0;
        foreach ($data['rankedwars'] as $warId => $war) {
            RankedWar::updateOrCreate(
                [
                    'war_id' => $warId,
                    'faction_id' => $factionId,
                ],
                [
                    'opponent_faction_id' => $war['opponent_factionid'] ?? null,
                    'opponent_faction_name' => $war['opponent_factionname'] ?? null,
                    'status' => $war['status'] ?? 'pending',
                    'start_date' => isset($war['start']) ? now()->createFromTimestamp($war['start']) : null,
                    'end_date' => isset($war['end']) ? now()->createFromTimestamp($war['end']) : null,
                    'score_ours' => $war['score_ours'] ?? null,
                    'score_them' => $war['score_them'] ?? null,
                    'data' => $war,
                ]
            );
            $count++;
        }

        $this->info("Synced {$count} ranked wars.");
        return Command::SUCCESS;
    }
}
