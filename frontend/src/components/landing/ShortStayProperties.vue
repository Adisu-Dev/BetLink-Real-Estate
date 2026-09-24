<template>
  <section class="py-12 md:py-16 bg-slate-50/60 dark:bg-slate-950/60 border-y border-slate-200/80 dark:border-slate-800 transition-colors duration-200" aria-label="Short stay properties">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section header -->
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
        <div class="max-w-2xl">
          <p class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-2">
            Flexible Accommodations
          </p>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
            Short Stay & Vacation Rentals
          </h2>
          <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 mt-2">
            Fully furnished studios, apartments, and guest villas for your temporary stay or holiday.
          </p>
        </div>

        <RouterLink
          to="/properties?listing_type=short_rent"
          class="inline-flex items-center text-sm font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors group flex-shrink-0"
        >
          <span>Explore short stays</span>
          <svg class="w-4 h-4 ml-1.5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </RouterLink>
      </div>

      <!-- Loading Skeleton State -->
      <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div
          v-for="i in 4"
          :key="i"
          class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl h-80 animate-pulse flex flex-col justify-between p-4"
        >
          <div class="w-full h-44 bg-slate-200 dark:bg-slate-800 rounded-xl" />
          <div class="space-y-2 mt-4">
            <div class="w-3/4 h-4 bg-slate-200 dark:bg-slate-800 rounded" />
            <div class="w-1/2 h-3 bg-slate-200 dark:bg-slate-800 rounded" />
          </div>
        </div>
      </div>

      <!-- Error State with Retry Button -->
      <div v-else-if="errorMessage" class="text-center py-12 px-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm">
        <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-950/30 text-rose-500 flex items-center justify-center mx-auto mb-3">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">Failed to load short stay properties</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-4">{{ errorMessage }}</p>
        <button
          type="button"
          class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white transition-colors"
          @click="fetchShortStayProperties"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Try Again
        </button>
      </div>

      <!-- Empty State -->
      <div v-else-if="shortStayProperties.length === 0" class="text-center py-12 px-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm">
        <p class="text-slate-600 dark:text-slate-400 text-sm font-medium">No short stay properties currently available.</p>
        <RouterLink
          to="/properties"
          class="mt-3 inline-flex items-center text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700"
        >
          Browse all properties &rarr;
        </RouterLink>
      </div>

      <!-- Property Grid (Symmetrically balanced rows without empty spaces) -->
      <div v-else :class="gridClass">
        <PropertyCard
          v-for="property in displayedShortStays"
          :key="property.id"
          :property="property"
        />
      </div>

      <!-- View All CTA -->
      <div v-if="shortStayProperties.length > 0" class="text-center mt-10">
        <RouterLink
          to="/properties?listing_type=short_rent"
          class="inline-flex items-center justify-center px-7 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white/10 dark:hover:bg-white/20 text-white font-bold text-xs sm:text-sm shadow-md transition-all active:scale-95"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          View All Short Stays
        </RouterLink>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import PropertyCard from '../property/PropertyCard.vue'
import { propertyService } from '../../services/propertyService'

const shortStayProperties = ref([])
const isLoading = ref(true)
const errorMessage = ref('')

const displayedShortStays = computed(() => {
  const list = shortStayProperties.value || []
  if (list.length === 0) return []
  if (list.length >= 8) return list.slice(0, 8)
  if (list.length >= 6) return list.slice(0, 6)
  if (list.length === 5) return list.slice(0, 4)
  if (list.length === 4) return list.slice(0, 4)
  return list
})

const gridClass = computed(() => {
  const count = displayedShortStays.value.length
  if (count >= 8) return 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6'
  if (count === 6) return 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6'
  if (count === 4) return 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6'
  return 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6'
})

const fetchShortStayProperties = async () => {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const res = await propertyService.getProperties({ listing_type: 'short_rent', per_page: 8 })
    if (res && res.success && res.data) {
      if (Array.isArray(res.data)) {
        shortStayProperties.value = res.data
      } else if (Array.isArray(res.data.data)) {
        shortStayProperties.value = res.data.data
      } else {
        shortStayProperties.value = []
      }
    } else if (Array.isArray(res)) {
      shortStayProperties.value = res
    } else if (res && Array.isArray(res.data)) {
      shortStayProperties.value = res.data
    } else {
      shortStayProperties.value = []
    }
  } catch (err) {
    errorMessage.value = err.message || 'Unable to connect to the server.'
    shortStayProperties.value = []
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchShortStayProperties()
})
</script>
