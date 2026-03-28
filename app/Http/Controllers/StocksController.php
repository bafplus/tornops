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

        // Debug: log the first stock to see structure
        if ($rawStocks && count($rawStocks) > 0) {
            \Illuminate\Support\Facades\Log::info('Stock API response sample', ['first' => array_first($rawStocks)]);
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
            
            return [
                'id' => $stock['id'] ?? 0,
                'acronym' => $stock['acronym'] ?? '',
                'name' => $stock['name'] ?? '',
                'price' => $price,
                'previous_price' => $price,
                'market_cap' => $market['cap'] ?? 0,
                'volume' => $market['investors'] ?? 0,
                'shares' => $market['shares'] ?? 0,
                'bonus' => $stock['bonus'] ?? null,
                'logo' => $stock['images']['logo'] ?? null,
                'profit' => 0, // No previous price in v2 API
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
