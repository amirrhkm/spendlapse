<template>
  <div class="min-h-screen bg-black">
    <Header :transactionCount="transactionCount" />
    <main class="container mx-auto px-4 pt-12 pb-24">
      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="text-center">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500 mb-4"></div>
          <p class="text-gray-400 text-lg">{{ $t('common.loading') }}</p>
        </div>
      </div>

      <!-- Content -->
      <div v-else>
        <!-- Page Title -->
        <div class="mt-8 mb-8">
          <h1 class="text-3xl font-bold text-white flex items-center">
            <svg class="w-8 h-8 mr-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            {{ $t('categories.title') }}
          </h1>
          <p class="text-gray-400 mt-2">{{ $t('categories.description') }}</p>
        </div>

        <!-- Add New Prefix Form -->
        <div class="bg-gray-900 rounded-xl shadow-lg border border-gray-700 p-8 mb-8">
        <h2 class="text-xl font-semibold text-white mb-6 flex items-center">
          <svg class="w-6 h-6 mr-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          {{ $t('categories.addNewCategory') }}
        </h2>
        
        <form @submit.prevent="addPrefix" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="prefix" class="block text-sm font-medium text-gray-300 mb-2">
                {{ $t('categories.prefix') }}
              </label>
              <input 
                id="prefix"
                v-model="newPrefix"
                type="text"
                :placeholder="$t('categories.prefixPlaceholder')"
                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-colors"
                required
              />
              <p class="text-xs text-gray-500 mt-1">{{ $t('categories.prefixHelp') }}</p>
            </div>
            
            <div>
              <label for="description" class="block text-sm font-medium text-gray-300 mb-2">
                {{ $t('categories.name') }} ({{ $t('common.optional') }})
              </label>
              <input 
                id="description"
                v-model="newDescription"
                type="text"
                :placeholder="$t('categories.namePlaceholder')"
                class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-500 transition-colors"
              />
              <p class="text-xs text-gray-500 mt-1">{{ $t('categories.nameHelp') }}</p>
            </div>
          </div>
          
          <div class="flex items-center space-x-4">
            <button 
              type="submit"
              :disabled="!newPrefix.trim() || adding"
              class="px-8 py-3 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-medium rounded-lg transition-colors flex items-center"
            >
              <svg v-if="adding" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ adding ? $t('categories.adding') : $t('categories.addCategory') }}
            </button>
            
            <button 
              type="button"
              @click="clearForm"
              class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors"
            >
              {{ $t('categories.clearForm') }}
            </button>
          </div>
        </form>
      </div>

      <!-- Current Categories -->
      <div class="bg-gray-900 rounded-xl shadow-lg border border-gray-700 p-8 mb-12">
        <h2 class="text-xl font-semibold text-white mb-6 flex items-center">
          <svg class="w-6 h-6 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
          {{ $t('categories.currentCategories') }}
        </h2>
        
        <div v-if="prefixes.length === 0" class="text-center py-12">
          <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
          </svg>
          <p class="text-gray-400 text-lg">{{ $t('categories.noCategories') }}</p>
          <p class="text-gray-500 text-sm mt-2">{{ $t('categories.noCategoriesDescription') }}</p>
        </div>
        
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div 
            v-for="prefix in prefixes" 
            :key="prefix.name"
            class="bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-gray-600 transition-colors"
          >
            <div class="flex items-start justify-between mb-4">
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-white mb-2">{{ prefix.display_name || prefix.name }}</h3>
                <p v-if="prefix.display_name" class="text-gray-400 text-xs">{{ prefix.name }}</p>
                <p v-else class="text-gray-500 text-sm italic">{{ $t('categories.noDescription') }}</p>
              </div>
              
              <div class="w-10 h-10 bg-purple-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
              </div>
            </div>
            
            <div class="flex items-center justify-between">
              <span class="text-xs text-gray-500">
                Added: {{ formatDate(prefix.created_at) }}
              </span>
              
              <button 
                @click="removePrefix(prefix.name)"
                class="text-gray-400 hover:text-red-400 transition-colors p-2 rounded-lg hover:bg-red-400/10"
                title="Remove category"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Header from './Header.vue';
import { categoryService } from '../services/CategoryService';

const prefixes = ref([]);
const newPrefix = ref('');
const newDescription = ref('');
const adding = ref(false);
const transactionCount = ref(0);
const loading = ref(true);

onMounted(async () => {
  loading.value = true;
  await loadCategories();
  await migrateFromLocalStorage();
  await loadTransactionCount();
  loading.value = false;
});

const loadCategories = async () => {
  try {
    const response = await categoryService.getCategories();
    if (response.success) {
      prefixes.value = response.categories;
    }
  } catch (error) {
    console.error('Failed to load categories:', error);
  }
};

const migrateFromLocalStorage = async () => {
  try {
    const response = await categoryService.migrateFromLocalStorage();
    if (response.success && response.migrated_count > 0) {
      console.log(`Migrated ${response.migrated_count} categories from localStorage`);
      await loadCategories();
    }
  } catch (error) {
    console.error('Migration failed:', error);
  }
};

const addPrefix = async () => {
  if (!newPrefix.value.trim()) return;
  
  adding.value = true;
  
  try {
    const response = await categoryService.createCategory({
      name: newPrefix.value.trim(),
      display_name: newDescription.value.trim() || null,
      description: null
    });
    
    if (response.success) {
      await loadCategories();
      clearForm();
    } else {
      alert(response.message || 'Failed to create category');
    }
  } catch (error) {
    alert('Network error. Please try again.');
    console.error('Create category error:', error);
  } finally {
    adding.value = false;
  }
};

const removePrefix = async (prefixName) => {
  if (confirm(`Are you sure you want to remove "${prefixName}"? This will also remove it from all transaction categorizations.`)) {
    const category = prefixes.value.find(p => p.name === prefixName);
    if (category) {
      try {
        const response = await categoryService.deleteCategory(category.id);
        if (response.success) {
          await loadCategories();
        } else {
          alert(response.message || 'Failed to delete category');
        }
      } catch (error) {
        alert('Network error. Please try again.');
        console.error('Delete category error:', error);
      }
    }
  }
};

const clearForm = () => {
  newPrefix.value = '';
  newDescription.value = '';
};

const loadTransactionCount = async () => {
  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    const response = await fetch('/api/transactions', {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken }),
      },
      credentials: 'same-origin',
    });
    
    if (response.ok) {
      const data = await response.json();
      if (Array.isArray(data)) {
        transactionCount.value = data.length;
      }
    }
  } catch (error) {
    console.error('Failed to load transaction count:', error);
  }
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};
</script>
