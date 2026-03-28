<?php

namespace App\Http\Controllers;

use App\Services\TornApiService;
use Illuminate\Support\Facades\Cache;

class StocksController extends Controller
{
    public function index(TornApiService $tornApi)
    {
        $rawStocks = Cache::remember('stocks_data', 1800, function () use ($tornApi) {
            return $tornApi->getStocks();
        });

        // Debug: log the first stock to see structure
        if ($rawStocks && count($rawStocks) > 0) {
            \Illuminate\Support\Facades\Log::info('Stock API response sample', ['first' => array_first($rawStocks)]);
        }

        if (!$rawStocks) {
            return view('stocks.index', [
                'error' => 'Failed to fetch stock data from Torn API.',
                'stocks' => []
            ]);
        }

        $stocks = collect($rawStocks)->map(function ($stock) {
            $price = $stock['current_price'] ?? 0;
            $prevPrice = $stock['previous_price'] ?? $price;
            $profit = ($prevPrice > 0) ? (($price - $prevPrice) / $prevPrice * 100) : 0;
            
            return [
                'id' => $stock['stock_id'] ?? 0,
                'acronym' => $stock['acronym'] ?? '',
                'name' => $stock['name'] ?? '',
                'price' => $price,
                'previous_price' => $prevPrice,
                'market_cap' => $stock['market_cap'] ?? 0,
                'volume' => $stock['volume'] ?? 0,
                'shares' => $stock['total_shares'] ?? 0,
                'profit' => $profit,
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
            return back()->with('error', 'Failed to fetch stock data from Torn API.');
        }

        return back()->with('success', 'Stock data updated!');
    }
}
