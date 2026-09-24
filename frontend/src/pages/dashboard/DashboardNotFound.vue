<template>
  <div class="flex flex-col items-center justify-center py-12 px-4">
    <div class="text-center">
      <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <h1 class="mt-4 text-3xl font-bold text-gray-900">Page Not Found</h1>
      <p class="mt-2 text-gray-600">The page you're looking for doesn't exist.</p>
      <RouterLink
        :to="`/${userRole}/dashboard`"
        class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors"
      >
        ← Back to Dashboard
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuth } from '../../composables/useAuth'

const { user } = useAuth()

const userRole = computed(() => {
  return user.value?.role || 'buyer'
})
</script>
