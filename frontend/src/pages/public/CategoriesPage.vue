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
      <div class="text-center max-w-2xl mx-auto space-y-3">
        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Property Categories</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Browse Properties by Category</h1>
        <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base">Find specific real estate categories tailored for residential living, commercial ventures, and short stays.</p>
      </div>

      <!-- Categories Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <article
          v-for="category in categories"
          :key="category.name"
          class="p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-slate-300 dark:hover:border-slate-700 hover:shadow-md transition-all duration-300 group cursor-pointer flex flex-col justify-between shadow-xs"
          @click="navigateToCategory(category.slug)"
        >
          <div class="space-y-3">
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 flex items-center justify-center text-2xl group-hover:bg-slate-200 dark:group-hover:bg-slate-700 transition-colors">
              {{ category.icon }}
            </div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white transition-colors">{{ category.name }}</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">{{ category.description }}</p>
          </div>
          <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-slate-800 dark:text-slate-200">
            <span>{{ category.count }} listings</span>
            <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
          </div>
        </article>
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

const categories = [
  { name: 'Luxury Apartments', slug: 'apartment', count: '5,890+', icon: '🏢', description: 'Modern 1, 2, and 3-bedroom condos in prime locations like Bole & Kazanchis.' },
  { name: 'Residential Villas', slug: 'villa', count: '1,234+', icon: '🏡', description: 'Spacious detached family homes with gardens and private parking.' },
  { name: 'Commercial Offices', slug: 'office', count: '432+', icon: '💼', description: 'Corporate offices, retail spaces, and business headquarters.' },
  { name: 'Plots & Land', slug: 'land', count: '987+', icon: '📐', description: 'Residential, agricultural, and industrial land ready for development.' },
  { name: 'Short Stay Rentals', slug: 'short-rental', count: '850+', icon: '🏖️', description: 'Furnished apartments and guest houses for vacations and business trips.' },
  { name: 'Warehouses & Industrial', slug: 'warehouse', count: '321+', icon: '🏭', description: 'Storage facilities, distribution centers, and light industrial spaces.' },
  { name: 'Guest Houses & Lodges', slug: 'hotel', count: '237+', icon: '🏨', description: 'Boutique hotels, lodges, and bed & breakfasts across Ethiopia.' },
  { name: 'Townhouses & Compounds', slug: 'house', count: '3,245+', icon: '🏘️', description: 'Gated compound residences offering community security and amenities.' },
]

const navigateToCategory = (slug) => {
  router.push({ path: '/properties', query: { type: slug } })
}
</script>
