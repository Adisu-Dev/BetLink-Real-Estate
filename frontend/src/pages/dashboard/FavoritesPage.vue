<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
          {{ t('saved_favorites', 'Saved Favorites') }}
        </h1>
      </div>

      <!-- Quick Counter -->
      <div v-if="!isLoading" class="text-xs font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 px-3.5 py-1.5 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-2xs self-start sm:self-auto">
        <span class="font-extrabold text-slate-900 dark:text-white">{{ favoritesList.length }}</span> saved {{ favoritesList.length === 1 ? 'property' : 'properties' }}
      </div>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="n in 3" :key="n" class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-200/80 dark:border-slate-800 shadow-xs animate-pulse">
        <div class="h-48 bg-slate-200 dark:bg-slate-800"></div>
        <div class="p-5 space-y-3">
          <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-3/4"></div>
          <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-1/2"></div>
        </div>
      </div>
    </div>

    <!-- Favorites Grid -->
    <div v-else-if="favoritesList.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="property in favoritesList" 
        :key="property.id" 
        class="group bg-white dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-xl hover:border-emerald-500/40 transition-all duration-300 flex flex-col justify-between"
      >
        <!-- Card Top Thumbnail -->
        <div class="relative h-48 sm:h-52 overflow-hidden bg-slate-100 dark:bg-slate-800 cursor-pointer" @click="navigateToDetails(property.id)">
          <img 
            :src="property.image || 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=600&q=80'" 
            :alt="property.title" 
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
            loading="lazy"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>

          <!-- Top-Left Listing Badge -->
          <div class="absolute top-3 left-3 flex gap-1.5">
            <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-lg bg-slate-900/80 text-white backdrop-blur-xs shadow-xs">
              {{ property.listing_type === 'rent' ? 'For Rent' : property.listing_type === 'short_rent' ? 'Short Stay' : 'For Sale' }}
            </span>
          </div>

          <!-- Remove Favorite Button -->
          <button 
            type="button"
            @click.stop="promptRemoveFavorite(property)"
            class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white/90 dark:bg-slate-900/90 hover:bg-rose-50 dark:hover:bg-rose-950/80 backdrop-blur-xs flex items-center justify-center text-rose-500 transition-all shadow-md active:scale-90 cursor-pointer"
            title="Remove from favorites"
            aria-label="Remove favorite"
          >
            <Heart class="w-4 h-4 fill-rose-500 text-rose-500" />
          </button>

          <!-- Price Overlay -->
          <div class="absolute bottom-3 left-3 right-3 flex items-end justify-between text-white">
            <p class="text-lg sm:text-xl font-black drop-shadow-md">
              ETB {{ Number(property.price || 0).toLocaleString() }}
              <span v-if="property.listing_type === 'rent'" class="text-xs font-normal text-white/80">/month</span>
            </p>
          </div>
        </div>

        <!-- Card Body -->
        <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between">
          <div>
            <div @click="navigateToDetails(property.id)" class="cursor-pointer">
              <h3 class="font-bold text-sm sm:text-base text-slate-900 dark:text-white group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors line-clamp-1">
                {{ property.title }}
              </h3>
              <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5 mt-1 truncate">
                <MapPin class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 shrink-0" />
                {{ formatLocation(property.location || property.address || property.city) }}
              </p>

              <!-- Beds, Baths, Area -->
              <div class="grid grid-cols-3 gap-2 py-2.5 my-2.5 border-y border-slate-100 dark:border-slate-800 text-xs font-medium text-slate-600 dark:text-slate-300">
                <div class="flex items-center gap-1">
                  <BedDouble class="w-3.5 h-3.5 text-slate-400" />
                  <span>{{ property.bedrooms || property.beds || 0 }} Beds</span>
                </div>
                <div class="flex items-center gap-1">
                  <Bath class="w-3.5 h-3.5 text-slate-400" />
                  <span>{{ property.bathrooms || property.baths || 0 }} Baths</span>
                </div>
                <div class="flex items-center gap-1">
                  <Maximize2 class="w-3.5 h-3.5 text-slate-400" />
                  <span>{{ property.area || property.sqft || 120 }} m²</span>
                </div>
              </div>
            </div>

            <!-- Owner Info Section -->
            <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-800/80 mb-3">
              <div class="flex items-center gap-2 min-w-0">
                <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-xs shrink-0">
                  {{ (property.owner?.name?.[0] || 'O').toUpperCase() }}
                </div>
                <div class="min-w-0">
                  <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">
                    {{ property.owner?.name || 'Verified Landlord' }}
                  </p>
                  <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold flex items-center gap-0.5">
                    <ShieldCheck class="w-3 h-3 inline text-slate-400" />
                    {{ property.owner?.role || 'Landlord' }}
                  </span>
                </div>
              </div>

              <span class="text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-500 px-2 py-0.5 rounded-full font-medium capitalize">
                {{ property.property_type || 'Residential' }}
              </span>
            </div>
          </div>

          <!-- Card Footer: View Details CTA & 3-Dot Action Menu -->
          <div class="flex items-center gap-2 pt-3 border-t border-slate-100 dark:border-slate-800/80">
            <!-- Primary Action: View Details -->
            <RouterLink 
              :to="`/buyer/properties/${property.id}`"
              class="flex-1 py-2.5 px-3 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-xl transition-all text-xs font-bold text-center flex items-center justify-center gap-1.5 shadow-2xs active:scale-98 cursor-pointer"
            >
              <span>View Details</span>
              <ArrowRight class="w-3.5 h-3.5" />
            </RouterLink>

            <!-- Three-Dot Dropdown Trigger -->
            <button
              type="button"
              @click.stop="toggleActionMenu(property, $event)"
              class="w-10 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center transition-all cursor-pointer border border-slate-200/60 dark:border-slate-700/60 shrink-0 active:scale-95"
              title="More Actions"
              aria-label="More Actions"
            >
              <MoreVertical class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-8 sm:p-12 text-center">
      <EmptyState
        title="No favorite properties"
        description="Add properties to your favorites while browsing to easily access and compare them later."
        actionLabel="Browse Properties"
        @action="router.push('/buyer/properties')"
      />
    </div>

    <!-- Floating Teleported Three-Dot Action Dropdown Menu -->
    <Teleport to="body">
      <div v-if="activeMenu" class="fixed inset-0 z-[99998]" @click="activeMenu = null">
        <div
          :style="{ top: activeMenu.top, left: activeMenu.left }"
          class="fixed w-48 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 py-1.5 z-[99999] animate-in fade-in zoom-in-95 text-left"
          @click.stop
        >
          <!-- Book Viewing Tour -->
          <button
            @click="bookAppointment(activeMenu.property); activeMenu = null"
            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <Calendar class="w-3.5 h-3.5 text-slate-500" />
            <span>Book Visit</span>
          </button>

          <!-- Message Host / Landlord -->
          <button
            @click="sendMessage(activeMenu.property); activeMenu = null"
            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
          >
            <MessageSquare class="w-3.5 h-3.5 text-slate-500" />
            <span>Message Host</span>
          </button>

          <!-- Remove from Favorites -->
          <button
            @click="promptRemoveFavorite(activeMenu.property); activeMenu = null"
            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer border-t border-slate-100 dark:border-slate-800 mt-1 pt-1.5"
          >
            <Trash2 class="w-3.5 h-3.5" />
            <span>Remove Favorite</span>
          </button>
        </div>
      </div>
    </Teleport>

    <!-- Remove Favorite Confirmation Modal -->
    <ConfirmModal
      :isOpen="showRemoveModal"
      title="Remove Favorite"
      :message="`Are you sure you want to remove '${itemToRemove?.title || 'this property'}' from your saved favorites?`"
      confirmLabel="Remove"
      cancelLabel="Cancel"
      :danger="true"
      @confirm="confirmRemoveFavorite"
      @cancel="showRemoveModal = false; itemToRemove = null"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import {
  Heart,
  MapPin,
  BedDouble,
  Bath,
  Maximize2,
  Calendar,
  MessageSquare,
  ShieldCheck,
  ArrowRight,
  Trash2,
  MoreVertical
} from 'lucide-vue-next'
import { favoriteService } from '@/services/favoriteService'
import { useLanguage } from '@/composables/useLanguage'
import { useToastStore } from '@/stores/toast'
import { useAuthStore } from '@/stores/auth'
import EmptyState from '@/components/dashboard/EmptyState.vue'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'

const router = useRouter()
const { t } = useLanguage()
const toastStore = useToastStore()
const authStore = useAuthStore()

const isLoading = ref(true)
const favoritesList = ref([])
const showRemoveModal = ref(false)
const itemToRemove = ref(null)
const activeMenu = ref(null)

function toggleActionMenu(property, event) {
  if (activeMenu.value && activeMenu.value.property?.id === property.id) {
    activeMenu.value = null
    return
  }

  const rect = event.currentTarget.getBoundingClientRect()
  const menuWidth = 192
  const menuHeight = 130

  let left = rect.right - menuWidth
  if (left < 10) left = 10
  if (left + menuWidth > window.innerWidth - 10) {
    left = window.innerWidth - menuWidth - 10
  }

  let top = rect.bottom + 6
  if (top + menuHeight > window.innerHeight - 10) {
    top = rect.top - menuHeight - 6
  }

  activeMenu.value = {
    property,
    top: `${top}px`,
    left: `${left}px`
  }
}

async function fetchFavorites() {
  isLoading.value = true
  try {
    const res = await favoriteService.getFavorites()
    const payload = res?.data || {}
    const items = payload.data || payload || []
    favoritesList.value = Array.isArray(items) ? items : []
  } catch (err) {
    console.error('Failed to load favorites:', err)
    favoritesList.value = []
  } finally {
    isLoading.value = false
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

function promptRemoveFavorite(property) {
  itemToRemove.value = property
  showRemoveModal.value = true
}

async function confirmRemoveFavorite() {
  if (itemToRemove.value) {
    const target = itemToRemove.value
    // Instant reactive local update
    favoritesList.value = favoritesList.value.filter(p => p.id !== target.id)
    showRemoveModal.value = false
    itemToRemove.value = null

    try {
      await favoriteService.removeFavorite(target.id)
      toastStore.success('Removed from favorites')
    } catch (err) {
      console.warn('Sync favorite removal:', err)
      toastStore.info('Removed from favorites')
    }
  }
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
  fetchFavorites()
})
</script>
