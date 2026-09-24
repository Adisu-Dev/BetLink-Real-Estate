<template>
  <div class="mt-3 p-4 bg-gray-50 rounded-lg space-y-2">
    <p class="text-xs font-semibold text-gray-700 mb-2">Password must contain:</p>
    <div class="space-y-1.5">
      <div
        v-for="req in requirements"
        :key="req.label"
        class="flex items-center gap-2 text-xs"
        :class="req.met ? 'text-green-600' : 'text-gray-500'"
      >
        <svg v-if="req.met" class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <svg v-else class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10"/>
        </svg>
        <span>{{ req.label }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  password: {
    type: String,
    default: ''
  }
})

const requirements = computed(() => [
  {
    label: 'At least 8 characters',
    met: props.password.length >= 8
  },
  {
    label: 'One uppercase letter',
    met: /[A-Z]/.test(props.password)
  },
  {
    label: 'One lowercase letter',
    met: /[a-z]/.test(props.password)
  },
  {
    label: 'One number',
    met: /\d/.test(props.password)
  },
  {
    label: 'One special character',
    met: /[!@#$%^&*(),.?":{}|<>]/.test(props.password)
  }
])
</script>
