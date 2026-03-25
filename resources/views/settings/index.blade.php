@extends('layouts.app')

@section('title', 'Settings - TornOps')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <h1 class="text-3xl font-bold">Settings</h1>

    @if(session('status'))
        <div class="bg-green-900/50 border border-green-700 text-green-400 px-4 py-3 rounded">
            {{ session('status') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-yellow-900/50 border border-yellow-700 text-yellow-400 px-4 py-3 rounded">
            {{ session('warning') }}
        </div>
    @endif

    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
        <h2 class="text-xl font-semibold mb-4">Account Information</h2>
        <div class="space-y-3">
            <div class="flex items-center justify-between py-2 border-b border-gray-700">
                <span class="text-gray-400">Username</span>
                <span class="text-white font-medium">{{ auth()->user()->name }}</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-gray-700">
                <span class="text-gray-400">Player ID</span>
                <span class="text-white font-mono">{{ auth()->user()->torn_player_id }}</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-gray-700">
                <span class="text-gray-400">Status</span>
                <span class="px-2 py-1 rounded text-xs 
                    {{ auth()->user()->status === 'active' ? 'bg-green-900 text-green-400' : 'bg-yellow-900 text-yellow-400' }}">
                    {{ ucfirst(auth()->user()->status) }}
                </span>
            </div>
        </div>
        <p class="text-gray-500 text-sm mt-4">
            Contact an admin to change your username or player ID.
        </p>
    </div>

    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
        <h2 class="text-xl font-semibold mb-4">Personal API Key</h2>
        <p class="text-gray-400 text-sm mb-4">
            Add your personal Torn API key for enhanced functionality.
        </p>
        <form action="/settings/api-key" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-gray-400 mb-2">Torn API Key</label>
                <input type="text" name="torn_api_key" value="{{ auth()->user()->torn_api_key ?? '' }}" 
                       placeholder="Enter your Torn API key" 
                       class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-2 text-white focus:outline-none focus:border-blue-500 font-mono">
                @error('torn_api_key')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded text-white">
                Save API Key
            </button>
        </form>
    </div>

    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
        <h2 class="text-xl font-semibold mb-4">Change Password</h2>
        <form action="/settings/password" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-gray-400 mb-2">Current Password</label>
                <input type="password" name="current_password" required class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                @error('current_password')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-gray-400 mb-2">New Password</label>
                <input type="password" name="password" required class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                @error('password')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-gray-400 mb-2">Confirm New Password</label>
                <input type="password" name="password_confirmation" required class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-2 text-white focus:outline-none focus:border-blue-500">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded text-white">
                Update Password
            </button>
        </form>
    </div>
</div>
@endsection