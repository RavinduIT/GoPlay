<?php $_base = defined('BASE_URL') ? BASE_URL : ''; ?>
<div class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="max-w-md mx-auto text-center">
        <div class="mb-8">
            <i class="fas fa-server text-red-500 text-6xl mb-4"></i>
            <h1 class="text-6xl font-bold text-gray-800 mb-4">500</h1>
            <h2 class="text-2xl font-semibold text-gray-600 mb-4">Internal Server Error</h2>
            <p class="text-gray-500 mb-8">
                Something went wrong on our end. We're working to fix this issue.
            </p>
        </div>
        
        <div class="space-y-4">
            <a href="<?= $_base ?>/" class="block bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                Go Home
            </a>
            <button onclick="location.reload()" class="block w-full bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-700 transition-colors">
                Try Again
            </button>
        </div>
        
        <div class="mt-8 text-sm text-gray-500">
            <p>If this problem persists, <a href="<?= $_base ?>/contact" class="text-blue-600 hover:text-blue-800">contact our support team</a></p>
        </div>
    </div>
</div>