<template>
  <div class="group bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 rounded-2xl overflow-hidden shadow-xs hover:shadow-md transition-all duration-200 flex flex-col h-full">
    <!-- Image & Overlays Container -->
    <div class="relative aspect-[16/10] overflow-hidden bg-slate-100 dark:bg-slate-800">
      <button
        v-if="inlineMode"
        type="button"
        @click="$emit('select', property)"
        class="block w-full h-full text-left cursor-pointer focus:outline-none"
      >
        <img
          :src="imageUrl"
          :alt="property.title"
          class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
          loading="lazy"
          decoding="async"
          @error="handleImageError"
        />
      </button>
      <RouterLink
        v-else
        :to="propertyLink"
        class="block w-full h-full"
      >
        <img
          :src="imageUrl"
          :alt="property.title"
          class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
          loading="lazy"
          decoding="async"
          @error="handleImageError"
        />
      </RouterLink>

      <!-- Status badge (Left) -->
      <div class="absolute top-3 left-3 pointer-events-none z-10">
        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-900/85 backdrop-blur-md text-white border border-white/10 shadow-xs">
          {{ formattedListingType }}
        </span>
      </div>

      <!-- Favorite button -->
      <button
        type="button"
        :class="[
          'absolute top-3 right-3 w-8 h-8 rounded-full backdrop-blur-md flex items-center justify-center transition-all duration-150 focus:outline-none shadow-md z-20 cursor-pointer',
          isFavorited
            ? 'bg-rose-500 text-white shadow-rose-500/30 scale-105'
            : 'bg-white/95 dark:bg-slate-900/95 text-slate-600 dark:text-slate-300 hover:text-rose-500 hover:scale-110'
        ]"
        :aria-label="isFavorited ? t('property.saved') : t('property.save_favorite')"
        @click.prevent="toggleFavorite"
      >
        <svg
          class="w-4 h-4"
          :fill="isFavorited ? 'currentColor' : 'none'"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
          />
        </svg>
      </button>

      <!-- Price overlay -->
      <div class="absolute bottom-3 left-3 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md px-3 py-1.5 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <span class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white">
          {{ formatPrice(property.price, property.currency || 'ETB') }}
        </span>
        <span v-if="property.listing_type === 'rent' || property.listingType === 'For Rent'" class="text-xs text-slate-500 dark:text-slate-400"> {{ t('property.per_month') }}</span>
        <span v-else-if="property.listing_type === 'short_rent' || property.listingType === 'Short Stay'" class="text-xs text-slate-500 dark:text-slate-400"> {{ t('property.per_night') }}</span>
      </div>
    </div>

    <!-- Content -->
    <div class="p-4 sm:p-5 flex flex-col flex-1 justify-between bg-white dark:bg-slate-900 transition-colors">
      <div>
        <div class="flex items-center text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">
          <svg class="w-3.5 h-3.5 mr-1 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <span class="truncate">{{ locationText }}</span>
        </div>

        <button
          v-if="inlineMode"
          type="button"
          @click="$emit('select', property)"
          class="block text-left hover:text-slate-700 dark:hover:text-slate-300 transition-colors cursor-pointer w-full focus:outline-none"
        >
          <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white line-clamp-1">
            {{ displayTitle }}
          </h3>
        </button>
        <RouterLink
          v-else
          :to="propertyLink"
          class="block hover:text-slate-700 dark:hover:text-slate-300 transition-colors"
        >
          <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white line-clamp-1">
            {{ displayTitle }}
          </h3>
        </RouterLink>
      </div>

      <!-- Clean Minimal Bottom Row (Category / Location + View Details) -->
      <div class="mt-3.5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 dark:text-slate-400 truncate min-w-0">
          <span class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-500 shrink-0"></span>
          <span class="truncate">{{ displayCategory }}</span>
        </span>

        <button
          v-if="inlineMode"
          type="button"
          @click="$emit('select', property)"
          class="inline-flex items-center gap-1 text-xs font-bold text-slate-900 dark:text-white hover:text-slate-600 dark:hover:text-slate-300 transition-colors cursor-pointer focus:outline-none shrink-0"
        >
          <span>{{ t('property.view_details') || 'View Details' }}</span>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
        <RouterLink
          v-else
          :to="propertyLink"
          class="inline-flex items-center gap-1 text-xs font-bold text-slate-900 dark:text-white hover:text-slate-600 dark:hover:text-slate-300 transition-colors shrink-0"
        >
          <span>{{ t('property.view_details') || 'View Details' }}</span>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { formatPrice, formatArea, getListingTypeVariant } from '../../utils/formatters'
import { useAuthStore } from '../../stores/auth'
import { useToastStore } from '../../stores/toast'
import { favoriteService } from '../../services/favoriteService'
import { useLanguage } from '../../composables/useLanguage'

const { t } = useLanguage()

const props = defineProps({
  property: {
    type: Object,
    required: true,
  },
  inlineMode: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['select'])

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const toastStore = useToastStore()

const isFavorited = ref(Boolean(props.property.is_favorited || props.property.is_favorite))

watch(() => props.property.is_favorited || props.property.is_favorite, (val) => {
  isFavorited.value = Boolean(val)
})

const propertyLink = computed(() => {
  const propertyIdentifier = props.property.id ?? props.property.slug
  if (!propertyIdentifier) return '/properties'
  if (route.path.startsWith('/buyer')) {
    return `/buyer/properties/${propertyIdentifier}`
  }
  return `/properties/${propertyIdentifier}`
})

const formattedListingType = computed(() => {
  const type = props.property.listing_type || props.property.listingType
  if (type === 'sale' || type === 'For Sale') return t('property.for_sale')
  if (type === 'rent' || type === 'For Rent') return t('property.for_rent')
  if (type === 'short_rent' || type === 'Short Stay') return t('property.short_stay')
  return type || t('nav.properties')
})

const bedroomsDisplay = computed(() => {
  const p = props.property
  const num = p.bedrooms ?? p.beds
  if (num !== undefined && num !== null && num !== '') {
    return `${num} ${t('property.beds') || 'Beds'}`
  }
  return '—'
})

const bathroomsDisplay = computed(() => {
  const p = props.property
  const num = p.bathrooms ?? p.baths
  if (num !== undefined && num !== null && num !== '') {
    return `${num} ${t('property.baths') || 'Baths'}`
  }
  return '—'
})

const areaDisplay = computed(() => {
  const p = props.property
  const val = p.area || p.area_sqm || p.sqft
  if (!val) return '—'
  return formatArea(val)
})

const displayTitle = computed(() => {
  const p = props.property
  if (!p) return ''
  
  // 1. Check raw slug
  if (p.slug) {
    const rawRes = t(`property_titles.${p.slug}`, '')
    if (rawRes && !rawRes.startsWith('property_titles.')) return rawRes

    // 2. Strip trailing database IDs (e.g. arba-minch-resort-42 -> arba-minch-resort)
    const cleanedSlug = p.slug.replace(/-\d+$/, '')
    const cleanRes = t(`property_titles.${cleanedSlug}`, '')
    if (cleanRes && !cleanRes.startsWith('property_titles.')) return cleanRes
  }

  // 3. Check normalized title
  if (p.title) {
    const titleSlug = p.title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')
    const titleRes = t(`property_titles.${titleSlug}`, '')
    if (titleRes && !titleRes.startsWith('property_titles.')) return titleRes
  }

  return p.title || ''
})

const displayCategory = computed(() => {
  const p = props.property
  const raw = p.category || p.type || p.property_type?.name || p.property_type?.slug || ''
  if (!raw) return t('property.verified_listing', 'Verified Listing')
  const slug = String(raw).toLowerCase().replace(/[\s/_-]+/g, '_')
  const translated = t(`property_types.${slug}`, '') || t(`categories.${slug}`, '')
  return translated || raw
})

const locationText = computed(() => {
  const p = props.property
  if (!p) return `${t('cities.addis_ababa', 'Addis Ababa')}, Ethiopia`
  const parts = []

  const extractName = (val) => {
    if (!val) return ''
    if (typeof val === 'string') return val.trim()
    if (typeof val === 'object') return val.name || val.title || val.slug || ''
    return String(val)
  }

  const localizePart = (name) => {
    if (!name) return ''
    const slug = name.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '')
    const cleanSlug = slug.replace(/_(city|town|sub_city|subcity)$/, '')

    const fromCity = t(`cities.${slug}`, '') || t(`cities.${cleanSlug}`, '')
    if (fromCity && !fromCity.startsWith('cities.')) return fromCity

    const fromSubcity = t(`subcities.${slug}`, '') || t(`subcities.${cleanSlug}`, '')
    if (fromSubcity && !fromSubcity.startsWith('subcities.')) return fromSubcity

    const fromLoc = t(`locations.${slug}`, '') || t(`locations.${cleanSlug}`, '')
    if (fromLoc && !fromLoc.startsWith('locations.')) return fromLoc

    return name
  }

  const subCity = extractName(p.address?.subCity) || extractName(p.address?.sub_city) || extractName(p.subcity) || extractName(p.sub_city)
  if (subCity && !subCity.includes('[object Object]')) parts.push(localizePart(subCity))

  const city = extractName(p.address?.city) || extractName(p.city)
  if (city && !city.includes('[object Object]')) parts.push(localizePart(city))

  if (parts.length === 0) {
    let raw = ''
    if (typeof p.address === 'string' && p.address) raw = p.address
    else if (p.address?.full_address) raw = p.address.full_address
    else if (p.address?.street) raw = p.address.street
    else if (p.location && typeof p.location === 'string') raw = p.location

    if (raw) {
      const segments = raw.split(',').map(s => s.trim())
      const localizedSegments = segments.map(s => localizePart(s))
      parts.push(...localizedSegments)
    }
  }

  return parts.join(', ') || `${t('cities.addis_ababa', 'Addis Ababa')}, Ethiopia`
})

const imageUrl = computed(() => {
  const p = props.property
  if (!p) return 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&q=80'

  if (typeof p.primary_image === 'string' && p.primary_image) return p.primary_image
  if (p.primary_image?.url) return p.primary_image.url
  if (typeof p.primaryImage === 'string' && p.primaryImage) return p.primaryImage
  if (p.primaryImage?.url) return p.primaryImage.url
  if (p.image_url) return p.image_url
  if (p.image && typeof p.image === 'string') return p.image

  if (Array.isArray(p.images) && p.images.length > 0) {
    const first = p.images[0]
    if (typeof first === 'string') return first
    if (first?.url) return first.url
    if (first?.image_url) return first.image_url
  }

  return 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&q=80'
})

const handleImageError = (e) => {
  e.target.src = 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&q=80'
}

const toggleFavorite = async () => {
  if (!authStore.isAuthenticated) {
    toastStore.info('Please sign in to save favorites')
    router.push({ path: '/login', query: { redirect: router.currentRoute.value.fullPath } })
    return
  }

  const ownerId = props.property.user_id || props.property.owner_id || props.property.owner?.id
  if (ownerId && authStore.user?.id && String(ownerId) === String(authStore.user.id)) {
    toastStore.info('You are the owner of this property.')
    return
  }

  const previousState = isFavorited.value
  isFavorited.value = !previousState

  try {
    if (previousState) {
      await favoriteService.removeFavorite(props.property.id)
      toastStore.success('Removed from saved favorites')
    } else {
      await favoriteService.addFavorite(props.property.id)
      toastStore.success('Added to saved favorites')
    }
  } catch {
    isFavorited.value = previousState
    toastStore.error('Could not update favorites. Please try again.')
  }
}
</script>
