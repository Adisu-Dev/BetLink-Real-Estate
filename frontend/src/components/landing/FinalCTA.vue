<template>
  <section class="py-12 md:py-16 bg-slate-50 dark:bg-slate-950 transition-colors duration-200" aria-label="Final call to action">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="relative rounded-3xl bg-slate-900 text-white overflow-hidden shadow-xl p-8 sm:p-14 lg:p-20 text-center border border-slate-800">
        <div class="relative z-10 max-w-3xl mx-auto space-y-6 sm:space-y-8">
          <!-- Badge -->
          <div class="inline-flex items-center gap-2 bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold tracking-widest uppercase px-4 py-1.5 rounded-full backdrop-blur-md">
            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse" aria-hidden="true"></span>
            {{ t('home.final_cta_badge') }}
          </div>

          <!-- Heading -->
          <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight tracking-tight">
            {{ t('home.final_cta_title') }}
          </h2>

          <!-- Description -->
          <p class="text-slate-300 text-sm sm:text-base lg:text-lg leading-relaxed max-w-2xl mx-auto">
            {{ t('home.final_cta_subtitle') }}
          </p>

          <!-- Buttons -->
          <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
            <RouterLink
              to="/properties"
              class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-lg shadow-emerald-950/50 transition-all transform active:scale-95"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="11" cy="11" r="8"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
              </svg>
              {{ t('home.view_all_properties') }}
            </RouterLink>

            <RouterLink
              to="/owner/properties/create"
              class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-sm border border-white/20 backdrop-blur-sm transition-all"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
              </svg>
              {{ t('nav.list_property') }}
            </RouterLink>
          </div>

          <!-- Trust stats -->
          <div class="grid grid-cols-3 gap-4 pt-8 border-t border-white/10 text-center max-w-lg mx-auto">
            <div>
              <p class="text-xl sm:text-2xl font-extrabold text-white">3,200+</p>
              <p class="text-[11px] sm:text-xs text-slate-400">{{ t('hero.stats_verified_units') }}</p>
            </div>
            <div>
              <p class="text-xl sm:text-2xl font-extrabold text-white">45,000+</p>
              <p class="text-[11px] sm:text-xs text-slate-400">{{ t('home.happy_users') }}</p>
            </div>
            <div>
              <p class="text-xl sm:text-2xl font-extrabold text-emerald-400">4.9 / 5</p>
              <p class="text-[11px] sm:text-xs text-slate-400">{{ t('home.user_rating') }}</p>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { RouterLink } from 'vue-router'
import { useLanguage } from '../../composables/useLanguage'

const { t } = useLanguage()
</script>
