<?php

namespace App\Http\Controllers;

use App\Services\TornApiService;
use Illuminate\Support\Facades\Cache;

class StocksController extends Controller
{
    public function index(TornApiService $tornApi)
    {
        $stocks = Cache::remember('stocks_data', 1800, function () use ($tornApi) {
            return $tornApi->getStocks();
        });

        if (!$stocks) {
            return view('stocks.index', [
                'error' => 'Failed to fetch stock data from Torn API',
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
        Cache::forget('stocks_data');
        $stocks = $tornApi->getStocks();
        
        if (!$stocks) {
            return back()->with('error', 'Failed to fetch stock data from Torn API');
        }

        return back()->with('success', 'Stock data updated!');
    }
}
