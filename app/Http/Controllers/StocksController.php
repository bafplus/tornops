<?php

namespace App\Http\Controllers;

use App\Services\TornApiService;
use App\Models\FactionSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class StocksController extends Controller
{
    public function index(TornApiService $tornApi)
    {
        $apiKey = $this->getApiKey();
        
        if (!$apiKey) {
            return view('stocks.index', [
                'error' => 'No API key found. Please add your Torn API key in Settings.',
                'stocks' => []
            ]);
        }
        
        $rawStocks = Cache::remember('stocks_data', 1800, function () use ($tornApi, $apiKey) {
            return $tornApi->getStocks($apiKey);
        });

        // Save to history if not already saved today
        if ($rawStocks) {
            $today = now()->toDateString();
            $existsToday = \App\Models\StockHistory::where('recorded_at', $today)->exists();
            
            if (!$existsToday) {
                foreach ($rawStocks as $stock) {
                    \App\Models\StockHistory::create([
                        'stock_id' => $stock['id'] ?? 0,
                        'name' => $stock['name'] ?? '',
                        'acronym' => $stock['acronym'] ?? '',
                        'price' => $stock['market']['price'] ?? 0,
                        'investors' => $stock['market']['investors'] ?? 0,
                        'shares' => $stock['market']['shares'] ?? 0,
                        'market_cap' => $stock['market']['cap'] ?? 0,
                        'recorded_at' => $today,
                    ]);
                }
            }
        }

        if (!$rawStocks) {
            return view('stocks.index', [
                'error' => 'Failed to fetch stock data. Check your API key has stocks access.',
                'stocks' => []
            ]);
        }

        $stocks = collect($rawStocks)->map(function ($stock) {
            $market = $stock['market'] ?? [];
            $price = $market['price'] ?? 0;
            $bonus = $stock['bonus'] ?? null;
            
            $bonusText = '';
            if ($bonus) {
                $freq = match($bonus['frequency'] ?? 0) {
                    1 => 'daily',
                    7 => 'weekly',
                    14 => 'bi-weekly',
                    31 => 'monthly',
                    91 => 'quarterly',
                    default => 'every ' . ($bonus['frequency'] ?? '?') . ' days'
                };
                $bonusText = [
                    'type' => $bonus['passive'] ? 'Passive' : 'Active',
                    'shares_needed' => number_format($bonus['requirement'] ?? 0),
                    'payout' => $bonus['description'] ?? '',
                    'frequency' => $freq,
                ];
            }
            
            return [
                'id' => $stock['id'] ?? 0,
                'acronym' => $stock['acronym'] ?? '',
                'name' => $stock['name'] ?? '',
                'price' => $price,
                'investors' => $market['investors'] ?? 0,
                'shares' => $market['shares'] ?? 0,
                'bonus_passive' => $bonus['passive'] ?? false,
                'bonus_requirement' => $bonus['requirement'] ?? 0,
                'bonus_payout' => $bonus['description'] ?? '',
                'bonus_frequency' => $bonus['frequency'] ?? 0,
                'bonus_text' => $bonusText,
                'logo' => $stock['images']['logo'] ?? null,
            ];
        })->sortByDesc('shares')->values();

        return view('stocks.index', [
            'stocks' => $stocks,
            'error' => null
        ]);
    }

    public function update(TornApiService $tornApi)
    {
        $apiKey = $this->getApiKey();
        
        if (!$apiKey) {
            return back()->with('error', 'No API key found. Please add your Torn API key in Settings.');
        }

        Cache::forget('stocks_data');
        $stocks = $tornApi->getStocks($apiKey);
        
        if (!$stocks) {
            return back()->with('error', 'Failed to fetch stock data. Check your API key.');
        }

        return back()->with('success', 'Stock data updated!');
    }

    private function getApiKey(): ?string
    {
        // First try logged in user's key
        if (Auth::check() && Auth::user()->torn_api_key) {
            return Auth::user()->torn_api_key;
        }
        
        // Fall back to faction settings key (for scheduled tasks)
        $settings = FactionSettings::first();
        return $settings?->torn_api_key;
    }
}
