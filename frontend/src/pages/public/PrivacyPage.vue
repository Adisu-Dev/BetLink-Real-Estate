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
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Privacy Policy</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Last Updated: August 2026</p>
      </div>

      <div class="prose prose-slate max-w-none text-xs sm:text-sm text-slate-600 dark:text-slate-300 space-y-6 leading-relaxed">
        <section class="space-y-2">
          <h2 class="text-base font-bold text-slate-900 dark:text-white">1. Information We Collect</h2>
          <p>BetLink collects information you provide directly to us when creating an account, publishing a property listing, or communicating with buyers and agents. This includes your name, email address, phone number, and listing verification documentation.</p>
        </section>

        <section class="space-y-2">
          <h2 class="text-base font-bold text-slate-900 dark:text-white">2. How We Use Your Information</h2>
          <p>We use the information collected to operate, maintain, and enhance the marketplace, prevent fraudulent property transactions, process verification requests, and enable seamless communication between property seekers and sellers.</p>
        </section>

        <section class="space-y-2">
          <h2 class="text-base font-bold text-slate-900 dark:text-white">3. Data Security & Storage</h2>
          <p>We implement industry-standard encryption and security protocols to safeguard your personal data and property documents. We never sell your personal information to third-party advertisers.</p>
        </section>

        <section class="space-y-2">
          <h2 class="text-base font-bold text-slate-900 dark:text-white">4. Contact Us Regarding Privacy</h2>
          <p>If you have any questions or requests regarding your data, please contact our data protection team at privacy@betlink.et.</p>
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
