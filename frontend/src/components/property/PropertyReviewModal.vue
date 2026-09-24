<template>
  <BaseModal
    :model-value="modelValue"
    title="Write a Property Review"
    max-width="md"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <div v-if="!authStore.isAuthenticated" class="text-center py-4">
      <div class="w-12 h-12 rounded-full bg-slate-800 text-emerald-400 mx-auto flex items-center justify-center mb-3">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
        </svg>
      </div>
      <h4 class="text-base font-semibold text-slate-100 mb-1">Login Required</h4>
      <p class="text-xs text-slate-400 mb-6">
        Please sign in or create an account to leave verified reviews and ratings.
      </p>
      <RouterLink
        :to="{ name: 'login', query: { redirect: $route.fullPath } }"
        class="inline-flex items-center justify-center w-full px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition-colors shadow-sm"
      >
        Sign In to Continue
      </RouterLink>
    </div>

    <form v-else @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Rating Stars -->
      <div>
        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
          Your Rating <span class="text-rose-400">*</span>
        </label>
        <div class="flex items-center space-x-2">
          <button
            v-for="star in 5"
            :key="star"
            type="button"
            class="p-1 text-slate-600 hover:text-amber-400 focus:outline-none transition-colors"
            @click="rating = star"
          >
            <svg
              class="w-7 h-7"
              :class="star <= rating ? 'text-amber-400 fill-current' : 'text-slate-700 fill-transparent stroke-current'"
              stroke-width="1.5"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
          </button>
          <span class="text-xs text-slate-400 ml-2 font-medium">
            {{ ratingText[rating] || 'Select rating' }}
          </span>
        </div>
      </div>

      <!-- Review Title -->
      <div>
        <label for="review-title" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
          Headline / Title <span class="text-rose-400">*</span>
        </label>
        <input
          id="review-title"
          v-model="title"
          type="text"
          maxlength="255"
          required
          placeholder="e.g. Spacious modern apartment in central Bole"
          class="w-full rounded-lg text-sm bg-slate-900 border border-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-slate-100 placeholder-slate-500 p-2.5 focus:outline-none transition-colors"
        />
      </div>

      <!-- Review Body -->
      <div>
        <label for="review-body" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
          Detailed Feedback <span class="text-rose-400">*</span>
        </label>
        <textarea
          id="review-body"
          v-model="body"
          rows="3"
          required
          placeholder="Describe your experience with this property..."
          class="w-full rounded-lg text-sm bg-slate-900 border border-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-slate-100 placeholder-slate-500 p-2.5 focus:outline-none transition-colors resize-none"
        />
      </div>

      <!-- Pros & Cons -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label for="review-pros" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
            Pros (Optional)
          </label>
          <input
            id="review-pros"
            v-model="pros"
            type="text"
            placeholder="e.g. Great view, quiet neighborhood"
            class="w-full rounded-lg text-xs bg-slate-900 border border-slate-700 focus:border-emerald-500 text-slate-100 placeholder-slate-500 p-2 focus:outline-none"
          />
        </div>
        <div>
          <label for="review-cons" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
            Cons (Optional)
          </label>
          <input
            id="review-cons"
            v-model="cons"
            type="text"
            placeholder="e.g. Traffic during peak hours"
            class="w-full rounded-lg text-xs bg-slate-900 border border-slate-700 focus:border-emerald-500 text-slate-100 placeholder-slate-500 p-2 focus:outline-none"
          />
        </div>
      </div>

      <p v-if="error" class="text-xs text-rose-400 font-medium bg-rose-950/40 p-2 rounded-lg border border-rose-800/50 flex items-center gap-1.5">
        <span>⚠️</span>
        <span>{{ error }}</span>
      </p>

      <!-- Action Buttons -->
      <div class="flex items-center justify-end space-x-3 pt-2">
        <BaseButton variant="secondary" size="md" @click="$emit('update:modelValue', false)">
          Cancel
        </BaseButton>
        <BaseButton type="submit" variant="primary" size="md" :loading="isSubmitting">
          Submit Review
        </BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { ref, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import BaseModal from '../common/BaseModal.vue'
import BaseButton from '../common/BaseButton.vue'
import { useAuthStore } from '../../stores/auth'
import { useToastStore } from '../../stores/toast'
import { reviewService } from '../../services/reviewService'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  propertyId: {
    type: [Number, String],
    required: true,
  },
})

const emit = defineEmits(['update:modelValue', 'success'])

const route = useRoute()
const authStore = useAuthStore()
const toastStore = useToastStore()

const rating = ref(5)
const title = ref('')
const body = ref('')
const pros = ref('')
const cons = ref('')
const isSubmitting = ref(false)
const error = ref('')

const ratingText = {
  1: '1 - Poor',
  2: '2 - Fair',
  3: '3 - Average',
  4: '4 - Very Good',
  5: '5 - Exceptional',
}

watch(() => props.modelValue, (isOpen) => {
  if (isOpen) {
    error.value = ''
  }
})

function resetForm() {
  rating.value = 5
  title.value = ''
  body.value = ''
  pros.value = ''
  cons.value = ''
  error.value = ''
}

const handleSubmit = async () => {
  error.value = ''

  // 1. Rating Validation
  if (!rating.value || rating.value < 1 || rating.value > 5) {
    error.value = 'Please select a rating.'
    toastStore.error(error.value)
    return
  }

  // 2. Headline / Title Validation
  const trimmedTitle = title.value.trim()
  if (!trimmedTitle) {
    error.value = 'Title is required.'
    toastStore.error(error.value)
    return
  }
  if (trimmedTitle.length < 3) {
    error.value = 'Title is too short.'
    toastStore.error(error.value)
    return
  }

  // 3. Detailed Feedback Validation
  const trimmedBody = body.value.trim()
  if (!trimmedBody) {
    error.value = 'Feedback is required.'
    toastStore.error(error.value)
    return
  }
  if (trimmedBody.length < 10) {
    error.value = 'Feedback is too short.'
    toastStore.error(error.value)
    return
  }

  isSubmitting.value = true

  try {
    await reviewService.submitReview(props.propertyId, {
      rating: rating.value,
      title: trimmedTitle,
      body: trimmedBody,
      pros: pros.value.trim() || undefined,
      cons: cons.value.trim() || undefined,
    })

    toastStore.success('Review submitted!')
    resetForm()
    emit('update:modelValue', false)
    emit('success')
  } catch (err) {
    error.value = err.response?.data?.message || err.message || 'Submission failed.'
    toastStore.error(error.value)
  } finally {
    isSubmitting.value = false
  }
}
</script>
