<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 py-8 md:py-12 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
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
      <div class="text-center max-w-3xl mx-auto space-y-3">
        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Transparent Pricing</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Flexible Plans for Owners & Real Estate Agents</h1>
        <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base">Buyers and tenants search 100% free forever. Choose a listing package that suits your property portfolio.</p>
      </div>

      <!-- Pricing Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Basic Plan -->
        <div class="p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col justify-between space-y-6">
          <div class="space-y-4">
            <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">Free Tier</span>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Individual Owner</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Perfect for private landlords listing an apartment or home.</p>
            <div class="text-3xl font-extrabold text-slate-900 dark:text-white">Free <span class="text-xs font-normal text-slate-500 dark:text-slate-400">/ first 30 days</span></div>
            <ul class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300 pt-4 border-t border-slate-100 dark:border-slate-800">
              <li class="flex items-center gap-2">✓ 1 Active Listing</li>
              <li class="flex items-center gap-2">✓ Up to 10 HD Photos</li>
              <li class="flex items-center gap-2">✓ Direct Client Messaging</li>
              <li class="flex items-center gap-2">✓ Standard Support</li>
            </ul>
          </div>
          <RouterLink to="/register" class="w-full py-3 text-center rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-bold text-xs transition-colors">
            Get Started Free
          </RouterLink>
        </div>

        <!-- Featured Plan -->
        <div class="p-8 rounded-3xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-md relative flex flex-col justify-between space-y-6">
          <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full shadow-md">
            Most Popular
          </div>
          <div class="space-y-4">
            <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200">Pro Listing</span>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Premium Owner</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Maximum visibility for prime homes and luxury properties.</p>
            <div class="text-3xl font-extrabold text-slate-900 dark:text-white">ETB 1,500 <span class="text-xs font-normal text-slate-500 dark:text-slate-400">/ property / month</span></div>
            <ul class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300 pt-4 border-t border-slate-100 dark:border-slate-800">
              <li class="flex items-center gap-2">✓ Featured on Home Page & Top of Search</li>
              <li class="flex items-center gap-2">✓ Unlimited HD Photos & Video Tours</li>
              <li class="flex items-center gap-2">✓ Verified Badge & Priority Screening</li>
              <li class="flex items-center gap-2">✓ 5x Inquiries & Direct WhatsApp Link</li>
            </ul>
          </div>
          <RouterLink to="/register" class="w-full py-3 text-center rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-bold text-xs transition-colors shadow-2xs">
            List as Premium
          </RouterLink>
        </div>

        <!-- Agency Plan -->
        <div class="p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col justify-between space-y-6">
          <div class="space-y-4">
            <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">Agency & Developer</span>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Certified Agency</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">For brokerages, real estate agencies, and property developers.</p>
            <div class="text-3xl font-extrabold text-slate-900 dark:text-white">ETB 6,000 <span class="text-xs font-normal text-slate-500 dark:text-slate-400">/ month</span></div>
            <ul class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300 pt-4 border-t border-slate-100 dark:border-slate-800">
              <li class="flex items-center gap-2">✓ Up to 30 Active Listings</li>
              <li class="flex items-center gap-2">✓ Dedicated Agent Profile & Team Management</li>
              <li class="flex items-center gap-2">✓ Lead CRM & Appointment Calendar</li>
              <li class="flex items-center gap-2">✓ Dedicated Account Manager</li>
            </ul>
          </div>
          <RouterLink to="/contact" class="w-full py-3 text-center rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-bold text-xs transition-colors">
            Contact Sales
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
