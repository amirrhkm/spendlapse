<template>
  <div class="min-h-screen bg-black flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
      <div class="bg-gray-900 rounded-xl shadow-lg border border-gray-700 p-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-white mb-2">{{ $t('auth.register.title') }}</h1>
          <p class="text-gray-400">{{ $t('auth.register.subtitle') }}</p>
        </div>

        <!-- Register Form -->
        <form @submit.prevent="handleRegister" class="space-y-6">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
              {{ $t('auth.name') }}
            </label>
            <input 
              id="name"
              v-model="form.name"
              type="text"
              :placeholder="$t('auth.namePlaceholder')"
              class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-colors"
              required
            />
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
              {{ $t('auth.email') }}
            </label>
            <input 
              id="email"
              v-model="form.email"
              type="email"
              :placeholder="$t('auth.emailPlaceholder')"
              class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-colors"
              required
            />
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
              {{ $t('auth.password') }}
            </label>
            <input 
              id="password"
              v-model="form.password"
              type="password"
              :placeholder="$t('auth.passwordPlaceholder')"
              class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-colors"
              required
            />
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
              {{ $t('auth.confirmPassword') }}
            </label>
            <input 
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              :placeholder="$t('auth.confirmPasswordPlaceholder')"
              class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-colors"
              required
            />
          </div>

          <div>
            <label for="bank_preference" class="block text-sm font-medium text-gray-300 mb-2">
              {{ $t('auth.bankPreference') }}
            </label>
            <select 
              id="bank_preference"
              v-model="form.bank_preference"
              class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-colors"
              required
            >
              <option value="CIMB">CIMB</option>
              <option value="MAE" disabled>MAE ({{ $t('auth.comingSoon') }})</option>
              <option value="Bank Rakyat" disabled>Bank Rakyat ({{ $t('auth.comingSoon') }})</option>
              <option value="Bank Islam" disabled>Bank Islam ({{ $t('auth.comingSoon') }})</option>
            </select>
            <p class="text-xs text-gray-500 mt-1">{{ $t('auth.bankPreferenceHelp') }}</p>
          </div>

          <!-- Error Messages -->
          <div v-if="errors.length > 0" class="bg-red-900/50 border border-red-500 rounded-lg p-3">
            <ul class="text-red-400 text-sm space-y-1">
              <li v-for="error in errors" :key="error">{{ error }}</li>
            </ul>
          </div>

          <button 
            type="submit"
            :disabled="loading"
            class="w-full px-4 py-3 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-medium rounded-lg transition-colors flex items-center justify-center"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loading ? $t('auth.register.registering') : $t('auth.register.button') }}
          </button>
        </form>

        <!-- Login Link -->
        <div class="mt-6 text-center">
          <p class="text-gray-400 text-sm">
            {{ $t('auth.register.hasAccount') }}
            <router-link to="/login" class="text-purple-400 hover:text-purple-300 font-medium">
              {{ $t('auth.register.loginLink') }}
            </router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { authService } from '../services/AuthService';

const router = useRouter();
const loading = ref(false);
const errors = ref([]);

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  bank_preference: 'CIMB'
});

const handleRegister = async () => {
  loading.value = true;
  errors.value = [];

  try {
    const response = await authService.register(form.value);
    
    if (response.success) {
      router.push('/');
    } else {
      if (response.errors) {
        // Handle validation errors
        Object.values(response.errors).forEach(errorArray => {
          errors.value.push(...errorArray);
        });
      } else {
        errors.value.push(response.message || 'Registration failed');
      }
    }
  } catch (err) {
    errors.value.push('Network error. Please try again.');
    console.error('Registration error:', err);
  } finally {
    loading.value = false;
  }
};
</script>
