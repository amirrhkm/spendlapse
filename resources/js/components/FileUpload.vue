<template>
  <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-4">
    <!-- File Drop Zone - Compact -->
    <div 
      @drop="handleDrop"
      @dragover.prevent
      @dragenter.prevent
      class="flex-1 border-2 border-dashed border-gray-600 rounded-lg p-4 transition-colors hover:border-blue-500 hover:bg-gray-700/30"
      :class="{ 'border-blue-500 bg-gray-700/50': isDragging }"
    >
      <div class="flex items-center space-x-3">
        <svg class="w-8 h-8 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        
        <div class="flex-1 min-w-0">
          <p class="text-gray-300 text-sm">
            <span v-if="!selectedFile">{{ $t('fileUpload.dropHere') }} </span>
            <button 
              v-if="!selectedFile"
              @click="triggerFileInput"
              class="text-blue-400 hover:text-blue-300 font-medium"
            >
              {{ $t('fileUpload.browseFiles') }}
            </button>
            <span v-if="selectedFile" class="text-white font-medium">{{ selectedFile.name }}</span>
          </p>
          <p v-if="!selectedFile" class="text-xs text-gray-500">{{ $t('fileUpload.fileTypes') }}, {{ $t('fileUpload.maxSize') }}</p>
          <p v-if="selectedFile" class="text-xs text-gray-400">{{ formatFileSize(selectedFile.size) }}</p>
        </div>
        
        <button 
          v-if="selectedFile"
          @click="clearFile"
          class="text-gray-400 hover:text-red-400 p-1"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <input 
        ref="fileInput"
        type="file"
        accept=".csv,.txt"
        @change="handleFileSelect"
        class="hidden"
      />
    </div>
    
    <!-- Upload Button - Compact -->
    <button 
      @click="uploadFile"
      :disabled="!selectedFile || loading"
      class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center justify-center flex-shrink-0"
    >
      <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      {{ loading ? $t('fileUpload.processing') : $t('fileUpload.upload') }}
    </button>
  </div>
  
  <!-- Success/Error Messages - Inline -->
  <div v-if="message" class="mt-3 p-3 rounded-lg text-sm" :class="messageClass">
    {{ message }}
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { transactionService } from '../services/TransactionService';

const emit = defineEmits(['uploaded']);
const props = defineProps({
  loading: {
    type: Boolean,
    default: false
  }
});

const selectedFile = ref(null);
const isDragging = ref(false);
const fileInput = ref(null);
const message = ref('');
const messageType = ref('');
const loading = ref(false);

const messageClass = computed(() => {
  return messageType.value === 'success' 
    ? 'bg-green-900/50 text-green-300 border border-green-700'
    : 'bg-red-900/50 text-red-300 border border-red-700';
});

const triggerFileInput = () => {
  fileInput.value.click();
};

const handleFileSelect = (event) => {
  const file = event.target.files[0];
  if (file) {
    selectedFile.value = file;
  }
};

const handleDrop = (event) => {
  event.preventDefault();
  isDragging.value = false;
  
  const file = event.dataTransfer.files[0];
  if (file && (file.type === 'text/csv' || file.name.endsWith('.csv'))) {
    selectedFile.value = file;
  }
};

const clearFile = () => {
  selectedFile.value = null;
  fileInput.value.value = '';
  message.value = '';
};

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const uploadFile = async () => {
  if (!selectedFile.value) return;
  
  loading.value = true;
  message.value = '';
  
  try {
    const response = await transactionService.uploadFile(selectedFile.value);
    
          if (response.message) {
        message.value = response.message;
        messageType.value = 'success';
        emit('uploaded');
        clearFile();
      }
  } catch (error) {
    message.value = $t('fileUpload.uploadError');
    messageType.value = 'error';
  } finally {
    loading.value = false;
  }
};
</script>
