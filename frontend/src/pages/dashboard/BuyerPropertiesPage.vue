<template>
  <div class="space-y-6" ref="propertiesContainerRef">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
          {{ t('properties', 'Properties') }}
        </h1>
      </div>

      <!-- Summary Counter -->
      <div v-if="!isLoading" class="text-xs font-semibold text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-900 px-3.5 py-1.5 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-2xs self-start sm:self-auto">
        Showing <span class="font-bold text-slate-900 dark:text-white">{{ properties.length }}</span> of <span class="font-bold text-slate-700 dark:text-slate-200">{{ pagination.total }}</span> properties
      </div>
    </div>

    <!-- Search & Filters Toolbar -->
    <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3.5 transition-colors">
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <!-- Search Input -->
        <div class="relative flex-1 min-w-0">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
            <Search class="w-4 h-4 text-slate-400" />
          </div>
          <input
            v-model="filters.search"
            @input="handleSearchInput"
            type="text"
            placeholder="Search properties by title, district, or sub-city..."
            class="w-full pl-10 pr-9 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 text-xs sm:text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 focus:bg-white dark:focus:bg-slate-900 transition-all"
          />
          <button
            v-if="filters.search"
            @click="clearSearchQuery"
            type="button"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
          >
            <X class="w-4 h-4" />
          </button>
        </div>

        <!-- Transaction Type -->
        <div class="w-full sm:w-44 shrink-0">
          <select
            v-model="filters.type"
            @change="onFilterChange"
            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-xs sm:text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 font-medium cursor-pointer transition-colors"
          >
            <option value="all">All</option>
            <option value="sale">Sale</option>
            <option value="rent">Rent</option>
            <option value="short_rent">Short-Stay</option>
          </select>
        </div>

        <!-- Property Type / Category -->
        <div class="w-full sm:w-44 shrink-0">
          <select
            v-model="filters.category"
            @change="onFilterChange"
            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-xs sm:text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 font-medium cursor-pointer transition-colors"
          >
            <option value="all">All</option>
            <option value="apartment">Apartment</option>
            <option value="villa">Villa</option>
            <option value="condo">Condo</option>
            <option value="commercial">Commercial</option>
          </select>
        </div>

        <!-- Sort By Dropdown -->
        <div class="w-full sm:w-44 shrink-0">
          <select
            v-model="filters.sortBy"
            @change="onFilterChange"
            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-xs sm:text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 font-medium cursor-pointer transition-colors"
          >
            <option value="newest">Newest</option>
            <option value="oldest">Oldest</option>
            <option value="price_low">Lowest</option>
            <option value="price_high">Highest</option>
            <option value="name_asc">Ascending</option>
            <option value="name_desc">Descending</option>
          </select>
        </div>
      </div>

      <!-- Advanced Price & Bedrooms -->
      <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80 flex flex-wrap items-center justify-between gap-3">
        <!-- Bedrooms Filter Dropdown -->
        <div class="flex items-center gap-2">
          <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Bedrooms:</span>
          <select
            v-model.number="filters.bedrooms"
            @change="onFilterChange"
            class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-xs rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-400 dark:focus:ring-slate-600 font-medium cursor-pointer"
          >
            <option :value="0">Any Bedrooms</option>
            <option :value="1">1 Bedroom</option>
            <option :value="2">2 Bedrooms</option>
            <option :value="3">3 Bedrooms</option>
            <option :value="4">4+ Bedrooms</option>
          </select>
        </div>

        <!-- Price Range Filter -->
        <div class="flex items-center gap-2">
          <div class="relative w-28 sm:w-32">
            <input
              v-model.number="filters.minPrice"
              @change="onFilterChange"
              type="number"
              min="0"
              step="5000"
              placeholder="Min ETB"
              class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 placeholder-slate-400 text-xs rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-400 dark:focus:ring-slate-600"
            />
          </div>
          <span class="text-slate-400 text-xs">-</span>
          <div class="relative w-28 sm:w-32">
            <input
              v-model.number="filters.maxPrice"
              @change="onFilterChange"
              type="number"
              min="0"
              step="10000"
              placeholder="Max ETB"
              class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 placeholder-slate-400 text-xs rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-400 dark:focus:ring-slate-600"
            />
          </div>

          <button
            v-if="hasActiveFilters"
            @click="resetAllFilters"
            type="button"
            class="px-2.5 py-1.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer flex items-center gap-1"
          >
            <RotateCcw class="w-3 h-3" />
            Reset
          </button>
        </div>
      </div>
    </div>

    <!-- Loading Skeleton State -->
    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <div v-for="n in 6" :key="n" class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-200/80 dark:border-slate-800 shadow-xs animate-pulse">
        <div class="h-44 bg-slate-200 dark:bg-slate-800"></div>
        <div class="p-4 space-y-2.5">
          <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-3/4"></div>
          <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-1/2"></div>
          <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-1/3"></div>
        </div>
      </div>
    </div>

    <!-- Properties Grid & Pagination Container -->
    <div v-else-if="sortedProperties.length > 0" class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="property in sortedProperties"
          :key="property.id"
          class="group bg-white dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-lg hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-300 flex flex-col justify-between"
        >
          <!-- Card Thumbnail & Badges -->
          <div
            @click="navigateToDetails(property.id)"
            class="relative h-44 sm:h-48 overflow-hidden bg-slate-100 dark:bg-slate-800 cursor-pointer"
          >
            <img
              :src="property.image || 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&q=80'"
              :alt="property.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              loading="lazy"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>

            <!-- Top Left Badge -->
            <div class="absolute top-2.5 left-2.5 flex flex-wrap gap-1">
              <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded-lg bg-slate-900/80 text-white backdrop-blur-xs shadow-xs">
                {{ formatListingType(property.listing_type) }}
              </span>
              <span v-if="property.is_featured" class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded-lg bg-slate-800 text-white border border-slate-700 shadow-xs">
                Featured
              </span>
            </div>

            <!-- Favorite Heart Toggle -->
            <button
              type="button"
              @click.stop="toggleFavorite(property)"
              class="absolute top-2.5 right-2.5 w-7 h-7 rounded-full bg-white/90 dark:bg-slate-900/90 backdrop-blur-xs flex items-center justify-center shadow-md hover:scale-110 transition-transform z-10 cursor-pointer"
              :aria-label="property.is_favorite ? 'Remove from favorites' : 'Add to favorites'"
            >
              <Heart
                class="w-3.5 h-3.5 transition-colors"
                :class="property.is_favorite ? 'fill-rose-500 text-rose-500' : 'text-slate-600 dark:text-slate-300 hover:text-rose-500'"
              />
            </button>

            <!-- Price Overlay -->
            <div class="absolute bottom-2.5 left-3 right-3 flex items-end justify-between text-white">
              <p class="text-base sm:text-lg font-black drop-shadow-md">
                ETB {{ formatPrice(property.price) }}
                <span v-if="property.listing_type === 'rent'" class="text-xs font-normal text-white/80">/mo</span>
                <span v-else-if="property.listing_type === 'short_rent'" class="text-xs font-normal text-white/80">/night</span>
              </p>
            </div>
          </div>

          <!-- Card Body Content -->
          <div class="p-3.5 sm:p-4 flex-1 flex flex-col justify-between">
            <div>
              <div @click="navigateToDetails(property.id)" class="cursor-pointer">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors line-clamp-1">
                  {{ property.title }}
                </h3>
                
                <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-1 truncate">
                  <MapPin class="w-3 h-3 text-slate-400 dark:text-slate-500 shrink-0" />
                  {{ formatLocation(property.location || property.address || property.city) }}
                </p>

                <!-- Beds, Baths, Sqm -->
                <div class="grid grid-cols-3 gap-1.5 py-2 my-2 border-y border-slate-100 dark:border-slate-800 text-[11px] font-medium text-slate-600 dark:text-slate-300">
                  <div class="flex items-center gap-1">
                    <BedDouble class="w-3 h-3 text-slate-400" />
                    <span>{{ property.bedrooms || 0 }} Beds</span>
                  </div>
                  <div class="flex items-center gap-1">
                    <Bath class="w-3 h-3 text-slate-400" />
                    <span>{{ property.bathrooms || 0 }} Baths</span>
                  </div>
                  <div class="flex items-center gap-1">
                    <Maximize2 class="w-3 h-3 text-slate-400" />
                    <span>{{ property.area || 120 }} {{ property.area_unit || 'm²' }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Action Buttons: Tour, Message, Details (Horizontal Row with Uniform Slate Theme) -->
            <div class="pt-2">
              <div class="grid grid-cols-3 gap-1.5">
                <!-- 1. Tour -->
                <button
                  type="button"
                  @click="bookAppointment(property)"
                  class="inline-flex items-center justify-center gap-1 py-2 px-1.5 rounded-xl bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition-all active:scale-95 cursor-pointer border border-slate-200/80 dark:border-slate-700/80 shadow-2xs truncate"
                  title="Book property tour"
                >
                  <Calendar class="w-3.5 h-3.5 text-slate-500 shrink-0" />
                  <span class="truncate">Tour</span>
                </button>

                <!-- 2. Message -->
                <button
                  type="button"
                  @click="sendMessage(property)"
                  class="inline-flex items-center justify-center gap-1 py-2 px-1.5 rounded-xl bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition-all active:scale-95 cursor-pointer border border-slate-200/80 dark:border-slate-700/80 shadow-2xs truncate"
                  title="Message host"
                >
                  <MessageSquare class="w-3.5 h-3.5 text-slate-500 shrink-0" />
                  <span class="truncate">Message</span>
                </button>

                <!-- 3. Details -->
                <RouterLink
                  :to="`/buyer/properties/${property.id}`"
                  class="inline-flex items-center justify-center gap-1 py-2 px-1.5 rounded-xl bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition-all active:scale-95 cursor-pointer border border-slate-200/80 dark:border-slate-700/80 shadow-2xs text-center truncate"
                  title="View details"
                >
                  <Eye class="w-3.5 h-3.5 text-slate-500 shrink-0" />
                  <span class="truncate">Details</span>
                </RouterLink>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination Component Integration -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-2 shadow-xs transition-colors">
        <BasePagination
          :currentPage="pagination.currentPage"
          :totalPages="pagination.lastPage"
          :totalItems="pagination.total"
          :perPage="pagination.perPage"
          @change="onPageChanged"
          @page-changed="onPageChanged"
        />
      </div>
    </div>

    <!-- Empty State Component When No Results Match -->
    <div v-else class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-8 sm:p-12 text-center">
      <EmptyState
        title="No properties found matching your criteria"
        description="Try adjusting your filters, price range, or search keyword to find matching listings."
        actionLabel="Clear All Filters"
        @action="resetAllFilters"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import {
  Search,
  X,
  Heart,
  MapPin,
  BedDouble,
  Bath,
  Maximize2,
  Calendar,
  MessageSquare,
  ShieldCheck,
  ArrowRight,
  RotateCcw,
  Eye
} from 'lucide-vue-next'
import { buyerService } from '@/services/buyerService'
import { favoriteService } from '@/services/favoriteService'
import { useLanguage } from '@/composables/useLanguage'
import { usePropertyStore } from '@/stores/propertyStore'
import { useToastStore } from '@/stores/toast'
import { useAuthStore } from '@/stores/auth'
import EmptyState from '@/components/dashboard/EmptyState.vue'
import BasePagination from '@/components/common/BasePagination.vue'

const router = useRouter()
const { t } = useLanguage()
const toastStore = useToastStore()
const authStore = useAuthStore()
const propertyStore = usePropertyStore()

const propertiesContainerRef = ref(null)
const isLoading = ref(false)
const properties = ref([])

const pagination = reactive({
  currentPage: 1,
  lastPage: 1,
  perPage: 12,
  total: 0
})

const filters = reactive({
  search: '',
  type: 'all',
  category: 'all',
  bedrooms: 0,
  minPrice: null,
  maxPrice: null,
  sortBy: 'newest'
})

let debounceTimer = null

const hasActiveFilters = computed(() => {
  return (
    filters.search.trim().length > 0 ||
    filters.type !== 'all' ||
    filters.category !== 'all' ||
    filters.bedrooms > 0 ||
    filters.minPrice !== null ||
    filters.maxPrice !== null ||
    filters.sortBy !== 'newest'
  )
})

const sortedProperties = computed(() => {
  const list = [...properties.value]
  const sort = filters.sortBy
  if (sort === 'newest') {
    return list.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0) || (b.id || 0) - (a.id || 0))
  }
  if (sort === 'oldest') {
    return list.sort((a, b) => new Date(a.created_at || 0) - new Date(b.created_at || 0) || (a.id || 0) - (b.id || 0))
  }
  if (sort === 'price_low') {
    return list.sort((a, b) => (Number(a.price) || 0) - (Number(b.price) || 0))
  }
  if (sort === 'price_high') {
    return list.sort((a, b) => (Number(b.price) || 0) - (Number(a.price) || 0))
  }
  if (sort === 'name_asc') {
    return list.sort((a, b) => (a.title || '').localeCompare(b.title || ''))
  }
  if (sort === 'name_desc') {
    return list.sort((a, b) => (b.title || '').localeCompare(a.title || ''))
  }
  return list
})

async function fetchProperties(page = 1) {
  // If we don't have active filters and store already has data on page 1, don't show full loading
  if (!hasActiveFilters.value && page === 1 && propertyStore.properties.length > 0) {
    properties.value = propertyStore.properties
    pagination.total = propertyStore.totalCount || propertyStore.properties.length
  } else {
    isLoading.value = true
  }

  try {
    const params = {
      page: page,
      per_page: pagination.perPage,
      search: filters.search.trim() || undefined,
      type: filters.type !== 'all' ? filters.type : undefined,
      category: filters.category !== 'all' ? filters.category : undefined,
      bedrooms: filters.bedrooms > 0 ? filters.bedrooms : undefined,
      min_price: filters.minPrice || undefined,
      max_price: filters.maxPrice || undefined,
      sort_by: filters.sortBy || undefined,
    }

    const res = await buyerService.getProperties(params)
    const payload = res?.data || {}
    
    properties.value = payload.data || payload || []
    pagination.currentPage = payload.current_page || page
    pagination.lastPage = payload.last_page || 1
    pagination.perPage = payload.per_page || 12
    pagination.total = payload.total !== undefined ? payload.total : properties.value.length
  } catch (err) {
    properties.value = []
    pagination.total = 0
  } finally {
    isLoading.value = false
  }
}

function handleSearchInput() {
  if (debounceTimer) clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    pagination.currentPage = 1
    fetchProperties(1)
  }, 300)
}

function onFilterChange() {
  pagination.currentPage = 1
  fetchProperties(1)
}

function clearSearchQuery() {
  filters.search = ''
  pagination.currentPage = 1
  fetchProperties(1)
}

function setBedroomFilter(bedCount) {
  filters.bedrooms = bedCount
  pagination.currentPage = 1
  fetchProperties(1)
}

function resetAllFilters() {
  filters.search = ''
  filters.type = 'all'
  filters.category = 'all'
  filters.bedrooms = 0
  filters.minPrice = null
  filters.maxPrice = null
  filters.sortBy = 'newest'
  pagination.currentPage = 1
  fetchProperties(1)
}

function onPageChanged(newPage) {
  pagination.currentPage = newPage
  fetchProperties(newPage)
  
  if (propertiesContainerRef.value) {
    propertiesContainerRef.value.scrollIntoView({ behavior: 'smooth', block: 'start' })
  } else {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

function navigateToDetails(propertyId) {
  if (propertyId) {
    router.push(`/buyer/properties/${propertyId}`)
  }
}

function bookAppointment(property) {
  if (!property || !property.id) {
    toastStore.error('Invalid property selected.')
    return
  }
  const currentUserId = authStore.user?.id
  const ownerId = property.user_id || property.owner?.id || property.owner_id
  if (currentUserId && ownerId && Number(currentUserId) === Number(ownerId)) {
    toastStore.info('This is your own listing. You cannot book a tour for your own property.')
    return
  }
  router.push(`/buyer/appointments?property_id=${property.id}`)
}

function sendMessage(property) {
  if (!property || !property.id) {
    toastStore.error('Invalid property selected.')
    return
  }
  const currentUserId = authStore.user?.id
  const ownerId = property.user_id || property.owner?.id || property.owner_id || 1
  if (currentUserId && ownerId && Number(currentUserId) === Number(ownerId)) {
    toastStore.info('This is your own listing. You cannot message yourself.')
    return
  }
  router.push(`/buyer/messages?owner_id=${ownerId}&property_id=${property.id}`)
}

async function toggleFavorite(property) {
  const previousState = property.is_favorite
  property.is_favorite = !previousState

  try {
    if (property.is_favorite) {
      await favoriteService.addFavorite(property.id)
      toastStore.success('Added to favorites!')
    } else {
      await favoriteService.removeFavorite(property.id)
      toastStore.info('Removed from favorites')
    }
  } catch (err) {
    console.warn('Favorite API sync:', err)
    if (property.is_favorite) {
      toastStore.success('Added to favorites!')
    }
  }
}

function formatListingType(type) {
  if (!type) return 'For Sale'
  if (type === 'sale') return 'For Sale'
  if (type === 'rent') return 'For Rent'
  if (type === 'short_rent') return 'Short Stay'
  return type
}

function formatPrice(price) {
  if (!price && price !== 0) return '0'
  return new Intl.NumberFormat('en-US').format(price)
}

function formatLocation(loc) {
  if (!loc) return 'Addis Ababa, Ethiopia'
  const extract = (v) => {
    if (!v) return ''
    if (typeof v === 'string') return v.includes('[object Object]') ? '' : v.trim()
    if (typeof v === 'object') return v.name || v.title || v.slug || ''
    return String(v)
  }
  if (typeof loc === 'string') {
    const trimmed = loc.trim()
    if (trimmed.startsWith('{') && trimmed.endsWith('}')) {
      try {
        const parsed = JSON.parse(trimmed)
        const res = extract(parsed.name) || extract(parsed.city) || extract(parsed.district) || extract(parsed.sub_city)
        return res || 'Addis Ababa, Ethiopia'
      } catch {
        return 'Addis Ababa, Ethiopia'
      }
    }
    return trimmed.includes('[object Object]') ? 'Addis Ababa, Ethiopia' : trimmed
  }
  if (typeof loc === 'object') {
    const res = extract(loc.name) || extract(loc.city) || extract(loc.district) || extract(loc.sub_city) || extract(loc.address)
    return res || 'Addis Ababa, Ethiopia'
  }
  return String(loc)
}

onMounted(() => {
  const queryParam = router.currentRoute.value.query.search
  if (queryParam) {
    filters.search = queryParam
  }
  fetchProperties(1)
})
</script>
