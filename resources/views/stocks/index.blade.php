@extends('layouts.app')

@section('title', 'Stocks - TornOps')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Stock Market</h1>
            <p class="text-gray-400">Current market values from Torn</p>
        </div>
        <form action="/stocks/update" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Refresh Data
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-900/50 border border-green-700 rounded-lg text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if($error)
        <div class="mb-4 p-4 bg-red-900/50 border border-red-700 rounded-lg text-red-400">
            {{ $error }}
        </div>
    @endif

    @if(empty($stocks))
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-8 text-center">
            <p class="text-gray-400">No stock data available. Click refresh to load.</p>
        </div>
    @else
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-700">
                        <tr class="text-left text-gray-400 text-sm">
                            <th class="p-3">ID</th>
                            <th class="p-3">Stock</th>
                            <th class="p-3 text-right">Price</th>
                            <th class="p-3 text-right">Market Cap</th>
                            <th class="p-3 text-right">Investors</th>
                            <th class="p-3 text-right">Shares</th>
                            <th class="p-3">Bonus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($stocks as $stock)
                        <tr class="hover:bg-gray-700/30">
                            <td class="p-3 font-mono text-gray-400">{{ $stock['id'] }}</td>
                            <td class="p-3">
                                <div class="flex items-center gap-2">
                                    @if($stock['logo'])
                                        <img src="{{ $stock['logo'] }}" class="w-6 h-6" alt="">
                                    @endif
                                    <div>
                                        <div class="font-medium">{{ $stock['name'] }}</div>
                                        <div class="text-gray-500 text-sm">{{ $stock['acronym'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3 text-right font-mono">${{ number_format($stock['price'], 2) }}</td>
                            <td class="p-3 text-right font-mono text-gray-400">${{ number_format($stock['market_cap']) }}</td>
                            <td class="p-3 text-right font-mono text-gray-400">{{ number_format($stock['volume']) }}</td>
                            <td class="p-3 text-right font-mono text-gray-400">{{ number_format($stock['shares']) }}</td>
                            <td class="p-3 text-xs">
                                @if($stock['bonus'])
                                    <span class="px-2 py-1 rounded bg-blue-900/50 text-blue-400">
                                        {{ $stock['bonus']['description'] ?? 'Bonus' }}
                                    </span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
