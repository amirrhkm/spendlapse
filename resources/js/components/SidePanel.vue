<template>
  <div class="fixed left-0 top-0 h-full w-64 bg-gray-900 border-r border-gray-700 transform transition-transform duration-300 ease-in-out z-50"
       :class="{ 'translate-x-0': isOpen, '-translate-x-full': !isOpen }">
    
    <!-- Panel Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-700">
      <h2 class="text-xl font-bold text-white">Navigation</h2>
      <button 
        @click="closePanel"
        class="text-gray-400 hover:text-white transition-colors"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    
    <!-- Navigation Links -->
    <nav class="p-4">
      <ul class="space-y-2">
        <li>
          <router-link 
            to="/"
            @click="closePanel"
            class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors"
            :class="{ 'bg-blue-600 text-white': $route.path === '/' }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
            </svg>
            Dashboard
          </router-link>
        </li>
        
        <li>
          <router-link 
            to="/prefixes"
            @click="closePanel"
            class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors"
            :class="{ 'bg-purple-600 text-white': $route.path === '/prefixes' }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            Categories
          </router-link>
        </li>
      </ul>
    </nav>
    
    <!-- Quick Stats -->
    <div class="p-4 border-t border-gray-700">
      <h3 class="text-sm font-medium text-gray-400 mb-3">Quick Stats</h3>
      <div class="space-y-2 text-xs">
        <div class="flex justify-between">
          <span class="text-gray-500">Categories:</span>
          <span class="text-white">{{ categoryCount }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-500">Transactions:</span>
          <span class="text-white">{{ transactionCount }}</span>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Overlay -->
  <div 
    v-if="isOpen"
    @click="closePanel"
    class="fixed inset-0 bg-black bg-opacity-50 z-40"
  ></div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  transactionCount: {
    type: Number,
    default: 0
  }
});

const emit = defineEmits(['close']);

const closePanel = () => {
  emit('close');
};

// Get category count from localStorage
const categoryCount = computed(() => {
  const savedPrefixes = localStorage.getItem('customPrefixes');
  if (savedPrefixes) {
    return JSON.parse(savedPrefixes).length;
  }
  return 0;
});
</script>
