<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TornOps - Torn City Faction Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-6xl font-bold mb-4 bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
                TornOps
            </h1>
            <p class="text-2xl text-gray-400 mb-8">
                Torn City Faction Member Portal
            </p>
            
            <div class="bg-gray-800 rounded-lg p-8 mb-8">
                <h2 class="text-xl font-semibold mb-4 text-blue-400">System Status</h2>
                <div class="grid grid-cols-2 gap-4 text-left">
                    <div class="bg-gray-700 p-4 rounded">
                        <span class="text-gray-400">Database:</span>
                        <span class="text-yellow-400 ml-2">Pending Setup</span>
                    </div>
                    <div class="bg-gray-700 p-4 rounded">
                        <span class="text-gray-400">API Key:</span>
                        <span class="text-yellow-400 ml-2">Not Configured</span>
                    </div>
                    <div class="bg-gray-700 p-4 rounded">
                        <span class="text-gray-400">Faction ID:</span>
                        <span class="text-yellow-400 ml-2">Not Set</span>
                    </div>
                    <div class="bg-gray-700 p-4 rounded">
                        <span class="text-gray-400">Laravel:</span>
                        <span class="text-green-400 ml-2">Installed</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-8">
                <h2 class="text-xl font-semibold mb-4 text-purple-400">Next Steps</h2>
                <ol class="text-left space-y-2 text-gray-300">
                    <li>1. Run <code class="bg-gray-700 px-2 py-1 rounded">composer install</code></li>
                    <li>2. Copy <code class="bg-gray-700 px-2 py-1 rounded">.env.example</code> to <code class="bg-gray-700 px-2 py-1 rounded">.env</code></li>
                    <li>3. Configure your Torn API key in <code class="bg-gray-700 px-2 py-1 rounded">.env</code></li>
                    <li>4. Run <code class="bg-gray-700 px-2 py-1 rounded">php artisan key:generate</code></li>
                    <li>5. Run <code class="bg-gray-700 px-2 py-1 rounded">php artisan migrate</code></li>
                    <li>6. Access the admin panel at <code class="bg-gray-700 px-2 py-1 rounded">/admin</code></li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>
