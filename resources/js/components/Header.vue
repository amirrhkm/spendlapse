<template>
  <header class="bg-black shadow-lg border-b border-gray-800">
    <div class="container mx-auto px-4 py-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-white">SpendLapse</h1>
            <p class="text-gray-400 text-sm">Bank Statement Analyzer</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-4">
          <div class="text-sm text-gray-400">
            Last updated: {{ new Date().toLocaleDateString() }}
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
// No additional logic needed for now
</script>
