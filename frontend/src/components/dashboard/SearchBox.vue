<template>
  <div class="relative w-full" ref="searchContainerRef">
    <!-- Search Input (Pill Style) -->
    <div class="relative">
      <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
        <Search class="w-5 h-5 text-slate-400 dark:text-slate-500" aria-hidden="true" />
      </div>

      <input
        ref="searchInputRef"
        v-model="searchQuery"
        @input="handleInput"
        @focus="handleFocus"
        @keydown="handleKeyDown"
        type="search"
        :placeholder="placeholder || t('global_search', 'Global Search: Find properties, users, appointments...')"
        class="w-full pl-11 pr-11 py-3 bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 rounded-full shadow-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-xs sm:text-sm transition-all"
        autocomplete="off"
        role="combobox"
        aria-autocomplete="list"
        :aria-expanded="isDropdownVisible"
      />

      <div class="absolute inset-y-0 right-0 pr-4 flex items-center gap-1.5">
        <!-- Loading Spinner -->
        <Loader2 v-if="isLoading" class="w-4 h-4 text-emerald-600 animate-spin" aria-hidden="true" />
        
        <!-- Clear Button -->
        <button
          v-else-if="searchQuery"
          @click="clearSearch"
          type="button"
          class="p-1 rounded-full text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
          aria-label="Clear search"
        >
          <X class="w-3.5 h-3.5" />
        </button>
      </div>
    </div>

    <!-- Categorized Search Results Dropdown Overlay -->
    <Transition
      enter-active-class="transition duration-150 ease-out transform"
      enter-from-class="opacity-0 -translate-y-2 scale-98"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition duration-100 ease-in transform"
      leave-from-class="opacity-100 translate-y-0 scale-100"
      leave-to-class="opacity-0 -translate-y-2 scale-98"
    >
      <div
        v-if="isDropdownVisible"
        class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 z-50 max-h-[26rem] overflow-y-auto overflow-hidden divide-y divide-slate-100 dark:divide-slate-800"
        role="listbox"
      >
        <!-- 1. Properties Category -->
        <div v-if="groupedResults.properties.length" class="py-1">
          <div class="px-4 py-2 bg-slate-50/90 dark:bg-slate-800/80 flex items-center justify-between">
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
              <Building2 class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
              {{ t('properties', 'Properties') }}
            </span>
            <span class="text-[10px] font-semibold text-slate-400 bg-slate-200/60 dark:bg-slate-700 px-1.5 py-0.5 rounded-full">
              {{ groupedResults.properties.length }}
            </span>
          </div>

          <button
            v-for="prop in groupedResults.properties"
            :key="`prop-${prop.id}`"
            type="button"
            @click="navigateTo(getPropertyTargetUrl(prop))"
            @mouseenter="activeIndex = getItemGlobalIndex('property', prop.id)"
            :class="[
              'w-full text-left px-4 py-2.5 transition-colors flex items-center gap-3 cursor-pointer',
              activeIndex === getItemGlobalIndex('property', prop.id)
                ? 'bg-emerald-50/80 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-100'
                : 'hover:bg-slate-50 dark:hover:bg-slate-800/80 text-slate-800 dark:text-slate-200'
            ]"
            role="option"
          >
            <!-- Property Thumbnail -->
            <img
              v-if="prop.image"
              :src="prop.image"
              :alt="prop.title"
              class="w-11 h-11 rounded-xl object-cover shrink-0 border border-slate-200/80 dark:border-slate-700"
            />
            <div v-else class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-400 flex items-center justify-center font-bold text-base shrink-0">
              🏠
            </div>

            <!-- Property Info -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2">
                <p class="text-xs sm:text-sm font-semibold truncate">{{ prop.title }}</p>
                <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                  {{ prop.listing_type }}
                </span>
              </div>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5 truncate">
                <MapPin class="w-3 h-3 text-slate-400 shrink-0" />
                {{ prop.location || 'Addis Ababa' }}
                <span v-if="prop.bedrooms" class="text-slate-400">&bull; {{ prop.bedrooms }} beds</span>
              </p>
            </div>

            <!-- Price Badge -->
            <div class="text-right shrink-0">
              <span class="text-xs sm:text-sm font-bold text-emerald-600 dark:text-emerald-400">
                ETB {{ formatPrice(prop.price) }}
              </span>
            </div>
          </button>
        </div>

        <!-- 2. Appointments Category -->
        <div v-if="groupedResults.appointments.length" class="py-1">
          <div class="px-4 py-2 bg-slate-50/90 dark:bg-slate-800/80 flex items-center justify-between">
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
              <Calendar class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" />
              {{ t('appointments', 'Appointments') }}
            </span>
            <span class="text-[10px] font-semibold text-slate-400 bg-slate-200/60 dark:bg-slate-700 px-1.5 py-0.5 rounded-full">
              {{ groupedResults.appointments.length }}
            </span>
          </div>

          <button
            v-for="appt in groupedResults.appointments"
            :key="`appt-${appt.id}`"
            type="button"
            @click="navigateTo(appt.url || '/buyer/appointments')"
            @mouseenter="activeIndex = getItemGlobalIndex('appointment', appt.id)"
            :class="[
              'w-full text-left px-4 py-2.5 transition-colors flex items-center justify-between gap-3 cursor-pointer',
              activeIndex === getItemGlobalIndex('appointment', appt.id)
                ? 'bg-blue-50/80 dark:bg-blue-950/40 text-blue-900 dark:text-blue-100'
                : 'hover:bg-slate-50 dark:hover:bg-slate-800/80 text-slate-800 dark:text-slate-200'
            ]"
            role="option"
          >
            <div class="min-w-0">
              <p class="text-xs sm:text-sm font-semibold truncate">{{ appt.property_title }}</p>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                <Clock class="w-3 h-3 text-slate-400 shrink-0" />
                <span>{{ formatDate(appt.scheduled_at) }}</span>
                <span class="text-slate-400">&bull; Owner: {{ appt.owner_name }}</span>
              </p>
            </div>
            
            <span
              :class="[
                'text-[10px] px-2 py-0.5 rounded-full font-bold uppercase shrink-0',
                appt.status === 'confirmed' ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400' :
                appt.status === 'pending' ? 'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-400' :
                'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'
              ]"
            >
              {{ appt.status }}
            </span>
          </button>
        </div>

        <!-- 3. Messages Category -->
        <div v-if="groupedResults.messages.length" class="py-1">
          <div class="px-4 py-2 bg-slate-50/90 dark:bg-slate-800/80 flex items-center justify-between">
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
              <MessageSquare class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" />
              {{ t('direct_messages', 'Messages') }}
            </span>
            <span class="text-[10px] font-semibold text-slate-400 bg-slate-200/60 dark:bg-slate-700 px-1.5 py-0.5 rounded-full">
              {{ groupedResults.messages.length }}
            </span>
          </div>

          <button
            v-for="msg in groupedResults.messages"
            :key="`msg-${msg.id}`"
            type="button"
            @click="navigateTo(msg.url || '/buyer/messages')"
            @mouseenter="activeIndex = getItemGlobalIndex('message', msg.id)"
            :class="[
              'w-full text-left px-4 py-2.5 transition-colors flex items-center justify-between gap-3 cursor-pointer',
              activeIndex === getItemGlobalIndex('message', msg.id)
                ? 'bg-purple-50/80 dark:bg-purple-950/40 text-purple-900 dark:text-purple-100'
                : 'hover:bg-slate-50 dark:hover:bg-slate-800/80 text-slate-800 dark:text-slate-200'
            ]"
            role="option"
          >
            <div class="min-w-0">
              <div class="flex items-center gap-1.5">
                <p class="text-xs sm:text-sm font-semibold truncate">{{ msg.other_user_name }}</p>
                <span class="text-[10px] text-slate-400 truncate">({{ msg.property_title }})</span>
              </div>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5">{{ msg.latest_message }}</p>
            </div>
            <span v-if="msg.unread_count > 0" class="w-5 h-5 rounded-full bg-purple-600 text-white text-[10px] font-bold flex items-center justify-center shrink-0">
              {{ msg.unread_count }}
            </span>
          </button>
        </div>

        <!-- 4. Empty State When No Results Found -->
        <div v-if="!isLoading && searchQuery && totalResultsCount === 0" class="px-6 py-8 text-center">
          <p class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
            No matching results found for "{{ searchQuery }}"
          </p>
          <p class="text-xs text-slate-400 mt-1">
            Try searching for another property title, sub-city, appointment, or message.
          </p>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { Search, Building2, Calendar, MessageSquare, MapPin, Clock, Loader2, X } from 'lucide-vue-next'
import { buyerService } from '@/services/buyerService'
import { useLanguage } from '@/composables/useLanguage'
import { useAuthStore } from '@/stores/auth'

defineProps({
  placeholder: {
    type: String,
    default: ''
  }
})

const router = useRouter()
const { t } = useLanguage()
const authStore = useAuthStore()

function getPropertyTargetUrl(prop) {
  if (!prop) return '/properties'
  if (prop.url) return prop.url
  const role = (authStore.user?.role || authStore.user?.roles?.[0]?.name || '').toLowerCase()
  if (role === 'owner') return `/owner/properties/${prop.id}`
  if (role === 'agent') return `/agent/properties/${prop.id}`
  if (role === 'buyer') return `/buyer/properties/${prop.id}`
  return `/properties/${prop.id}`
}

const searchQuery = ref('')
const isLoading = ref(false)
const isFocused = ref(false)
const activeIndex = ref(-1)
const searchContainerRef = ref(null)
const searchInputRef = ref(null)

const groupedResults = ref({
  properties: [],
  appointments: [],
  messages: []
})

let debounceTimer = null

const totalResultsCount = computed(() => {
  return (
    groupedResults.value.properties.length +
    groupedResults.value.appointments.length +
    groupedResults.value.messages.length
  )
})

const isDropdownVisible = computed(() => {
  return isFocused.value && searchQuery.value.trim().length > 0
})

// Flatten results array to enable keyboard ArrowUp/Down navigation
const flatResultsList = computed(() => {
  const list = []
  groupedResults.value.properties.forEach(p => list.push({ ...p, _category: 'property' }))
  groupedResults.value.appointments.forEach(a => list.push({ ...a, _category: 'appointment' }))
  groupedResults.value.messages.forEach(m => list.push({ ...m, _category: 'message' }))
  return list
})

function getItemGlobalIndex(category, id) {
  return flatResultsList.value.findIndex(item => item._category === category && item.id === id)
}

function handleInput() {
  if (debounceTimer) clearTimeout(debounceTimer)

  const query = searchQuery.value.trim()
  if (!query) {
    groupedResults.value = { properties: [], appointments: [], messages: [] }
    isLoading.value = false
    activeIndex.value = -1
    return
  }

  isLoading.value = true
  debounceTimer = setTimeout(async () => {
    try {
      const res = await buyerService.search(query)
      const data = res?.data || {}
      groupedResults.value = {
        properties: data.properties || [],
        appointments: data.appointments || [],
        messages: data.messages || []
      }
      activeIndex.value = -1
    } catch (err) {
      console.error('Search failed:', err)
      groupedResults.value = { properties: [], appointments: [], messages: [] }
    } finally {
      isLoading.value = false
    }
  }, 300)
}

function handleFocus() {
  isFocused.value = true
  if (searchQuery.value.trim() && totalResultsCount.value === 0 && !isLoading.value) {
    handleInput()
  }
}

function handleKeyDown(e) {
  if (!isDropdownVisible.value) return

  const total = flatResultsList.value.length

  if (e.key === 'ArrowDown') {
    e.preventDefault()
    if (total > 0) {
      activeIndex.value = (activeIndex.value + 1) % total
    }
  } else if (e.key === 'ArrowUp') {
    e.preventDefault()
    if (total > 0) {
      activeIndex.value = (activeIndex.value - 1 + total) % total
    }
  } else if (e.key === 'Enter') {
    e.preventDefault()
    if (activeIndex.value >= 0 && activeIndex.value < total) {
      const selected = flatResultsList.value[activeIndex.value]
      navigateTo(getPropertyTargetUrl(selected))
    } else if (groupedResults.value.properties.length > 0) {
      const role = (authStore.user?.role || authStore.user?.roles?.[0]?.name || '').toLowerCase()
      const basePath = role === 'owner' ? '/owner/properties' : (role === 'agent' ? '/agent/properties' : (role === 'buyer' ? '/buyer/properties' : '/properties'))
      router.push(`${basePath}?search=${encodeURIComponent(searchQuery.value.trim())}`)
      isFocused.value = false
    }
  } else if (e.key === 'Escape') {
    isFocused.value = false
    if (searchInputRef.value) {
      searchInputRef.value.blur()
    }
  }
}

function navigateTo(url) {
  isFocused.value = false
  if (url) {
    router.push(url)
  }
}

function clearSearch() {
  searchQuery.value = ''
  groupedResults.value = { properties: [], appointments: [], messages: [] }
  activeIndex.value = -1
}

function handleClickOutside(event) {
  if (searchContainerRef.value && !searchContainerRef.value.contains(event.target)) {
    isFocused.value = false
  }
}

function formatPrice(price) {
  if (!price && price !== 0) return '0'
  return new Intl.NumberFormat('en-US').format(price)
}

function formatDate(dateString) {
  if (!dateString) return 'Upcoming'
  try {
    const d = new Date(dateString)
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
  } catch {
    return dateString
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  if (debounceTimer) clearTimeout(debounceTimer)
})
</script>
