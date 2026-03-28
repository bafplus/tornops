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
        
        $stocks = Cache::remember('stocks_data', 1800, function () use ($tornApi, $apiKey) {
            return $tornApi->getStocks($apiKey);
        });

        if (!$stocks) {
            return view('stocks.index', [
                'error' => 'Failed to fetch stock data from Torn API. Please ensure you have a valid API key in Settings.',
                'stocks' => []
            ]);
        }

        $stocks = collect($stocks)->map(function ($stock, $acronym) {
            return [
                'acronym' => $acronym,
                'name' => $stock['name'] ?? $acronym,
                'price' => $stock['current_price'] ?? 0,
                'previous_price' => $stock['previous_price'] ?? 0,
                'market_cap' => $stock['market_cap'] ?? 0,
                'volume' => $stock['volume'] ?? 0,
                'shares' => $stock['total_shares'] ?? 0,
                'profit' => isset($stock['current_price'], $stock['previous_price']) 
                    ? (($stock['current_price'] - $stock['previous_price']) / $stock['previous_price'] * 100)
                    : 0,
            ];
        })->sortByDesc('market_cap')->values();

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
            return back()->with('error', 'Failed to fetch stock data from Torn API. Check your API key.');
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
