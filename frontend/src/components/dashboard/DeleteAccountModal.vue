<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div
      @click="$emit('cancel')"
      class="absolute inset-0 bg-black/50 transition-opacity duration-200"
    ></div>

    <!-- Modal -->
    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md">
      <!-- Header -->
      <div class="bg-red-50 border-b border-red-200 px-6 py-4 rounded-t-xl">
        <h2 class="text-xl font-bold text-red-900">Delete Account</h2>
      </div>

      <!-- Content -->
      <div class="p-6 space-y-6">
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
          <p class="text-sm text-red-900">
            <strong>Warning:</strong> Deleting your account is permanent and cannot be undone. All your data will be permanently deleted.
          </p>
        </div>

        <p class="text-gray-700">
          To confirm deletion, type <span class="font-mono font-bold text-red-600">DELETE</span> in the field below:
        </p>

        <!-- Confirmation Input -->
        <input
          v-model="confirmText"
          type="text"
          placeholder="Type DELETE to confirm"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
          @keyup.enter="handleConfirm"
        />

        <!-- Buttons -->
        <div class="flex gap-3 pt-4 border-t border-gray-200">
          <button
            @click="$emit('cancel')"
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors"
          >
            Cancel
          </button>
          <button
            @click="handleConfirm"
            :disabled="!canDelete || loading"
            class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <svg v-if="loading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ loading ? 'Deleting...' : 'Delete Account' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

defineProps({
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['confirm', 'cancel'])

const confirmText = ref('')

const canDelete = computed(() => confirmText.value === 'DELETE')

const handleConfirm = () => {
  if (canDelete.value) {
    emit('confirm')
  }
}
</script>
