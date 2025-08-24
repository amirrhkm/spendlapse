<template>
  <div class="bg-gray-900 rounded-xl shadow-lg p-6 border border-gray-700">
    <h2 class="text-xl font-semibold text-white mb-6 flex items-center">
      <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
      </svg>
      Monthly Summary
    </h2>
    
    <div v-if="loading" class="flex items-center justify-center py-12">
      <svg class="animate-spin h-8 w-8 text-blue-400" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>
    
    <div v-else-if="!summary" class="text-center py-12">
      <svg class="w-12 h-12 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <p class="text-gray-400">Upload a CSV file to see your monthly summary</p>
    </div>
    
    <div v-else class="space-y-6">
      <!-- Period Info -->
      <div class="text-center mb-6">
        <h3 class="text-lg font-medium text-white">
          {{ getMonthName(summary.month) }} {{ summary.year }}
        </h3>
      </div>
      
      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Money In -->
        <div class="bg-green-900/30 border border-green-700 rounded-lg p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-green-300 text-sm font-medium uppercase tracking-wide">Money In</p>
              <p class="text-2xl font-bold text-white mt-1">
                {{ formatCurrency(summary.total_money_in) }}
              </p>
            </div>
            <div class="w-12 h-12 bg-green-600/20 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M7 11l5-5m0 0l5 5m-5-5v12" />
              </svg>
            </div>
          </div>
        </div>
        
        <!-- Money Out -->
        <div class="bg-red-900/30 border border-red-700 rounded-lg p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-red-300 text-sm font-medium uppercase tracking-wide">Money Out</p>
              <p class="text-2xl font-bold text-white mt-1">
                {{ formatCurrency(summary.total_money_out) }}
              </p>
            </div>
            <div class="w-12 h-12 bg-red-600/20 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M17 13l-5 5m0 0l-5-5m5 5V6" />
              </svg>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Final Balance -->
      <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
        <div class="text-center">
          <p class="text-slate-300 text-sm font-medium uppercase tracking-wide mb-2">Final Balance</p>
          <p class="text-3xl font-bold text-white">
            {{ formatCurrency(summary.final_balance) }}
          </p>
          
          <!-- Net Change -->
          <div class="mt-4 flex items-center justify-center space-x-2">
            <span class="text-gray-400 text-sm">Net Change:</span>
            <span 
              class="text-sm font-medium"
              :class="summary.net_change >= 0 ? 'text-green-400' : 'text-red-400'"
            >
              {{ summary.net_change >= 0 ? '+' : '' }}{{ formatCurrency(summary.net_change) }}
            </span>
          </div>
        </div>
      </div>
      
      <!-- Summary Stats -->
      <div class="bg-gray-800 border border-gray-700 rounded-lg p-4">
        <h4 class="text-white font-medium mb-3">Quick Stats</h4>
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <span class="text-gray-400">Transactions:</span>
            <span class="text-white ml-2 font-medium">{{ summary.transaction_count || 'N/A' }}</span>
          </div>
          <div>
            <span class="text-gray-400">Average Expense:</span>
            <span class="text-white ml-2 font-medium">
              {{ summary.total_money_out > 0 ? formatCurrency(summary.total_money_out / (summary.transaction_count || 1)) : 'N/A' }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  summary: {
    type: Object,
    default: null
  },
  loading: {
    type: Boolean,
    default: false
  }
});

const formatCurrency = (amount) => {
  if (amount === null || amount === undefined) return 'MYR 0.00';
  return `MYR ${Number(amount).toFixed(2)}`;
};

const getMonthName = (monthNumber) => {
  const months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];
  return months[monthNumber - 1] || 'Unknown';
};
</script>
