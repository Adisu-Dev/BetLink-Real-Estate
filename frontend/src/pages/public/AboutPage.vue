<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 py-8 md:py-12 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
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

      <!-- Header -->
      <div class="text-center max-w-3xl mx-auto space-y-4">
        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">About BetLink</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
          Empowering Ethiopian Real Estate with Trust & Modern Technology
        </h1>
        <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base leading-relaxed">
          BetLink is Ethiopia’s premier digital property marketplace connecting buyers, renters, owners, and verified agents with complete transparency.
        </p>
      </div>

      <!-- Hero Image / Feature -->
      <div class="relative rounded-3xl overflow-hidden shadow-xl border border-slate-200 dark:border-slate-800 aspect-[21/9]">
        <img
          src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1600&auto=format&fit=crop&q=80"
          alt="Modern Addis Ababa skyline"
          class="w-full h-full object-cover"
          loading="lazy"
          decoding="async"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/40 to-transparent flex items-end p-6 sm:p-10">
          <div class="text-white space-y-1">
            <h2 class="sm:text-xs md:text-xl text-slate-100 font-bold">Building Digital Infrastructure for Ethiopian Real Estate</h2>
            <p class="text-xs sm:text-sm text-slate-300">Headquartered in Addis Ababa, Ethiopia</p>
          </div>
        </div>
      </div>

      <!-- Values Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
        <div class="p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 space-y-3 shadow-xs">
          <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 flex items-center justify-center font-bold text-xl">
            ✓
          </div>
          <h2 class="text-lg font-bold text-slate-900 dark:text-white">Verified Listings Only</h2>
          <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
            We manually verify ownership records, physical locations, and credentials to protect you from fraudulent real estate schemes.
          </p>
        </div>

        <div class="p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 space-y-3 shadow-xs">
          <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 flex items-center justify-center font-bold text-xl">
            ⚡
          </div>
          <h2 class="text-lg font-bold text-slate-900 dark:text-white">Direct Contact & Zero Clutter</h2>
          <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
            Communicate directly with verified landlords, real estate developers, and certified agents without middlemen markups.
          </p>
        </div>

        <div class="p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 space-y-3 shadow-xs">
          <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 flex items-center justify-center font-bold text-xl">
            🌍
          </div>
          <h2 class="text-lg font-bold text-slate-900 dark:text-white">Diaspora Friendly</h2>
          <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
            Easily browse, inspect, and arrange properties in Ethiopia from anywhere in the world with high-resolution virtual previews.
          </p>
        </div>
      </div>

      <!-- CTA -->
      <div class="p-6 sm:p-8 rounded-2xl bg-slate-900 dark:bg-slate-900 border border-slate-800 text-white text-center space-y-4">
        <h2 class="text-xl sm:text-2xl font-extrabold">Ready to explore property in Ethiopia?</h2>
        <p class="text-slate-300 text-sm max-w-xl mx-auto">Browse thousands of active listings or list your own property today.</p>
        <div class="flex flex-wrap justify-center gap-3 pt-2">
          <RouterLink to="/properties" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs sm:text-sm transition-colors">
            Explore Properties
          </RouterLink>
          <RouterLink to="/contact" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs sm:text-sm transition-colors">
            Contact Team
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { RouterLink, useRouter } from 'vue-router'

const router = useRouter()

const handleGoBack = () => {
  if (window.history.state && window.history.state.back) {
    router.back()
  } else {
    router.push('/')
  }
}
</script>
