<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 py-8 md:py-12 transition-colors duration-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
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
      <div class="text-center space-y-3">
        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Help & Support</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Frequently Asked Questions</h1>
        <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base">Find answers to common questions about buying, renting, and listing properties on BetLink.</p>
      </div>

      <!-- FAQ Accordion -->
      <div class="space-y-4">
        <div
          v-for="(faq, idx) in faqs"
          :key="idx"
          class="border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 bg-white dark:bg-slate-900 shadow-xs hover:border-slate-300 dark:hover:border-slate-700 transition-colors"
        >
          <button
            type="button"
            class="w-full flex items-center justify-between text-left gap-4 cursor-pointer"
            @click="faq.open = !faq.open"
          >
            <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">{{ faq.q }}</h2>
            <span class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold text-xs flex-shrink-0">
              {{ faq.open ? '−' : '+' }}
            </span>
          </button>
          <div v-if="faq.open" class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
            {{ faq.a }}
          </div>
        </div>
      </div>

      <!-- Still have questions -->
      <div class="p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-center space-y-3 shadow-xs">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Still have unanswered questions?</h2>
        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">Our customer support team in Addis Ababa is available to assist you.</p>
        <RouterLink to="/contact" class="inline-block px-6 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold transition-colors shadow-2xs">
          Contact Customer Support
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { RouterLink, useRouter } from 'vue-router'

const router = useRouter()

const handleGoBack = () => {
  if (window.history.state && window.history.state.back) {
    router.back()
  } else {
    router.push('/')
  }
}

const faqs = reactive([
  {
    q: 'How does BetLink verify properties listed on the platform?',
    a: 'Every property submitted for approval is vetted by our compliance team. We review ownership title deeds, site location coordinates, and owner identity before the listing is tagged as Verified.',
    open: true,
  },
  {
    q: 'Are there any fees for buyers or tenants to search for properties?',
    a: 'No. Searching, filtering, viewing property details, saving favorites, and sending viewing inquiries is 100% free for all buyers and tenants.',
    open: false,
  },
  {
    q: 'How do I list my apartment or house on BetLink?',
    a: 'Create a free account, go to your Owner Dashboard, and click "List Property". Fill in property dimensions, features, and upload photos. Once reviewed, your property goes live across Ethiopia.',
    open: false,
  },
  {
    q: 'Can Ethiopians in the diaspora purchase or rent properties through BetLink?',
    a: 'Yes! Diaspora buyers can browse verified listings in foreign currencies or ETB, schedule virtual video tours with certified agents, and connect directly with verified real estate developers.',
    open: false,
  },
  {
    q: 'How do short-stay bookings and calendar appointments work?',
    a: 'Short-stay listings have real-time calendar availability. You can choose check-in and check-out dates and send reservation requests directly to the host.',
    open: false,
  },
])
</script>
