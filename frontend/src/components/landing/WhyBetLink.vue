<template>
  <section class="py-12 md:py-16 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200" aria-label="Why choose BetLink">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section header -->
      <div class="text-center max-w-2xl mx-auto mb-12">
        <p class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-2">Our Advantage</p>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-3">
          {{ t('home.why_title') }}
        </h2>
        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400">
          {{ t('home.why_subtitle') }}
        </p>
      </div>

      <!-- Feature grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
        <article
          v-for="feature in features"
          :key="feature.title"
          class="bg-slate-50/50 dark:bg-slate-900/60 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-200 flex flex-col items-center text-center space-y-4 group"
        >
          <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center transition-transform duration-200 group-hover:scale-105 shadow-xs">
            <component :is="feature.icon" class="w-7 h-7" aria-hidden="true" />
          </div>

          <div class="space-y-1.5">
            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white transition-colors">
              {{ feature.title }}
            </h3>
            <p class="text-slate-600 dark:text-slate-300 text-xs sm:text-sm leading-relaxed">
              {{ feature.description }}
            </p>
          </div>
        </article>
      </div>

      <!-- Stats bar -->
      <div class="mt-12 p-8 rounded-2xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200/80 dark:border-slate-800">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
          <div
            v-for="stat in stats"
            :key="stat.label"
            class="space-y-1"
          >
            <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ stat.value }}</p>
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 font-semibold">{{ stat.label }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, defineComponent, h } from 'vue'
import { useLanguage } from '../../composables/useLanguage'

const { t } = useLanguage()

// Icon components
const ShieldCheckIcon = defineComponent({
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' })
  ])
})

const SearchCircleIcon = defineComponent({
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
    h('circle', { cx: '11', cy: '11', r: '8', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }),
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M21 2l-2 2m-7.61 7.61a5.5 5.5 0 11-7.778 7.778 5.5 5.5 0 017.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4' })
  ])
})

const HeartIcon = defineComponent({
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' })
  ])
})

const ChatIcon = defineComponent({
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z' })
  ])
})

const features = computed(() => [
  {
    title: t('property.verified', 'Verified Listings'),
    description: 'Every property goes through strict documentation and identity verification.',
    icon: ShieldCheckIcon,
  },
  {
    title: t('hero.search_btn', 'Smart Search & Filter'),
    description: 'Pinpoint properties by exact neighborhood, price threshold, and specific amenities.',
    icon: SearchCircleIcon,
  },
  {
    title: t('nav.saved_favorites', 'Save & Compare'),
    description: 'Bookmark listings to your favorites and compare unit specs side by side.',
    icon: HeartIcon,
  },
  {
    title: t('dashboard.direct_messages', 'Direct Communication'),
    description: 'Chat directly with certified owners and agents with zero hidden mediator markups.',
    icon: ChatIcon,
  },
])

const stats = computed(() => [
  { value: '12,000+', label: t('hero.stats_verified_units') },
  { value: '45,000+', label: t('hero.stats_active_users') },
  { value: '3,200+', label: t('property.verified') },
  { value: '99.4%', label: t('hero.stats_deal_success') },
])
</script>
