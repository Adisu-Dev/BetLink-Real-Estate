<template>
  <section
    class="relative z-30 min-h-[100dvh] flex flex-col justify-center items-center pt-24 sm:pt-28 md:pt-32 pb-16 sm:pb-20 md:pb-24 transition-colors duration-200"
    aria-label="Hero section"
  >
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
      <img
        src="/images/hero-bg.webp"
        alt="Bespoke luxury villa in Ethiopia"
        class="w-full h-full object-cover object-center transform scale-100 transition-transform duration-1000 ease-out"
        fetchpriority="high"
        decoding="async"
      />
      <div class="absolute inset-0 bg-gradient-to-b from-black/65 via-black/35 to-black/80 dark:from-slate-950/80 dark:via-slate-950/55 dark:to-slate-950"></div>
    </div>

    <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 md:py-14">
      <div class="text-center mb-5 sm:mb-8 max-w-3xl mx-auto">
        <h1 class="text-2xl sm:text-3xl lg:text-3xl font-black text-white tracking-tight drop-shadow-[0_4px_16px_rgba(0,0,0,0.8)] leading-tight">
          {{ t('hero.headline', 'Rentals. Homes. Villas. Land.') }}
        </h1>
        <p class="text-xs sm:text-base text-slate-200/90 font-medium mt-2 sm:mt-3 drop-shadow-md">
          {{ t('hero.headline_sub', 'Discover vetted properties, luxury estates, and rental homes across Ethiopia.') }}
        </p>
      </div>

      <div class="max-w-5xl mx-auto">
        <PropertySearch :standalone="true" />
      </div>
    </div>
  </section>
</template>

<script setup>
import PropertySearch from './PropertySearch.vue'
import { useLanguage } from '../../composables/useLanguage'

const { t } = useLanguage()
</script>

