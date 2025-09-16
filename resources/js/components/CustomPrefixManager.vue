<template>
    <div class="mb-8">
    <!-- Category Summary Cards -->
    <div v-if="prefixes.length > 0 && categoryData.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div 
        v-for="category in categoryData" 
        :key="category.prefix"
        class="bg-gray-900 rounded-lg p-4 border border-gray-700 hover:border-gray-600 transition-colors"
      >
        <div class="flex items-center justify-between mb-3">
          <div>
            <h4 class="text-lg font-semibold text-white">{{ category.displayName }}</h4>
            <p v-if="category.displayName !== category.prefix" class="text-xs text-gray-500">{{ category.prefix }}</p>
          </div>
          <div class="w-8 h-8 bg-purple-600/20 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
          </div>
        </div>
        
         <div class="space-y-2">
           <div class="flex justify-between items-center">
             <span class="text-gray-400 text-sm">{{ $t('categories.totalSpent') }}:</span>
             <span class="text-red-400 font-medium">{{ formatCurrency(category.totalSpent) }}</span>
           </div>
           
           <div class="flex justify-between items-center">
             <span class="text-gray-400 text-sm">{{ $t('categories.totalReceived') }}:</span>
             <span class="text-green-400 font-medium">{{ formatCurrency(category.totalReceived) }}</span>
           </div>
           
           <div class="flex justify-between items-center pt-2 border-t border-gray-700">
             <span class="text-gray-400 text-sm font-medium">{{ $t('categories.net') }}:</span>
             <span 
               class="font-bold"
               :class="category.netAmount >= 0 ? 'text-green-400' : 'text-red-400'"
             >
               {{ category.netAmount >= 0 ? '+' : '' }}{{ formatCurrency(category.netAmount) }}
             </span>
           </div>
           
           <div class="flex justify-between items-center">
             <span class="text-gray-400 text-xs">{{ $t('categories.transactions') }}:</span>
             <span class="text-white text-xs">{{ category.transactionCount }}</span>
           </div>
         </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, getCurrentInstance } from 'vue';
import localizationService from '../services/LocalizationService';

const props = defineProps({
  transactions: {
    type: Array,
    default: () => []
  }
});

const prefixes = ref([]);

// Load saved prefixes from localStorage
onMounted(() => {
  loadPrefixes();
});

// Watch for changes in localStorage
const loadPrefixes = () => {
  const savedPrefixes = localStorage.getItem('customPrefixes');
  if (savedPrefixes) {
    prefixes.value = JSON.parse(savedPrefixes);
  }
};

// Set up an interval to check for localStorage changes
onMounted(() => {
  setInterval(loadPrefixes, 1000); // Check every second for changes
});

const formatCurrency = (amount) => {
  if (amount === null || amount === undefined) return `${localizationService.t('common.currency')} 0.00`;
  return `${localizationService.t('common.currency')} ${Number(amount).toFixed(2)}`;
};

// Calculate category data based on prefixes and transactions
const categoryData = computed(() => {
  if (!props.transactions.length || !prefixes.value.length) return [];
  
  return prefixes.value.map(prefixObj => {
    const matchingTransactions = props.transactions.filter(transaction => 
      transaction.transaction_details.toUpperCase().includes(prefixObj.name)
    );
    
    const totalSpent = matchingTransactions
      .filter(t => t.money_out && parseFloat(t.money_out) > 0)
      .reduce((sum, t) => sum + parseFloat(t.money_out), 0);
    
    const totalReceived = matchingTransactions
      .filter(t => t.money_in && parseFloat(t.money_in) > 0)
      .reduce((sum, t) => sum + parseFloat(t.money_in), 0);
    
    return {
      prefix: prefixObj.name,
      displayName: prefixObj.description ? prefixObj.description : prefixObj.name,
      totalSpent,
      totalReceived,
      netAmount: totalReceived - totalSpent,
      transactionCount: matchingTransactions.length
    };
  }).filter(category => category.transactionCount > 0);
});
</script>
