<template>
  <div class="min-h-screen bg-black">
    <Header />
    <main class="container mx-auto px-4 py-12">
      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="text-center">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500 mb-4"></div>
          <p class="text-gray-400 text-lg">{{ $t('common.loading') }}</p>
        </div>
      </div>

      <!-- Content -->
      <div v-else>
        <!-- Prefix Title -->
        <div class="mt-8 mb-8">
          <h1 class="text-3xl font-bold text-white flex items-center">
            <svg class="w-8 h-8 mr-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            {{ prefixData.displayName }} - Transaction Details
          </h1>
          <p v-if="prefixData.displayName !== prefixData.prefix" class="text-gray-400 mt-2">
            Prefix: {{ prefixData.prefix }}
          </p>
        </div>

        <!-- Filter Controls -->
        <div class="p-4 mb-8">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-4">
              <!-- Year Filter -->
              <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-300 mb-1">Filter by Year</label>
                <select 
                  v-model="selectedYear" 
                  @change="applyFilters"
                  class="bg-gray-800 border border-gray-600 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                >
                  <option value="">All Years</option>
                  <option v-for="year in availableYears" :key="year" :value="year">
                    {{ year }}
                  </option>
                </select>
              </div>

              <!-- Month Filter -->
              <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-300 mb-1">Filter by Month</label>
                <select 
                  v-model="selectedMonth" 
                  @change="applyFilters"
                  class="bg-gray-800 border border-gray-600 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                  :disabled="!selectedYear"
                >
                  <option value="">All Months</option>
                  <option v-for="month in availableMonths" :key="month.value" :value="month.value">
                    {{ month.name }}
                  </option>
                </select>
              </div>

              <!-- Transaction Counter -->
              <div class="flex items-end">
                <div class="text-sm text-gray-400 flex items-center pb-2">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                  Showing {{ filteredTransactions.length }} of {{ allTransactions.length }} transactions
                  <span v-if="selectedYear || selectedMonth" class="ml-2 px-2 py-1 bg-purple-600/20 text-purple-300 rounded text-xs">
                    Filtered
                  </span>
                </div>
              </div>
            </div>

            <!-- Reset Filter Button -->
            <div class="flex justify-end">
              <button 
                @click="resetFilters"
                :disabled="!selectedYear && !selectedMonth"
                class="bg-gray-700 hover:bg-gray-600 disabled:bg-gray-800 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center text-sm"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Reset Filters
              </button>
            </div>
          </div>
        </div>

        <!-- Prefix Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
          <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
            <div class="text-gray-400 text-sm font-medium">{{ $t('categories.totalSpent') }}</div>
            <div class="text-2xl font-bold text-red-400">{{ formatCurrency(prefixData.totalSpent) }}</div>
            <div v-if="selectedYear || selectedMonth" class="text-xs text-gray-500 mt-1">
              <span v-if="selectedYear && selectedMonth">{{ getMonthName(parseInt(selectedMonth)) }} {{ selectedYear }}</span>
              <span v-else-if="selectedYear">{{ selectedYear }}</span>
            </div>
          </div>
          
          <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
            <div class="text-gray-400 text-sm font-medium">{{ $t('categories.totalReceived') }}</div>
            <div class="text-2xl font-bold text-green-400">{{ formatCurrency(prefixData.totalReceived) }}</div>
            <div v-if="selectedYear || selectedMonth" class="text-xs text-gray-500 mt-1">
              <span v-if="selectedYear && selectedMonth">{{ getMonthName(parseInt(selectedMonth)) }} {{ selectedYear }}</span>
              <span v-else-if="selectedYear">{{ selectedYear }}</span>
            </div>
          </div>
          
          <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
            <div class="text-gray-400 text-sm font-medium">{{ $t('categories.net') }}</div>
            <div 
              class="text-2xl font-bold"
              :class="prefixData.netAmount >= 0 ? 'text-green-400' : 'text-red-400'"
            >
              {{ prefixData.netAmount >= 0 ? '+' : '' }}{{ formatCurrency(prefixData.netAmount) }}
            </div>
            <div v-if="selectedYear || selectedMonth" class="text-xs text-gray-500 mt-1">
              <span v-if="selectedYear && selectedMonth">{{ getMonthName(parseInt(selectedMonth)) }} {{ selectedYear }}</span>
              <span v-else-if="selectedYear">{{ selectedYear }}</span>
            </div>
          </div>
          
          <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
            <div class="text-gray-400 text-sm font-medium">{{ $t('categories.transactions') }}</div>
            <div class="text-2xl font-bold text-blue-400">{{ prefixData.transactionCount }}</div>
            <div v-if="selectedYear || selectedMonth" class="text-xs text-gray-500 mt-1">
              <span v-if="selectedYear && selectedMonth">{{ getMonthName(parseInt(selectedMonth)) }} {{ selectedYear }}</span>
              <span v-else-if="selectedYear">{{ selectedYear }}</span>
            </div>
          </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-gray-900 rounded-xl shadow-lg border border-gray-700 overflow-hidden mb-8">
          <div class="px-6 py-4 border-b border-gray-700">
            <h2 class="text-xl font-semibold text-white">
              {{ $t('prefixDetail.allTransactions') }} ({{ filteredTransactions.length }})
            </h2>
            <div v-if="selectedYear || selectedMonth" class="text-sm text-gray-400 mt-1">
              <span v-if="selectedYear && selectedMonth">
                Filtered by {{ getMonthName(parseInt(selectedMonth)) }} {{ selectedYear }}
              </span>
              <span v-else-if="selectedYear">
                Filtered by {{ selectedYear }}
              </span>
            </div>
          </div>
          
          <!-- No transactions message -->
          <div v-if="filteredTransactions.length === 0" class="px-6 py-12 text-center">
            <div class="text-gray-400 text-lg mb-2">
              <span v-if="selectedYear || selectedMonth">No transactions found for the selected filter</span>
              <span v-else>{{ $t('prefixDetail.noTransactions') }}</span>
            </div>
            <div class="text-gray-500 text-sm">
              <span v-if="selectedYear || selectedMonth">Try adjusting your filter or reset to see all transactions</span>
              <span v-else>No transactions match the prefix "{{ prefixData.prefix }}"</span>
            </div>
          </div>
          
          <div v-if="filteredTransactions.length > 0" class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-800 border-b border-gray-700">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                    {{ $t('monthDetail.date') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                    {{ $t('monthDetail.details') }}
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
                    {{ $t('monthDetail.moneyIn') }}
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
                    {{ $t('monthDetail.moneyOut') }}
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
                    {{ $t('monthDetail.balance') }}
                  </th>
                </tr>
              </thead>
              
              <tbody class="divide-y divide-gray-700">
                <tr 
                  v-for="transaction in filteredTransactions" 
                  :key="transaction.id"
                  class="hover:bg-gray-800/50 transition-colors duration-150"
                >
                  <!-- Date Column -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-white">
                      {{ formatDate(transaction.transaction_date) }}
                    </div>
                  </td>
                  
                  <!-- Details Column -->
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-300 break-words" :title="transaction.transaction_details">
                      <span 
                        v-html="highlightPrefix(transaction.transaction_details, prefixData.prefix)"
                        class="leading-relaxed"
                      ></span>
                    </div>
                  </td>
                  
                  <!-- Money In Column -->
                  <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div 
                      v-if="transaction.money_in && parseFloat(transaction.money_in) > 0"
                      class="text-sm font-medium text-green-400"
                    >
                      {{ formatCurrency(transaction.money_in) }}
                    </div>
                    <div v-else class="text-sm text-gray-500">-</div>
                  </td>
                  
                  <!-- Money Out Column -->
                  <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div 
                      v-if="transaction.money_out && parseFloat(transaction.money_out) > 0"
                      class="text-sm font-medium text-red-400"
                    >
                      {{ formatCurrency(transaction.money_out) }}
                    </div>
                    <div v-else class="text-sm text-gray-500">-</div>
                  </td>
                  
                  <!-- Balance Column -->
                  <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div class="text-sm font-medium text-white">
                      {{ formatCurrency(transaction.balance) }}
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Back to Dashboard Button -->
        <div class="flex justify-center">
          <button 
            @click="goBack"
            class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-8 rounded-lg transition-colors duration-200 flex items-center shadow-lg"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Dashboard
          </button>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import Header from './Header.vue';
import { transactionService } from '../services/TransactionService';
import { categoryService } from '../services/CategoryService';

const router = useRouter();
const prefixData = ref({});
const allTransactions = ref([]);
const loading = ref(true);

// Filter state
const selectedYear = ref('');
const selectedMonth = ref('');

const props = defineProps({
  prefixName: {
    type: String,
    required: true
  }
});

// Computed properties
const filteredTransactions = computed(() => {
  let filtered = allTransactions.value;
  
  if (selectedYear.value) {
    filtered = filtered.filter(transaction => {
      const date = new Date(transaction.transaction_date);
      let year = date.getFullYear();
      
      // Normalize year if needed
      if (year < 100) {
        year += 2000;
      }
      
      return year === parseInt(selectedYear.value);
    });
  }
  
  if (selectedMonth.value) {
    filtered = filtered.filter(transaction => {
      const date = new Date(transaction.transaction_date);
      const month = date.getMonth() + 1;
      return month === parseInt(selectedMonth.value);
    });
  }
  
  return filtered;
});

const availableYears = computed(() => {
  const years = new Set();
  allTransactions.value.forEach(transaction => {
    const date = new Date(transaction.transaction_date);
    let year = date.getFullYear();
    
    // Normalize year if needed
    if (year < 100) {
      year += 2000;
    }
    
    years.add(year);
  });
  
  return Array.from(years).sort((a, b) => b - a); // Sort descending
});

const availableMonths = computed(() => {
  if (!selectedYear.value) return [];
  
  const months = new Set();
  allTransactions.value.forEach(transaction => {
    const date = new Date(transaction.transaction_date);
    let year = date.getFullYear();
    
    // Normalize year if needed
    if (year < 100) {
      year += 2000;
    }
    
    if (year === parseInt(selectedYear.value)) {
      const month = date.getMonth() + 1;
      months.add(month);
    }
  });
  
  const monthNames = [
    { value: 1, name: 'January' },
    { value: 2, name: 'February' },
    { value: 3, name: 'March' },
    { value: 4, name: 'April' },
    { value: 5, name: 'May' },
    { value: 6, name: 'June' },
    { value: 7, name: 'July' },
    { value: 8, name: 'August' },
    { value: 9, name: 'September' },
    { value: 10, name: 'October' },
    { value: 11, name: 'November' },
    { value: 12, name: 'December' }
  ];
  
  return monthNames.filter(month => months.has(month.value));
});

// Methods
const formatCurrency = (amount) => {
  if (amount === null || amount === undefined) return 'MYR 0.00';
  return `MYR ${Number(amount).toFixed(2)}`;
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const getMonthName = (monthNumber) => {
  const months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];
  return months[monthNumber - 1] || 'Unknown';
};

const highlightPrefix = (text, prefix) => {
  if (!prefix || !text) return text;
  
  // Create a regex to find the prefix (case insensitive)
  const regex = new RegExp(`(${prefix.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
  return text.replace(regex, '<span class="bg-purple-600/30 text-purple-300 px-1 rounded">$1</span>');
};

const applyFilters = () => {
  // Filters are automatically applied via computed properties
  updatePrefixData();
};

const resetFilters = () => {
  selectedYear.value = '';
  selectedMonth.value = '';
  updatePrefixData();
};

const goBack = () => {
  router.push('/');
};

const updatePrefixData = () => {
  // Calculate prefix summary based on filtered transactions
  const transactions = filteredTransactions.value;
  
  const totalSpent = transactions
    .filter(t => t.money_out && parseFloat(t.money_out) > 0)
    .reduce((sum, t) => sum + parseFloat(t.money_out), 0);
  
  const totalReceived = transactions
    .filter(t => t.money_in && parseFloat(t.money_in) > 0)
    .reduce((sum, t) => sum + parseFloat(t.money_in), 0);
  
  prefixData.value = {
    ...prefixData.value,
    totalSpent,
    totalReceived,
    netAmount: totalReceived - totalSpent,
    transactionCount: transactions.length
  };
};

const loadPrefixData = async () => {
  loading.value = true;
  try {
    // Load all transactions
    const allTransactionsData = await transactionService.getAllTransactions();
    
    // Load categories to get display name
    const categoriesResponse = await categoryService.getCategories();
    const categories = categoriesResponse.success ? categoriesResponse.categories : [];
    
    // Find the category info
    const category = categories.find(cat => cat.name === props.prefixName);
    const displayName = category ? category.display_name : props.prefixName;
    
    // Filter transactions that match the prefix
    const matchingTransactions = allTransactionsData.filter(transaction => 
      transaction.transaction_details.toUpperCase().includes(props.prefixName.toUpperCase())
    );
    
    // Sort transactions by date (oldest first)
    matchingTransactions.sort((a, b) => new Date(a.transaction_date) - new Date(b.transaction_date));
    
    allTransactions.value = matchingTransactions;
    
    // Set initial prefix data
    prefixData.value = {
      prefix: props.prefixName,
      displayName: displayName || props.prefixName,
      totalSpent: 0,
      totalReceived: 0,
      netAmount: 0,
      transactionCount: 0
    };
    
    // Update with initial calculations
    updatePrefixData();
    
  } catch (error) {
    console.error('Failed to load prefix data:', error);
  } finally {
    loading.value = false;
  }
};

// Watchers
watch([selectedYear, selectedMonth], () => {
  if (!loading.value) {
    updatePrefixData();
  }
});

// Reset month when year changes
watch(selectedYear, () => {
  if (selectedMonth.value && !availableMonths.value.find(m => m.value === parseInt(selectedMonth.value))) {
    selectedMonth.value = '';
  }
});

onMounted(() => {
  loadPrefixData();
});
</script>
