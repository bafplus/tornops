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
            $factions = $war['factions'] ?? [];
            $warInfo = $war['war'] ?? [];

            $opponentId = null;
            $opponentName = null;
            $scoreOurs = null;
            $scoreThem = null;

            foreach ($factions as $oppId => $oppData) {
                if ((string)$oppId !== (string)$factionId) {
                    $opponentId = $oppId;
                    $opponentName = $oppData['name'] ?? null;
                    $scoreThem = $oppData['score'] ?? null;
                } else {
                    $scoreOurs = $oppData['score'] ?? null;
                }
            }

            $status = 'pending';
            if (isset($warInfo['winner']) && $warInfo['winner'] > 0) {
                $status = ((string)$warInfo['winner'] === (string)$factionId) ? 'won' : 'lost';
            } elseif ($scoreOurs > 0 || $scoreThem > 0) {
                $status = 'in progress';
            }

            RankedWar::updateOrCreate(
                [
                    'war_id' => $warId,
                    'faction_id' => $factionId,
                ],
                [
                    'opponent_faction_id' => $opponentId,
                    'opponent_faction_name' => $opponentName,
                    'status' => $status,
                    'start_date' => isset($warInfo['start']) ? now()->createFromTimestamp($warInfo['start']) : null,
                    'end_date' => isset($warInfo['end']) && $warInfo['end'] > 0 ? now()->createFromTimestamp($warInfo['end']) : null,
                    'score_ours' => $scoreOurs,
                    'score_them' => $scoreThem,
                    'data' => $war,
                ]
            );
            $count++;
        }

        $this->info("Synced {$count} ranked wars.");
        return Command::SUCCESS;
    }
}
