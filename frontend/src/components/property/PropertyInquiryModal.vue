<template>
  <BaseModal
    :model-value="modelValue"
    title="Send Inquiry to Owner"
    max-width="md"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <div v-if="!authStore.isAuthenticated" class="text-center py-4">
      <div class="w-12 h-12 rounded-full bg-slate-800 text-emerald-400 mx-auto flex items-center justify-center mb-3">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
      </div>
      <h4 class="text-base font-semibold text-slate-100 mb-1">Login Required</h4>
      <p class="text-xs text-slate-400 mb-6">
        Please sign in or create an account to send direct messages to property owners.
      </p>
      <RouterLink
        :to="{ name: 'login', query: { redirect: $route.fullPath } }"
        class="inline-flex items-center justify-center w-full px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition-colors shadow-sm"
      >
        Sign In to Continue
      </RouterLink>
    </div>

    <form v-else @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Property Summary -->
      <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 flex items-center space-x-3">
        <div class="w-12 h-12 rounded-lg bg-slate-800 overflow-hidden flex-shrink-0">
          <img :src="propertyThumbnail" :alt="property.title" class="w-full h-full object-cover" @error="handleImgError" />
        </div>
        <div class="flex-1 min-w-0">
          <h5 class="text-xs font-semibold text-slate-200 truncate">{{ property.title }}</h5>
          <p class="text-xs text-emerald-400 font-bold mt-0.5">{{ formatPrice(property.price, property.currency || 'ETB') }}</p>
        </div>
      </div>

      <!-- Quick Message Templates -->
      <div>
        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
          Quick Templates
        </label>
        <div class="flex flex-wrap gap-1.5">
          <button
            v-for="(template, idx) in quickTemplates"
            :key="idx"
            type="button"
            class="text-[11px] px-2.5 py-1 rounded-md bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors border border-slate-700"
            @click="message = template"
          >
            {{ template }}
          </button>
        </div>
      </div>

      <!-- Message text area -->
      <div>
        <label for="inquiry-message" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
          Your Message <span class="text-rose-400">*</span>
        </label>
        <textarea
          id="inquiry-message"
          v-model="message"
          rows="4"
          required
          maxlength="2000"
          placeholder="Write your question or request to the property owner..."
          class="w-full rounded-lg text-sm bg-slate-900 border border-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-slate-100 placeholder-slate-500 p-3 focus:outline-none transition-colors resize-none"
        />
        <p v-if="error" class="mt-1 text-xs text-rose-400">{{ error }}</p>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-end space-x-3 pt-2">
        <BaseButton variant="secondary" size="md" @click="$emit('update:modelValue', false)">
          Cancel
        </BaseButton>
        <BaseButton type="submit" variant="primary" size="md" :loading="isSubmitting">
          Send Message
        </BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { ref, computed } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import BaseModal from '../common/BaseModal.vue'
import BaseButton from '../common/BaseButton.vue'
import { formatPrice } from '../../utils/formatters'
import { useAuthStore } from '../../stores/auth'
import { useToastStore } from '../../stores/toast'
import { conversationService } from '../../services/conversationService'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  property: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['update:modelValue', 'success'])

const route = useRoute()
const authStore = useAuthStore()
const toastStore = useToastStore()

const message = ref('Hi, I am interested in this property. Is it still available for viewing?')
const isSubmitting = ref(false)
const error = ref('')

const quickTemplates = [
  'Is this property still available?',
  'Can I schedule a viewing this weekend?',
  'Is the price negotiable?',
]

const fallbackThumbnail = 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=200&q=80'

const propertyThumbnail = computed(() => {
  const p = props.property
  return (
    p.primary_image?.image_url ||
    p.primary_image?.url ||
    p.primaryImage?.url ||
    p.primaryImage?.image_url ||
    p.images?.[0]?.image_url ||
    p.images?.[0]?.url ||
    fallbackThumbnail
  )
})

const handleImgError = (e) => {
  e.target.src = fallbackThumbnail
}

const handleSubmit = async () => {
  if (!message.value.trim()) {
    error.value = 'Please enter a message.'
    return
  }

  isSubmitting.value = true
  error.value = ''

  try {
    const ownerId = props.property.user_id || props.property.owner?.id || props.property.owner_id
    if (!ownerId) {
      throw new Error('Property owner contact is currently not available.')
    }

    if (ownerId === authStore.user?.id) {
      throw new Error('You cannot send an inquiry on your own property listing.')
    }

    await conversationService.startConversation({
      property_id: props.property.id,
      recipient_id: ownerId,
      initial_message: message.value.trim(),
    })

    toastStore.success('Inquiry sent!')
    emit('update:modelValue', false)
    emit('success')
  } catch (err) {
    error.value = err.message || 'Failed to send inquiry.'
    toastStore.error(error.value)
  } finally {
    isSubmitting.value = false
  }
}
</script>
