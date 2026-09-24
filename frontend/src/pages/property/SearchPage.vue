<template>
  <div class="min-h-screen bg-slate-950 text-slate-100 py-8 lg:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
      <!-- Page Header -->
      <div class="text-center max-w-2xl mx-auto">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-100 tracking-tight mb-2">
          Search Properties in Ethiopia
        </h1>
        <p class="text-xs sm:text-sm text-slate-400">
          Find your dream home, apartment, office, or investment property across Addis Ababa and beyond.
        </p>
      </div>

      <!-- Search Box Section -->
      <div class="py-2">
        <PropertySearch :standalone="true" />
      </div>

      <!-- Results Header & Sort -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-slate-800">
        <p class="text-xs sm:text-sm text-slate-400">
          <span class="font-bold text-slate-100">{{ pagination.total }}</span> properties available
        </p>

        <div class="flex items-center gap-2">
          <label for="search-sort" class="text-xs text-slate-400">Sort by:</label>
          <select
            id="search-sort"
            v-model="sort"
            class="bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 focus:outline-none focus:border-emerald-500 transition-colors"
            @change="fetchResults(1)"
          >
            <option value="latest">Newest First</option>
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
            <option value="popular">Most Popular</option>
          </select>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
        <div
          v-for="i in 6"
          :key="i"
          class="bg-slate-900 border border-slate-800 rounded-2xl h-80 animate-pulse flex flex-col justify-between p-5"
        >
          <div class="w-full h-44 bg-slate-800 rounded-xl" />
          <div class="space-y-2 mt-4">
            <div class="w-3/4 h-4 bg-slate-800 rounded" />
            <div class="w-1/2 h-3 bg-slate-800 rounded" />
          </div>
        </div>
      </div>

      <!-- Error State with Retry -->
      <div v-else-if="errorMessage" class="text-center py-16 px-4 bg-slate-900/60 border border-slate-800 rounded-2xl">
        <p class="text-xs sm:text-sm text-rose-400 mb-4">{{ errorMessage }}</p>
        <button
          type="button"
          class="px-4 py-2 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors"
          @click="fetchResults(1)"
        >
          Try Again
        </button>
      </div>

      <!-- Empty State -->
      <div v-else-if="properties.length === 0" class="text-center py-16 px-4 bg-slate-900/40 border border-slate-800 rounded-2xl">
        <p class="text-slate-400 text-sm">No properties found matching your search criteria.</p>
        <RouterLink
          to="/properties"
          class="mt-3 inline-flex items-center text-xs font-semibold text-emerald-400 hover:text-emerald-300"
        >
          View all properties &rarr;
        </RouterLink>
      </div>

      <!-- Results Grid -->
      <div v-else class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
          <PropertyCard
            v-for="property in properties"
            :key="property.id"
            :property="property"
          />
        </div>

        <div class="pt-4 border-t border-slate-800">
          <BasePagination
            :current-page="pagination.current_page"
            :total-pages="pagination.last_page"
            :total-items="pagination.total"
            :per-page="pagination.per_page"
            @change="fetchResults"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import PropertySearch from '../../components/landing/PropertySearch.vue'
import PropertyCard from '../../components/property/PropertyCard.vue'
import BasePagination from '../../components/common/BasePagination.vue'
import { searchService } from '../../services/searchService'

const route = useRoute()
const properties = ref([])
const isLoading = ref(true)
const errorMessage = ref('')
const sort = ref('latest')

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  per_page: 15,
})

const fetchResults = async (page = 1) => {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const params = {
      ...route.query,
      page,
      per_page: pagination.per_page,
      sort: sort.value,
    }
    const res = await searchService.search(params)
    if (res && res.success && res.data) {
      if (Array.isArray(res.data)) {
        properties.value = res.data
        pagination.total = res.meta?.total || res.data.length
        pagination.current_page = res.meta?.current_page || 1
        pagination.last_page = res.meta?.last_page || 1
      } else if (res.data.data && Array.isArray(res.data.data)) {
        properties.value = res.data.data
        pagination.total = res.data.total || res.data.data.length
        pagination.current_page = res.data.current_page || 1
        pagination.last_page = res.data.last_page || 1
      }
    } else if (res && Array.isArray(res.data)) {
      properties.value = res.data
    } else {
      properties.value = []
    }
  } catch (err) {
    errorMessage.value = err.message || 'Unable to load search results.'
    properties.value = []
  } finally {
    isLoading.value = false
  }
}

watch(() => route.query, () => {
  fetchResults(1)
})

onMounted(() => {
  fetchResults(1)
})
</script>
