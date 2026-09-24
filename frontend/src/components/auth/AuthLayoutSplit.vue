<template>
  <div class="min-h-[calc(100vh-4rem)] lg:min-h-[calc(100vh-5rem)] bg-slate-50 dark:bg-slate-950 flex flex-col transition-colors duration-200">
    <div :class="[
      'w-full flex flex-1',
      centered ? 'items-center justify-center p-4 sm:p-6 lg:p-8' : 'lg:grid lg:grid-cols-12'
    ]">
      
      <!-- Visual panel (desktop only) -->
      <div
        v-if="!centered"
        class="hidden lg:flex lg:col-span-5 xl:col-span-6 relative bg-slate-950 overflow-hidden"
      >
        <img
          :src="image"
          alt="Modern luxury real estate architecture"
          class="absolute inset-0 w-full h-full object-cover object-center transform scale-105 transition-transform duration-700 ease-out hover:scale-100"
          fetchpriority="high"
          decoding="async"
        />

        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-900/60 to-slate-950/50"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/70 via-transparent to-transparent"></div>

        <div class="relative z-10 w-full flex flex-col justify-between p-10 xl:p-14 text-white">
          
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold uppercase tracking-wider backdrop-blur-md w-fit">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
            {{ t('auth.property_hub_badge', 'Ethiopian Property Hub') }}
          </div>

          <div class="space-y-6 max-w-lg my-auto pt-10">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight tracking-tight">
              {{ headline }}
            </h1>

            <p v-if="subtitle" class="text-slate-300 text-sm xl:text-base leading-relaxed">
              {{ subtitle }}
            </p>

            <div class="grid grid-cols-3 gap-3 pt-2">
              <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 border border-white/10">
                <p class="text-lg xl:text-xl font-extrabold text-emerald-400">3,200+</p>
                <p class="text-[10px] xl:text-xs text-slate-300">{{ t('auth.verified_units', 'Verified Units') }}</p>
              </div>
              <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 border border-white/10">
                <p class="text-lg xl:text-xl font-extrabold text-white">45,000+</p>
                <p class="text-[10px] xl:text-xs text-slate-300">{{ t('auth.active_users', 'Active Users') }}</p>
              </div>
              <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 border border-white/10">
                <p class="text-lg xl:text-xl font-extrabold text-slate-200">98.5%</p>
                <p class="text-[10px] xl:text-xs text-slate-300">{{ t('auth.deal_success', 'Deal Success') }}</p>
              </div>
            </div>

            <div v-if="quote" class="bg-slate-900/70 backdrop-blur-md rounded-2xl p-4 border border-white/10 space-y-2">
              <div class="flex items-center gap-1 text-amber-400 text-xs">
                ★★★★★
              </div>
              <p class="text-xs text-slate-200 italic leading-relaxed">
                "{{ quote }}"
              </p>
              <p v-if="quoteAuthor" class="text-[11px] font-semibold text-slate-300">
                — {{ quoteAuthor }}
              </p>
            </div>
          </div>

          <div class="pt-6 text-xs text-slate-400 flex items-center justify-between gap-4 flex-wrap">
            <span>{{ t('auth.copyright', `© ${new Date().getFullYear()} BetLink Real Estate`, { year: new Date().getFullYear() }) }}</span>
            <span class="flex items-center gap-1.5">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
              {{ t('auth.secure_encryption', 'Secure Encryption') }}
            </span>
          </div>

        </div>
      </div>

      <!-- Form panel -->
      <div :class="[
        'flex-1 flex flex-col justify-center items-center py-6 sm:py-8 lg:py-10 px-4 sm:px-6 lg:px-8',
        centered ? 'w-full max-w-xl' : 'lg:col-span-7 xl:col-span-6'
      ]">
        <div class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 transition-all duration-200">
          <slot />
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { useLanguage } from '@/composables/useLanguage'

const { t } = useLanguage()

defineProps({
  image: {
    type: String,
    default: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1600&auto=format&fit=crop&q=80',
  },
  centered: {
    type: Boolean,
    default: false,
  },
  headline: {
    type: String,
    default: 'Find your dream home or manage properties seamlessly in Ethiopia.',
  },
  subtitle: {
    type: String,
    default: '',
  },
  quote: {
    type: String,
    default: 'BetLink made discovering and securing our luxury home in Bole remarkably simple, secure, and transparent.',
  },
  quoteAuthor: {
    type: String,
    default: 'Marta & Dawit, Bole Homeowners',
  },
})
</script>