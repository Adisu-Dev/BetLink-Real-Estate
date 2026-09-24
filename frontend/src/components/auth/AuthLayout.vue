<template>
  <div class="min-h-[calc(100vh-5rem)] bg-slate-50 dark:bg-slate-950 flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 transition-colors duration-200">
    <div class="w-full max-w-md">
      <!-- Centered Logo (optional) -->
      <div v-if="showLogo" class="mb-8 text-center">
        <RouterLink to="/" class="inline-flex items-center gap-2.5 group">
          <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center shadow-md shadow-emerald-900/20">
            <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 text-white" aria-hidden="true">
              <path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
              <path d="M9 21V12h6v9" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            </svg>
          </div>
          <span class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
            Bet<span class="text-emerald-600">Link</span>
          </span>
        </RouterLink>
      </div>

      <!-- Centered Card Container -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm sm:shadow-md border border-slate-200/80 dark:border-slate-800 p-7 sm:p-9 transition-colors">
        <slot />
      </div>

      <!-- Centered Footer Info (optional) -->
      <p v-if="showFooter" class="mt-6 text-center text-xs text-slate-500 dark:text-slate-400">
        Secured by industry-standard encryption &bull; &copy; {{ new Date().getFullYear() }} BetLink
      </p>
    </div>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router'

defineProps({
  showLogo: {
    type: Boolean,
    default: true
  },
  showFooter: {
    type: Boolean,
    default: true
  }
})
</script>
