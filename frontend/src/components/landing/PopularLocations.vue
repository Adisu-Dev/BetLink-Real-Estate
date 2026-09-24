<template>
  <section class="py-12 md:py-16 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200" aria-label="Popular locations">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
        <div class="max-w-2xl">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900/5 dark:bg-white/10 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 text-xs font-bold uppercase tracking-wider mb-3">
            <span class="w-2 h-2 rounded-full bg-slate-900 dark:bg-white animate-pulse"></span>
            <span>{{ t('locations.tag', 'Prime Destinations') }}</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
            {{ t('home.popular_locations_title', 'Popular Locations in Ethiopia') }}
          </h2>
          <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 mt-2">
            {{ t('home.popular_locations_subtitle', 'Explore properties across high-demand urban and regional hubs.') }}
          </p>
        </div>

        <RouterLink
          to="/locations"
          class="inline-flex items-center text-sm font-bold text-slate-900 dark:text-white hover:text-slate-700 dark:hover:text-slate-300 transition-colors group flex-shrink-0"
        >
          <span>{{ t('locations.view_all', 'All Locations') }}</span>
          <svg class="w-4 h-4 ml-1.5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </RouterLink>
      </div>

      <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div
          v-for="i in 4"
          :key="i"
          class="bg-slate-100 dark:bg-slate-800 rounded-2xl h-72 animate-pulse"
        />
      </div>

      <div v-else-if="errorMessage" class="text-center py-10 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm">
        <p class="text-slate-500 text-xs mb-3">{{ errorMessage }}</p>
        <button
          type="button"
          class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900"
          @click="fetchLocations"
        >
          {{ t('common.reset', 'Retry') }}
        </button>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <article
          v-for="(city, idx) in displayCities"
          :key="city.id || city.name"
          class="relative h-72 rounded-2xl overflow-hidden group cursor-pointer border border-slate-200/80 dark:border-slate-700 shadow-sm hover:shadow-2xl transition-all duration-300 flex flex-col justify-end p-5"
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
            <h3 class="text-lg font-bold text-white group-hover:text-slate-200 transition-colors">
              {{ formatCityName(city.name, city.slug) }}
            </h3>
            <p class="text-xs text-slate-300 flex items-center justify-between">
              <span>{{ formatListingCount(city.properties_count) }}</span>
              <span class="text-white font-semibold opacity-0 group-hover:opacity-100 transition-opacity">{{ t('categories.explore', 'Explore') }} &rarr;</span>
            </p>
          </div>
        </article>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { locationService } from '../../services/locationService'
import { useLanguage } from '../../composables/useLanguage'

const router = useRouter()
const { t } = useLanguage()
const cities = ref([])
const isLoading = ref(true)
const errorMessage = ref('')

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

const fallbackCityImages = [
  'https://images.unsplash.com/photo-1480714378408-67cf0d13bc1b?w=600&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1449844908441-8829872d2607?w=600&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?w=600&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=600&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1600585154526-990dced4db0d?w=600&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1613977257363-707ba9348227?w=600&auto=format&fit=crop&q=80',
]

const displayCities = computed(() => {
  if (!cities.value || cities.value.length === 0) return []
  return [...cities.value]
    .sort((a, b) => (b.properties_count || 0) - (a.properties_count || 0))
    .slice(0, 8)
})

const getCityImage = (city, idx) => {
  if (city.image_url || city.image) return city.image_url || city.image
  const slug = (city.slug || city.name || '').toLowerCase().replace(/[^a-z0-9]/g, '-')
  if (cityImageMap[slug]) return cityImageMap[slug]
  return fallbackCityImages[idx % fallbackCityImages.length]
}

const fetchLocations = async () => {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const res = await locationService.getCities()
    if (res && res.success && Array.isArray(res.data)) {
      cities.value = res.data
    } else if (Array.isArray(res)) {
      cities.value = res
    } else if (res && Array.isArray(res.data?.data)) {
      cities.value = res.data.data
    }
  } catch (err) {
    console.error('Failed to fetch live locations:', err)
    errorMessage.value = 'Failed to load locations from database.'
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
  fetchLocations()
})
</script>
