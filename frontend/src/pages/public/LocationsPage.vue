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
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ t('common.back', 'Back') }}</span>
      </div>

      <!-- Header -->
      <div class="text-center max-w-2xl mx-auto space-y-3">
        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ t('locations.tag', 'Prime Destinations') }}</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ t('locations.page_title', 'Explore Ethiopian Real Estate by City') }}</h1>
        <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base">{{ t('locations.page_subtitle', 'Browse verified apartments, commercial buildings, and land plots in your preferred municipality.') }}</p>
      </div>

      <!-- Locations Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <article
          v-for="(city, idx) in cities"
          :key="city.id"
          class="relative h-72 rounded-2xl overflow-hidden group cursor-pointer border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-2xl transition-all duration-300 flex flex-col justify-end p-5"
          @click="navigateToCity(city)"
        >
          <img
            :src="getCityImage(city, idx)"
            :alt="formatCityName(city.name, city.slug)"
            class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
            loading="lazy"
            decoding="async"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent"></div>

          <div class="relative z-10 space-y-1">
            <h2 class="text-lg font-bold text-white group-hover:text-slate-200 transition-colors">{{ formatCityName(city.name, city.slug) }}</h2>
            <p class="text-xs text-slate-300 flex items-center justify-between">
              <span>{{ formatListingCount(city.properties_count) }}</span>
              <span class="text-slate-200 font-semibold opacity-0 group-hover:opacity-100 transition-opacity">{{ t('categories.explore', 'Explore') }} &rarr;</span>
            </p>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { locationService } from '../../services/locationService'
import { useLanguage } from '../../composables/useLanguage'

const router = useRouter()
const { t } = useLanguage()
const cities = ref([])
const isLoading = ref(true)

const handleGoBack = () => {
  if (window.history.state && window.history.state.back) {
    router.back()
  } else {
    router.push('/')
  }
}

const cityImageMap = {
  'addis-ababa': 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&auto=format&fit=crop&q=80',
  'dire-dawa': 'https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?w=600&auto=format&fit=crop&q=80',
  'tigray': 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=600&auto=format&fit=crop&q=80',
  'amhara': 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?w=600&auto=format&fit=crop&q=80',
  'oromia': 'https://images.unsplash.com/photo-1613977257363-707ba9348227?w=600&auto=format&fit=crop&q=80',
  'afar': 'https://images.unsplash.com/photo-1480714378408-67cf0d13bc1b?w=600&auto=format&fit=crop&q=80',
  'sidama': 'https://images.unsplash.com/photo-1449844908441-8829872d2607?w=600&auto=format&fit=crop&q=80',
  'benishangul-gumuz': 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=600&auto=format&fit=crop&q=80',
}

const fallbackImages = [
  'https://images.unsplash.com/photo-1480714378408-67cf0d13bc1b?w=600&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1449844908441-8829872d2607?w=600&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?w=600&auto=format&fit=crop&q=80',
]

const getCityImage = (city, idx) => {
  if (city.image_url || city.image) return city.image_url || city.image
  const slug = (city.slug || city.name || '').toLowerCase().replace(/[^a-z0-9]/g, '-')
  if (cityImageMap[slug]) return cityImageMap[slug]
  return fallbackImages[idx % fallbackImages.length]
}

const fetchCities = async () => {
  isLoading.value = true
  try {
    const res = await locationService.getCities()
    const data = res?.data?.data || res?.data || res || []
    if (Array.isArray(data)) {
      cities.value = [...data].sort((a, b) => (b.properties_count || 0) - (a.properties_count || 0))
    }
  } catch (err) {
    cities.value = []
  } finally {
    isLoading.value = false
  }
}

const formatCityName = (name, slug) => {
  if (!name && !slug) return ''
  const key = (slug || name).toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '')
  const fromCities = t(`cities.${key}`, '')
  if (fromCities && !fromCities.startsWith('cities.')) return fromCities
  
  const fromLocations = t(`locations.${key}`, '')
  if (fromLocations && !fromLocations.startsWith('locations.')) return fromLocations
  
  return name || ''
}

const formatListingCount = (count) => {
  const num = count || 0
  return t('locations.homes_count', '{count} homes', { count: num })
}

const navigateToCity = (city) => {
  router.push({ path: '/properties', query: { city_id: city.id } })
}

onMounted(() => {
  fetchCities()
})
</script>
