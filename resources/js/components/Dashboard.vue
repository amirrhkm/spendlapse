<template>
  <div class="min-h-screen bg-black">
    <Header :transactionCount="allTransactions.length" />
    <main class="container mx-auto px-4 py-12">
        <!-- Loading State -->
        <div v-if="dataLoading" class="flex items-center justify-center py-20">
          <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500 mb-4"></div>
            <p class="text-gray-400 text-lg">{{ $t('common.loading') }}</p>
          </div>
        </div>

        <!-- Content -->
        <div v-else>
          <!-- Custom Categories Summary -->
          <div class="mt-8 mb-8">
            <CustomPrefixManager :transactions="allTransactions" />
          </div>

          <!-- Monthly Overview Cards -->
          <div class="mt-8">
            <MonthlyOverview :monthlyData="monthlyData" />
          </div>
          
          <!-- Upload Section - Simple inline button -->
          <div class="mb-8">
            <FileUpload @uploaded="handleUpload" :loading="loading" />
          </div>
        </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Header from './Header.vue';
import FileUpload from './FileUpload.vue';
import MonthlyOverview from './MonthlyOverview.vue';
import CustomPrefixManager from './CustomPrefixManager.vue';
import { transactionService } from '../services/TransactionService';

const loading = ref(false);
const dataLoading = ref(true);
const monthlyData = ref([]);
const allTransactions = ref([]);

const loadMonthlyData = async () => {
  dataLoading.value = true;
  try {
    const transactions = await transactionService.getAllTransactions();
    allTransactions.value = transactions; // Store all transactions for prefix manager
    
    // Group transactions by month and calculate summaries
    const monthlyGroups = {};
    
    transactions.forEach(transaction => {
      const date = new Date(transaction.transaction_date);
      let year = date.getFullYear();
      const month = date.getMonth() + 1;
      
      // Normalize year: if year is less than 100, assume it's 20xx
      if (year < 100) {
        year += 2000;
      }
      
      const key = `${year}-${month}`;
      
      // Debug: log the date parsing
      console.log(`Transaction date: ${transaction.transaction_date} -> Parsed: ${date.toISOString()} -> Year: ${year}, Month: ${month}`);
      
      if (!monthlyGroups[key]) {
        monthlyGroups[key] = {
          year,
          month,
          total_money_in: 0,
          total_money_out: 0,
          final_balance: 0,
          net_change: 0,
          transaction_count: 0,
          transactions: [] // Store all transactions for this month
        };
      }
      
      // Add transaction to the month group
      monthlyGroups[key].transactions.push(transaction);
    });
    
    // Calculate local aggregates first, then fetch authoritative summaries from backend
    const monthList = Object.values(monthlyGroups);
    monthList.forEach(monthGroup => {
      // We cannot rely on intra-day ordering (no timestamps). Use order-agnostic metrics.
      
      // Calculate totals
      monthGroup.total_money_in = monthGroup.transactions
        .filter(t => t.money_in && parseFloat(t.money_in) > 0)
        .reduce((sum, t) => sum + parseFloat(t.money_in), 0);
      
      monthGroup.total_money_out = monthGroup.transactions
        .filter(t => t.money_out && parseFloat(t.money_out) > 0)
        .reduce((sum, t) => sum + parseFloat(t.money_out), 0);
      
      monthGroup.transaction_count = monthGroup.transactions.length;
      
      // Calculate net change
      monthGroup.net_change = monthGroup.total_money_in - monthGroup.total_money_out;
      
      // Remove transactions array to keep the data clean
      delete monthGroup.transactions;
    });

    // Fetch backend summaries (authoritative final_balance via chain-matching)
    const summaries = await Promise.all(
      monthList.map(m => transactionService.getSummary(m.year, m.month))
    );

    // Merge backend data into month groups
    monthList.forEach((m, idx) => {
      const s = summaries[idx];
      if (s && typeof s.final_balance === 'number') {
        m.final_balance = s.final_balance;
        // Keep local totals/net change as they already reflect current client data
      }
    });
    
    // Sort months by date (ascending order - January first, December last)
    monthlyData.value = Object.values(monthlyGroups)
      .sort((a, b) => {
        if (a.year !== b.year) return a.year - b.year;
        return a.month - b.month;
      });
    
    // Debug logging
    console.log('Monthly groups:', monthlyGroups);
    console.log('Final monthly data:', monthlyData.value);
      
  } catch (error) {
    console.error('Failed to load monthly data:', error);
  } finally {
    dataLoading.value = false;
  }
};

const handleUpload = async () => {
  // Clear monthly data first to prevent duplication
  monthlyData.value = [];
  
  // Update monthly data
  await loadMonthlyData();
};

onMounted(async () => {
  await loadMonthlyData();
});
</script>
