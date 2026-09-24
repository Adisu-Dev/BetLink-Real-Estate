<template>
  <section class="py-12 md:py-16 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200" aria-label="Property types">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Section header -->
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
        <div class="max-w-2xl">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900/5 dark:bg-white/10 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 text-xs font-extrabold uppercase tracking-widest mb-3">
            <span class="w-2 h-2 rounded-full bg-slate-900 dark:bg-white animate-pulse"></span>
            <span>{{ t('categories.explore_tag', 'Explore Categories') }}</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
            {{ t('categories.title', 'Find the Right Property for Your Needs') }}
          </h2>
          <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 mt-2 leading-relaxed">
            {{ t('categories.subtitle', 'Browse through curated residential, luxury, and commercial property spaces across Addis Ababa and top regional hubs.') }}
          </p>
        </div>

        <!-- Quick filter / direct link -->
        <RouterLink
          to="/properties"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 dark:bg-white/10 hover:bg-slate-800 dark:hover:bg-white/20 text-white text-xs sm:text-sm font-bold transition-all duration-300 shadow-md group self-start md:self-auto"
        >
          <span>{{ t('categories.view_all', 'View All Listings') }}</span>
          <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </RouterLink>
      </div>

      <!-- Modern Visual Architectural Category Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6">
        <article
          v-for="type in displayTypes"
          :key="type.slug"
          class="group relative h-72 sm:h-80 rounded-2xl overflow-hidden cursor-pointer border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-300 transform hover:-translate-y-0.5 flex flex-col justify-between p-6"
          @click="navigateToType(type.slug)"
        >
          <!-- Background Image with smooth Zoom -->
          <div class="absolute inset-0 z-0">
            <img
              :src="type.image"
              :alt="type.name"
              class="w-full h-full object-cover object-center transform group-hover:scale-105 transition-transform duration-500 ease-out"
              loading="lazy"
              decoding="async"
            />
            <!-- Dark Vignette Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/60 to-slate-950/20 group-hover:from-slate-950/95 transition-colors duration-300"></div>
          </div>

          <!-- Top Row: Icon badge + Count pill -->
          <div class="relative z-10 flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md border border-white/25 flex items-center justify-center text-white shadow-sm group-hover:bg-white/30 transition-all duration-200">
              <component :is="type.icon" class="w-5 h-5" aria-hidden="true" />
            </div>

            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-950/70 border border-white/20 backdrop-blur-md text-slate-200">
              <span>{{ type.countText }}</span>
            </span>
          </div>

          <!-- Bottom Row: Title & Explore link -->
          <div class="relative z-10">
            <h3 class="text-2xl font-bold text-white tracking-tight leading-snug mb-3">
              {{ type.name }}
            </h3>

            <div class="flex items-center justify-between pt-2.5 border-t border-white/15 text-xs font-medium text-slate-300 group-hover:text-white transition-colors">
              <span>{{ t('categories.explore', 'Explore') }}</span>
              <div class="w-6 h-6 rounded-full bg-white/15 group-hover:bg-white/25 flex items-center justify-center text-white transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
              </div>
            </div>
          </div>

        </article>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, defineComponent, h } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { propertyService } from '../../services/propertyService'
import { useLanguage } from '../../composables/useLanguage'

const router = useRouter()
const { t } = useLanguage()

// SVG Icon definitions
const HouseIcon = defineComponent({
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z' }),
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M9 21V12h6v9' })
  ])
})

const VillaIcon = defineComponent({
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3 10l9-7 9 7v10a1 1 0 01-1 1H4a1 1 0 01-1-1V10z' }),
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M9 21V12h6v9' }),
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M7 10h10' })
  ])
})

const ApartmentIcon = defineComponent({
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
    h('rect', { x: '4', y: '2', width: '16', height: '20', rx: '2' }),
    h('line', { x1: '9', y1: '6', x2: '9', y2: '6.01' }),
    h('line', { x1: '15', y1: '6', x2: '15', y2: '6.01' }),
    h('line', { x1: '9', y1: '10', x2: '9', y2: '10.01' }),
    h('line', { x1: '15', y1: '10', x2: '15', y2: '10.01' }),
    h('line', { x1: '9', y1: '14', x2: '9', y2: '14.01' }),
    h('line', { x1: '15', y1: '14', x2: '15', y2: '14.01' }),
    h('path', { d: 'M10 22v-4h4v4' })
  ])
})

const LandIcon = defineComponent({
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3 21h18M5 21V8l4-5 4 3 4-3v18' })
  ])
})

const CommercialIcon = defineComponent({
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z' }),
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M15 13a3 3 0 11-6 0 3 3 0 016 0z' })
  ])
})

const OfficeIcon = defineComponent({
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' })
  ])
})

const HotelIcon = defineComponent({
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3 20V8a1 1 0 011-1h4V4a1 1 0 011-1h8a1 1 0 011 1v3h4a1 1 0 011 1v12' }),
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3 12h18M7 8h2M7 12h2M7 16h2M15 8h2M15 12h2M15 16h2' })
  ])
})

const CalendarIcon = defineComponent({
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
    h('rect', { x: '3', y: '4', width: '18', height: '18', rx: '2', ry: '2' }),
    h('line', { x1: '16', y1: '2', x2: '16', y2: '6' }),
    h('line', { x1: '8', y1: '2', x2: '8', y2: '6' }),
    h('line', { x1: '3', y1: '10', x2: '21', y2: '10' })
  ])
})

const liveCounts = ref({})

const baseCategories = [
  {
    name: 'Villas',
    slug: 'villa',
    image: 'https://images.unsplash.com/photo-1613977257363-707ba9348227?w=600&q=75&auto=format&fit=crop',
    icon: VillaIcon
  },
  {
    name: 'Apartments',
    slug: 'apartment',
    image: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=75&auto=format&fit=crop',
    icon: ApartmentIcon
  },
  {
    name: 'Commercial',
    slug: 'commercial',
    image: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=600&q=75&auto=format&fit=crop',
    icon: CommercialIcon
  },
  {
    name: 'Offices',
    slug: 'office',
    image: 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=600&q=75&auto=format&fit=crop',
    icon: OfficeIcon
  },
  {
    name: 'Land',
    slug: 'land',
    image: 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=600&q=75&auto=format&fit=crop',
    icon: LandIcon
  },
  {
    name: 'Hotels',
    slug: 'hotel',
    image: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=75&auto=format&fit=crop',
    icon: HotelIcon
  },
  {
    name: 'Warehouses',
    slug: 'warehouse',
    image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=600&q=75&auto=format&fit=crop',
    icon: HouseIcon
  },
  {
    name: 'Vacation',
    slug: 'short-rental',
    image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=600&q=75&auto=format&fit=crop',
    icon: CalendarIcon
  },
]

const displayTypes = computed(() => {
  return baseCategories.map(cat => {
    const count = liveCounts.value[cat.slug]
    const countText = (count !== undefined && count > 0)
      ? `${count} ${t('categories.listings', 'listings')}`
      : t('categories.verified', 'Verified')
    const slugKey = cat.slug.replace(/-/g, '_')
    const localizedName = t(`categories.${slugKey}`, cat.name)
    return {
      ...cat,
      name: localizedName,
      countText
    }
  })
})

const fetchLiveCounts = async () => {
  try {
    const res = await propertyService.getTypes()
    const typesList = res?.data || (Array.isArray(res) ? res : [])
    if (Array.isArray(typesList)) {
      const counts = {}
      typesList.forEach(t => {
        if (t.slug) {
          counts[t.slug] = t.properties_count || 0
        }
      })
      liveCounts.value = counts
    }
  } catch {
    // Graceful fallback
  }
}

function navigateToType(slug) {
  router.push({ path: '/properties', query: { type: slug } })
}

onMounted(() => {
  fetchLiveCounts()
})
</script>
