<template>
  <section class="py-12 md:py-16 bg-slate-50/60 dark:bg-slate-950/60 border-y border-slate-200/80 dark:border-slate-800 transition-colors duration-200" aria-label="How BetLink works">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section header -->
      <div class="text-center max-w-2xl mx-auto mb-12">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900/5 dark:bg-white/10 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 text-xs font-bold uppercase tracking-wider mb-3">
          <span class="w-2 h-2 rounded-full bg-slate-900 dark:bg-white animate-pulse"></span>
          <span>{{ t('home.simple_process', 'Simple Process') }}</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-3">
          {{ t('home.how_title') || 'How BetLink Works' }}
        </h2>
        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400">
          {{ t('home.how_subtitle') || 'Find, tour, and secure your next home in four simple steps.' }}
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 relative">
        <article
          v-for="step in steps"
          :key="step.number"
          class="relative bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs hover:shadow-lg transition-all duration-300 flex flex-col items-center text-center space-y-4"
        >
          <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white dark:bg-white dark:text-slate-900 flex items-center justify-center font-extrabold text-base shadow-md">
            {{ step.number }}
          </div>

          <!-- Icon -->
          <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center">
            <component :is="step.icon" class="w-6 h-6" aria-hidden="true" />
          </div>

          <!-- Content -->
          <div class="space-y-1.5">
            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">{{ step.title }}</h3>
            <p class="text-slate-600 dark:text-slate-300 text-xs sm:text-sm leading-relaxed">
              {{ step.description }}
            </p>
          </div>
        </article>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, defineComponent, h } from 'vue'
import { RouterLink } from 'vue-router'
import { useLanguage } from '../../composables/useLanguage'

const { t } = useLanguage()

// Icon components
const SearchIcon = defineComponent({
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
    h('circle', { cx: '11', cy: '11', r: '8', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }),
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M21 21l-4.35-4.35' })
  ])
})

const GridIcon = defineComponent({
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
    h('rect', { x: '3', y: '3', width: '7', height: '7', rx: '1', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }),
    h('rect', { x: '14', y: '3', width: '7', height: '7', rx: '1', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }),
    h('rect', { x: '14', y: '14', width: '7', height: '7', rx: '1', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }),
    h('rect', { x: '3', y: '14', width: '7', height: '7', rx: '1', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' })
  ])
})

const MessageIcon = defineComponent({
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z' })
  ])
})

const KeyIcon = defineComponent({
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M21 2l-2 2m-7.61 7.61a5.5 5.5 0 11-7.778 7.778 5.5 5.5 0 017.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4' })
  ])
})

const steps = computed(() => [
  {
    number: '1',
    title: t('home.step1_title') || 'Search',
    description: t('home.step1_desc') || 'Filter by location, type, and budget.',
    icon: SearchIcon,
  },
  {
    number: '2',
    title: t('home.step2_title') || 'Explore',
    description: t('home.step2_desc') || 'Review verified photos and specs.',
    icon: GridIcon,
  },
  {
    number: '3',
    title: t('home.step3_title') || 'Connect',
    description: t('home.step3_desc') || 'Schedule direct viewings with owners.',
    icon: MessageIcon,
  },
  {
    number: '4',
    title: t('home.step4_title') || 'Move-in',
    description: t('home.step4_desc') || 'Finalize your agreement securely.',
    icon: KeyIcon,
  },
])
</script>
