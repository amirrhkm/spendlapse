<template>
  <div class="fixed left-0 top-0 h-full w-64 bg-gray-900 border-r border-gray-700 transform transition-transform duration-300 ease-in-out z-50 flex flex-col"
       :class="{ 'translate-x-0': isOpen, '-translate-x-full': !isOpen }">
    
            <!-- Panel Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-700">
          <h2 class="text-xl font-bold text-white">{{ $t('sidePanel.title') }}</h2>
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
        <nav class="p-4 flex-1">
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
                {{ $t('navigation.dashboard') }}
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
                {{ $t('navigation.categories') }}
              </router-link>
            </li>
          </ul>
        </nav>
        
        <!-- User Info -->
        <div class="p-4 border-t border-gray-700">
          <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <div>
              <div class="text-white font-medium text-sm">{{ currentUser?.name || 'User' }}</div>
              <div class="text-gray-400 text-xs">{{ currentUser?.email || '' }}</div>
            </div>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="p-4 border-t border-gray-700">
          <h3 class="text-sm font-medium text-gray-400 mb-3">{{ $t('sidePanel.quickStats') }}</h3>
          <div class="space-y-2 text-xs">
            <div class="flex justify-between">
              <span class="text-gray-500">{{ $t('sidePanel.categories') }}:</span>
              <span class="text-white">{{ categoryCount }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-500">{{ $t('sidePanel.transactions') }}:</span>
              <span class="text-white">{{ transactionCount }}</span>
            </div>
          </div>
        </div>
        
        <!-- Logout Button -->
        <div class="p-4 border-t border-gray-700">
          <button 
            @click="handleLogout"
            :disabled="loggingOut"
            class="w-full flex items-center justify-center px-4 py-3 bg-red-600 hover:bg-red-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-medium rounded-lg transition-colors"
          >
            <svg v-if="loggingOut" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            {{ loggingOut ? $t('auth.logout.logging') : $t('auth.logout.button') }}
          </button>
        </div>
  </div>
  
  <!-- Overlay -->
  <div 
    v-if="isOpen"
    @click="closePanel"
    class="fixed inset-0 bg-black/50 z-40"
  ></div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { authService } from '../services/AuthService';

const router = useRouter();
const loggingOut = ref(false);
const currentUser = ref(null);

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

// Load current user info and category count
onMounted(async () => {
  try {
    const response = await authService.getUser();
    if (response.success) {
      currentUser.value = response.user;
      await loadCategoryCount(); // Load category count after confirming authentication
    }
  } catch (error) {
    console.error('Failed to load user info:', error);
  }
});

const closePanel = () => {
  emit('close');
};

const handleLogout = async () => {
  loggingOut.value = true;
  
  try {
    const response = await authService.logout();
    
    if (response.success) {
      closePanel();
      router.push('/login');
    } else {
      alert('Logout failed. Please try again.');
    }
  } catch (error) {
    alert('Network error. Please try again.');
    console.error('Logout error:', error);
  } finally {
    loggingOut.value = false;
  }
};

const categoryCount = ref(0);

// Load category count from database
const loadCategoryCount = async () => {
  try {
    const response = await authService.getUser(); // Check if authenticated first
    if (response.success) {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      
      const categoryResponse = await fetch('/api/categories', {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken }),
        },
        credentials: 'same-origin',
      });
      
      if (categoryResponse.ok) {
        const data = await categoryResponse.json();
        if (data.success) {
          categoryCount.value = data.categories.length;
        }
      }
    }
  } catch (error) {
    console.error('Failed to load category count:', error);
  }
};
</script>
