<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 py-8 md:py-12 transition-colors duration-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
      <!-- Back Navigation Bar -->
      <div class="flex items-center gap-3">
        <button
          type="button"
          @click="handleGoBack"
          class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors shrink-0 cursor-pointer shadow-2xs"
          title="Go Back"
          aria-label="Go Back"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
        </button>
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Back</span>
      </div>

      <div class="border-b border-slate-200 dark:border-slate-800 pb-6">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Terms of Service</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Last Updated: August 2026</p>
      </div>

      <div class="prose prose-slate max-w-none text-xs sm:text-sm text-slate-600 dark:text-slate-300 space-y-6 leading-relaxed">
        <section class="space-y-2">
          <h2 class="text-base font-bold text-slate-900 dark:text-white">1. Acceptance of Terms</h2>
          <p>By accessing or using BetLink, you agree to be bound by these Terms of Service and our Community Guidelines. If you disagree with any portion, you may not use our services.</p>
        </section>

        <section class="space-y-2">
          <h2 class="text-base font-bold text-slate-900 dark:text-white">2. Accuracy of Property Information</h2>
          <p>Property owners and agents represent that all uploaded photos, floor dimensions, legal titles, and pricing data are accurate, authentic, and not misleading. BetLink reserves the right to suspend or remove fraudulent listings without notice.</p>
        </section>

        <section class="space-y-2">
          <h2 class="text-base font-bold text-slate-900 dark:text-white">3. User Conduct & Inquiries</h2>
          <p>Users agree to interact respectfully and avoid spamming, harassment, or unauthorized data scraping. All viewings and transactions must adhere to applicable Ethiopian real estate and financial regulations.</p>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const handleGoBack = () => {
  if (window.history.state && window.history.state.back) {
    router.back()
  } else {
    router.push('/')
  }
}
</script>
