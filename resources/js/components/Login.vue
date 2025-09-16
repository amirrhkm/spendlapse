<template>
  <div class="min-h-screen bg-black flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
      <div class="bg-gray-900 rounded-xl shadow-lg border border-gray-700 p-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-white mb-2">{{ $t('auth.login.title') }}</h1>
          <p class="text-gray-400">{{ $t('auth.login.subtitle') }}</p>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="handleLogin" class="space-y-6">
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

          <!-- Error Message -->
          <div v-if="error" class="bg-red-900/50 border border-red-500 rounded-lg p-3">
            <p class="text-red-400 text-sm">{{ error }}</p>
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
            {{ loading ? $t('auth.login.logging') : $t('auth.login.button') }}
          </button>
        </form>

        <!-- Register Link -->
        <div class="mt-6 text-center">
          <p class="text-gray-400 text-sm">
            {{ $t('auth.login.noAccount') }}
            <router-link to="/register" class="text-purple-400 hover:text-purple-300 font-medium">
              {{ $t('auth.login.registerLink') }}
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
const error = ref('');

const form = ref({
  email: '',
  password: ''
});

const handleLogin = async () => {
  loading.value = true;
  error.value = '';

  try {
    const response = await authService.login(form.value.email, form.value.password);
    
    if (response.success) {
      router.push('/');
    } else {
      error.value = response.message || 'Login failed';
    }
  } catch (err) {
    error.value = 'Network error. Please try again.';
    console.error('Login error:', err);
  } finally {
    loading.value = false;
  }
};
</script>
