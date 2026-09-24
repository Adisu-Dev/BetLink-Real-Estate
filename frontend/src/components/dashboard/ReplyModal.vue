<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div
      @click="closeModal"
      class="absolute inset-0 bg-black/50 transition-opacity duration-200"
    ></div>

    <!-- Modal -->
    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-900">Reply to Inquiry</h2>
        <button
          @click="closeModal"
          class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div class="p-6 space-y-4">
        <!-- Original Message -->
        <div v-if="message" class="bg-gray-50 rounded-lg p-4 border border-gray-200">
          <p class="text-sm text-gray-600 mb-2">
            <strong>From:</strong> {{ message.sender_name }}
          </p>
          <p class="text-sm text-gray-700">{{ message.content }}</p>
          <p class="text-xs text-gray-500 mt-2">{{ formatDate(message.created_at) }}</p>
        </div>

        <!-- Reply Form -->
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <!-- Reply Text -->
          <div>
            <label for="reply" class="block text-sm font-medium text-gray-700 mb-2">
              Your Reply <span class="text-red-600">*</span>
            </label>
            <textarea
              id="reply"
              v-model="form.content"
              rows="5"
              placeholder="Type your reply here..."
              :class="[
                'w-full px-4 py-2 rounded-lg border transition-all duration-200',
                'focus:outline-none focus:ring-2',
                errors.content 
                  ? 'border-red-300 focus:ring-red-500' 
                  : 'border-gray-300 focus:ring-blue-500'
              ]"
              @blur="validateField('content')"
              @input="errors.content = ''"
            />
            <p v-if="errors.content" class="mt-1 text-sm text-red-600">{{ errors.content }}</p>
          </div>

          <!-- Error Message -->
          <transition name="fade">
            <div v-if="submitError" class="p-3 bg-red-50 border border-red-200 rounded-lg">
              <p class="text-sm text-red-700">{{ submitError }}</p>
            </div>
          </transition>

          <!-- Buttons -->
          <div class="flex gap-3 pt-4 border-t border-gray-200">
            <button
              type="button"
              @click="closeModal"
              :disabled="loading"
              class="flex-1 px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <svg v-if="loading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ loading ? 'Sending...' : 'Send Reply' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useMessage } from '../../composables/useMessage'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  message: {
    type: Object,
    default: null
  },
  conversationId: {
    type: [String, Number],
    required: true
  }
})

const emit = defineEmits(['close', 'message-sent'])

const { sendMessage } = useMessage()
const loading = ref(false)
const submitError = ref('')

const form = reactive({
  content: ''
})

const errors = reactive({
  content: ''
})

const validateField = (fieldName) => {
  errors[fieldName] = ''

  switch (fieldName) {
    case 'content':
      if (!form.content.trim()) {
        errors.content = 'Reply cannot be empty'
      } else if (form.content.trim().length < 5) {
        errors.content = 'Reply must be at least 5 characters'
      }
      break
  }
}

const validateForm = () => {
  validateField('content')
  return !errors.content
}

const handleSubmit = async () => {
  submitError.value = ''

  if (!validateForm()) {
    return
  }

  loading.value = true

  try {
    const messageData = {
      content: form.content
    }

    const result = await sendMessage(props.conversationId, messageData)

    if (result.success) {
      emit('message-sent', result.data)
      resetForm()
      closeModal()
    } else {
      submitError.value = result.error || 'Failed to send message'
    }
  } catch (err) {
    console.error('Error sending message:', err)
    submitError.value = 'An unexpected error occurred. Please try again.'
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  form.content = ''
  errors.content = ''
  submitError.value = ''
}

const closeModal = () => {
  resetForm()
  emit('close')
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
