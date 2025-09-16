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
        <!-- Month Title -->
        <div class="mt-8 mb-8">
           <h1 class="text-3xl font-bold text-white flex items-center">
             <svg class="w-8 h-8 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                     d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
             </svg>
             {{ getMonthName(monthData.month) }} {{ monthData.year }} - Transaction Details
           </h1>
         </div>

                     <!-- Month Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
          <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
            <div class="text-gray-400 text-sm font-medium">{{ $t('monthDetail.totalMoneyIn') }}</div>
            <div class="text-2xl font-bold text-green-400">{{ formatCurrency(monthData.total_money_in) }}</div>
          </div>
          
          <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
            <div class="text-gray-400 text-sm font-medium">{{ $t('monthDetail.totalMoneyOut') }}</div>
            <div class="text-2xl font-bold text-red-400">{{ formatCurrency(monthData.total_money_out) }}</div>
          </div>
          
          <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
            <div class="text-gray-400 text-sm font-medium">{{ $t('monthDetail.netChange') }}</div>
            <div 
              class="text-2xl font-bold"
              :class="monthData.net_change >= 0 ? 'text-green-400' : 'text-red-400'"
            >
              {{ monthData.net_change >= 0 ? '+' : '' }}{{ formatCurrency(monthData.net_change) }}
            </div>
          </div>
          
          <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
            <div class="text-gray-400 text-sm font-medium">{{ $t('monthDetail.finalBalance') }}</div>
            <div class="text-2xl font-bold text-blue-400">{{ formatCurrency(monthData.final_balance) }}</div>
          </div>
        </div>

        <!-- Custom Categories Summary for this month -->
        <div class="mb-8">
          <CustomPrefixManager :transactions="transactions" />
        </div>

        <!-- Transactions Table -->
       <div class="bg-gray-900 rounded-xl shadow-lg border border-gray-700 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-700">
          <h2 class="text-xl font-semibold text-white">
            {{ $t('monthDetail.allTransactions') }} ({{ transactions.length }})
          </h2>
        </div>
        
        <div class="overflow-x-auto">
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
                <th class="px-6 py-3 text-right text-xs font-medium text-xs font-medium text-gray-300 uppercase tracking-wider">
                  {{ $t('monthDetail.moneyOut') }}
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
                  {{ $t('monthDetail.balance') }}
                </th>
              </tr>
            </thead>
            
            <tbody class="divide-y divide-gray-700">
              <tr 
                v-for="transaction in transactions" 
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
                    {{ transaction.transaction_details }}
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
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Header from './Header.vue';
import CustomPrefixManager from './CustomPrefixManager.vue';
import { transactionService } from '../services/TransactionService';

const monthData = ref({});
const transactions = ref([]);
const loading = ref(true);

const props = defineProps({
  year: {
    type: [String, Number],
    required: true
  },
  month: {
    type: [String, Number],
    required: true
  }
});

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

const loadMonthData = async () => {
  loading.value = true;
  try {
    // Load all transactions and filter by month
    const allTransactions = await transactionService.getAllTransactions();
    
    // Filter transactions for the specific month
    const monthTransactions = allTransactions.filter(transaction => {
      const date = new Date(transaction.transaction_date);
      const year = date.getFullYear();
      const month = date.getMonth() + 1;
      
      // Normalize year if needed
      let normalizedYear = year;
      if (year < 100) {
        normalizedYear += 2000;
      }
      
      return normalizedYear === parseInt(props.year) && month === parseInt(props.month);
    });
    
    // Sort transactions by date (oldest first)
    monthTransactions.sort((a, b) => new Date(a.transaction_date) - new Date(b.transaction_date));
    
    transactions.value = monthTransactions;
    
    // Calculate month summary
    const totalMoneyIn = monthTransactions
      .filter(t => t.money_in && parseFloat(t.money_in) > 0)
      .reduce((sum, t) => sum + parseFloat(t.money_in), 0);
    
    const totalMoneyOut = monthTransactions
      .filter(t => t.money_out && parseFloat(t.money_out) > 0)
      .reduce((sum, t) => sum + parseFloat(t.money_out), 0);
    
    const finalBalance = monthTransactions.length > 0 
      ? parseFloat(monthTransactions[monthTransactions.length - 1].balance)
      : 0;
    
    monthData.value = {
      year: parseInt(props.year),
      month: parseInt(props.month),
      total_money_in: totalMoneyIn,
      total_money_out: totalMoneyOut,
      net_change: totalMoneyIn - totalMoneyOut,
      final_balance: finalBalance
    };
    
  } catch (error) {
    console.error('Failed to load month data:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadMonthData();
});
</script>
