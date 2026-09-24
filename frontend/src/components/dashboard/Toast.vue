<template>
  <transition-group
    tag="div"
    name="toast"
    class="fixed top-5 right-4 sm:top-6 sm:right-6 z-[9999] space-y-3 pointer-events-none"
  >
    <div
      v-for="toast in toasts"
      :key="toast.id"
      :class="[
        'flex items-start gap-3 rounded-lg shadow-lg p-4 pointer-events-auto',
        toast.type === 'success' && 'bg-green-50 border border-green-200',
        toast.type === 'error' && 'bg-red-50 border border-red-200',
        toast.type === 'info' && 'bg-blue-50 border border-blue-200',
        toast.type === 'warning' && 'bg-yellow-50 border border-yellow-200'
      ]"
    >
      <!-- Icon -->
      <div :class="[
        'flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-full',
        toast.type === 'success' && 'bg-green-100',
        toast.type === 'error' && 'bg-red-100',
        toast.type === 'info' && 'bg-blue-100',
        toast.type === 'warning' && 'bg-yellow-100'
      ]">
        <svg
          v-if="toast.type === 'success'"
          class="h-4 w-4 text-green-600"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <svg
          v-else-if="toast.type === 'error'"
          class="h-4 w-4 text-red-600"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <svg
          v-else-if="toast.type === 'warning'"
          class="h-4 w-4 text-yellow-600"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <svg
          v-else
          class="h-4 w-4 text-blue-600"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
        </svg>
      </div>

      <!-- Content -->
      <div class="flex-1">
        <p :class="[
          'text-sm font-medium',
          toast.type === 'success' && 'text-green-800',
          toast.type === 'error' && 'text-red-800',
          toast.type === 'info' && 'text-blue-800',
          toast.type === 'warning' && 'text-yellow-800'
        ]">
          {{ toast.title }}
        </p>
        <p v-if="toast.message" :class="[
          'text-sm mt-1',
          toast.type === 'success' && 'text-green-700',
          toast.type === 'error' && 'text-red-700',
          toast.type === 'info' && 'text-blue-700',
          toast.type === 'warning' && 'text-yellow-700'
        ]">
          {{ toast.message }}
        </p>
      </div>

      <!-- Close Button -->
      <button
        @click="removeToast(toast.id)"
        :class="[
          'flex-shrink-0 inline-flex text-gray-400 hover:text-gray-500 focus:outline-none',
          toast.type === 'success' && 'hover:text-green-600',
          toast.type === 'error' && 'hover:text-red-600',
          toast.type === 'info' && 'hover:text-blue-600',
          toast.type === 'warning' && 'hover:text-yellow-600'
        ]"
      >
        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>

      <!-- Progress Bar -->
      <div
        v-if="toast.duration"
        class="absolute bottom-0 left-0 h-1 bg-current opacity-30"
        :style="{ animation: `shrink ${toast.duration}ms linear forwards` }"
      />
    </div>
  </transition-group>
</template>

<script setup>
import { ref } from 'vue'

const toasts = ref([])
let toastId = 0

const showToast = (title, options = {}) => {
  const {
    message = '',
    type = 'info',
    duration = 5000
  } = options

  const id = toastId++
  const toast = { id, title, message, type, duration }

  toasts.value.push(toast)

  if (duration > 0) {
    setTimeout(() => {
      removeToast(id)
    }, duration)
  }

  return id
}

const removeToast = (id) => {
  toasts.value = toasts.value.filter(t => t.id !== id)
}

const success = (title, message = '') => showToast(title, { message, type: 'success' })
const error = (title, message = '') => showToast(title, { message, type: 'error' })
const info = (title, message = '') => showToast(title, { message, type: 'info' })
const warning = (title, message = '') => showToast(title, { message, type: 'warning' })

defineExpose({
  showToast,
  removeToast,
  success,
  error,
  info,
  warning,
  toasts
})
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(30px);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

@keyframes shrink {
  from {
    width: 100%;
  }
  to {
    width: 0%;
  }
}
</style>
