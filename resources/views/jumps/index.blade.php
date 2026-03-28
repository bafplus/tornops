@extends('layouts.app')

@section('title', 'Jump Helper - TornOps')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Jump Helper</h1>
        <p class="text-gray-400">Happy jump calculator - calculate stat gains</p>
    </div>

    @if($error)
        <div class="mb-4 p-4 bg-red-900/50 border border-red-700 rounded-lg text-red-400">
            {{ $error }}
        </div>
    @endif

    @if($stats && $bars)
    <!-- Gym Info -->
    <div class="mb-6 bg-gray-800 rounded-lg border border-purple-700/50 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-purple-900/50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <div>
                    <div class="font-semibold text-purple-400 text-lg">{{ $gym_name }}</div>
                    <div class="text-gray-500 text-sm">Gym ID: {{ $gym_id ?? 'None' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
            <h2 class="text-lg font-semibold mb-4 text-blue-400">Battle Stats</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-400">Strength</span>
                    <span class="font-mono">{{ number_format($strength) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Defense</span>
                    <span class="font-mono">{{ number_format($defense) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Speed</span>
                    <span class="font-mono">{{ number_format($speed) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Dexterity</span>
                    <span class="font-mono">{{ number_format($dexterity) }}</span>
                </div>
                <div class="flex justify-between border-t border-gray-700 pt-2">
                    <span class="text-white font-semibold">Total Stats</span>
                    <span class="font-mono text-yellow-400">{{ number_format($total_stats) }}</span>
                </div>
            </div>
        </div>

        <!-- Current Bars -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
            <h2 class="text-lg font-semibold mb-4 text-green-400">Bars</h2>
            
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-gray-400">Happy</span>
                        <div class="text-right">
                            <span class="font-mono text-green-400">{{ number_format($current_happy) }}</span>
                            <span class="text-gray-500"> / {{ number_format($max_happy) }}</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-3">
                        <div class="bg-green-500 h-3 rounded-full transition-all" style="width: {{ min(100, $current_happy / $max_happy * 100) }}%"></div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ floor($current_happy / 250) }} jumps available (250 each)</div>
                </div>
                
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-gray-400">Energy</span>
                        <div class="text-right">
                            <span class="font-mono text-blue-400">{{ number_format($current_energy) }}</span>
                            <span class="text-gray-500"> / {{ number_format($max_energy) }}</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-3">
                        <div class="bg-blue-500 h-3 rounded-full transition-all" style="width: {{ min(100, $current_energy / $max_energy * 100) }}%"></div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ floor($current_energy / 100) }} gym hits available (100 each)</div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 p-4 bg-yellow-900/20 border border-yellow-700/50 rounded-lg">
        <p class="text-yellow-400 text-sm">Calculator coming soon - ready to build!</p>
    </div>
    @endif
</div>
@endsection
