<template>
  <div v-if="monthlyData.length > 0" class="mb-8">
    <h2 class="text-2xl font-bold text-white mb-8 flex items-center">
      <svg class="w-6 h-6 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
      Financial Dashboard
    </h2>
    
    <!-- Dashboard Table -->
    <div class="bg-gray-900 rounded-xl shadow-lg border border-gray-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full min-w-full">
          <!-- Table Header -->
          <thead class="bg-gray-800 border-b border-gray-700">
            <tr>
              <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                Period
              </th>
              <th class="px-2 sm:px-6 py-3 sm:py-4 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
                Money In
              </th>
              <th class="px-2 sm:px-6 py-3 sm:py-4 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
                Money Out
              </th>
              <th class="px-2 sm:px-6 py-3 sm:py-4 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
                Net Change
              </th>
              <th class="px-2 sm:px-6 py-3 sm:py-4 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
                Final Balance
              </th>
              <th class="px-2 sm:px-6 py-3 sm:py-4 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">
                Transactions
              </th>
            </tr>
          </thead>
          
          <!-- Table Body -->
          <tbody class="divide-y divide-gray-700">
                         <tr 
               v-for="month in monthlyData" 
               :key="`${month.year}-${month.month}`"
               @click="goToMonthDetail(month.year, month.month)"
               class="hover:bg-gray-800/50 transition-colors duration-150 cursor-pointer"
             >
                             <!-- Period Column -->
               <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                 <div class="flex items-center">
                   <div class="w-6 h-6 sm:w-8 sm:h-8 bg-blue-600/20 rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                     <svg class="w-3 h-3 sm:w-4 sm:h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                             d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                       </svg>
                     </div>
                     <div>
                       <div class="text-xs sm:text-sm font-medium text-white">
                         {{ getMonthName(month.month) }} {{ month.year }}
                       </div>
                     </div>
                   </div>
                 </td>
                 
                 <!-- Money In Column -->
                 <td class="px-2 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-right">
                   <div class="text-xs sm:text-sm font-medium text-green-400">
                     {{ formatCurrency(month.total_money_in) }}
                   </div>
                 </td>
                 
                 <!-- Money Out Column -->
                 <td class="px-2 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-right">
                   <div class="text-xs sm:text-sm font-medium text-red-400">
                     {{ formatCurrency(month.total_money_out) }}
                   </div>
                 </td>
                 
                 <!-- Net Change Column -->
                 <td class="px-2 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-right">
                   <div 
                     class="text-xs sm:text-sm font-medium"
                     :class="month.net_change >= 0 ? 'text-green-400' : 'text-red-400'"
                   >
                     {{ month.net_change >= 0 ? '+' : '' }}{{ formatCurrency(month.net_change) }}
                   </div>
                 </td>
                 
                 <!-- Final Balance Column -->
                 <td class="px-2 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-right">
                   <div class="text-xs sm:text-sm font-bold text-blue-400">
                     {{ formatCurrency(month.final_balance) }}
                   </div>
                 </td>
                 
                 <!-- Transactions Column -->
                 <td class="px-2 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-center">
                   <div class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-700 text-gray-300">
                     {{ month.transaction_count || 'N/A' }}
                   </div>
                 </td>
            </tr>
          </tbody>
        </table>
      </div>
      
             <!-- Summary Row -->
       <div class="bg-gray-800 border-t border-gray-700 px-4 sm:px-6 py-3 sm:py-4">
         <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-2 sm:space-y-0 text-xs sm:text-sm">
           <div class="flex items-center space-x-4">
             <span class="text-gray-400 font-medium">Total Months:</span>
             <span class="text-white font-bold">{{ monthlyData.length }}</span>
           </div>
           
           <div class="flex items-center space-x-4">
             <span class="text-gray-400 font-medium">Total Transactions:</span>
             <span class="text-white font-bold">{{ totalTransactions }}</span>
           </div>
           
           <div class="flex items-center space-x-4">
             <span class="text-gray-400 font-medium">Overall Net:</span>
             <span 
               class="font-bold"
               :class="overallNetChange >= 0 ? 'text-green-400' : 'text-red-400'"
             >
               {{ overallNetChange >= 0 ? '+' : '' }}{{ formatCurrency(overallNetChange) }}
             </span>
           </div>
         </div>
       </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const props = defineProps({
  monthlyData: {
    type: Array,
    default: () => []
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

// Computed properties for summary row
const totalTransactions = computed(() => {
  return props.monthlyData.reduce((total, month) => total + (month.transaction_count || 0), 0);
});

const overallNetChange = computed(() => {
  return props.monthlyData.reduce((total, month) => total + (month.net_change || 0), 0);
});

const goToMonthDetail = (year, month) => {
  router.push(`/month/${year}/${month}`);
};
</script>
