<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FFScouterService
{
    private string $baseUrl = 'https://ffscouter.com/api.php';
    private int $cacheTtl = 3600;

    public function getPlayerStats(int $playerId): ?array
    {
        $cacheKey = 'ffscouter_player_' . $playerId;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($playerId) {
            $response = Http::timeout(10)
                ->get($this->baseUrl, [
                    'action' => 'playerstats',
                    'player_id' => $playerId
                ]);

            if ($response->failed()) {
                Log::error('FFScouter API Error', [
                    'player_id' => $playerId,
                    'status' => $response->status()
                ]);
                return null;
            }

            $data = $response->json();

            if (isset($data['error'])) {
                Log::error('FFScouter API Error', [
                    'player_id' => $playerId,
                    'error' => $data['error']
                ]);
                return null;
            }

            return $data;
        });
    }

    public function getBSSPublic(int $playerId): ?float
    {
        $stats = $this->getPlayerStats($playerId);
        return $stats['bss_public'] ?? null;
    }

    public function clearCache(int $playerId = null): void
    {
        if ($playerId) {
            Cache::forget('ffscouter_player_' . $playerId);
        } else {
            Cache::flush();
        }
    }
}
