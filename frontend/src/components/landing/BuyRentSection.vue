<template>
  <section class="py-12 md:py-16 bg-slate-50 dark:bg-slate-950 transition-colors duration-200" aria-label="Buy or rent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
        
        <!-- Buy card -->
        <article
          class="relative overflow-hidden rounded-2xl group cursor-pointer border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-2xl transition-all duration-300 min-h-[380px] sm:min-h-[420px] flex flex-col justify-end p-6 sm:p-10"
          @click="navigateTo('sale')"
        >
          <div class="absolute inset-0 z-0">
            <img
              src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=1000&auto=format&fit=crop&q=80"
              alt="Homes for sale in Ethiopia"
              class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
              loading="lazy"
              decoding="async"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-900/60 to-slate-900/20"></div>
          </div>

          <div class="relative z-10 space-y-3 sm:space-y-4">
            <div class="inline-flex items-center gap-2 bg-emerald-500/20 backdrop-blur-md border border-emerald-400/30 text-emerald-300 text-xs font-bold tracking-wider uppercase px-3 py-1 rounded-full">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 11-7.778 7.778 5.5 5.5 0 017.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
              </svg>
              Properties for Sale
            </div>

            <h3 class="text-2xl font-extrabold text-white leading-tight">
              Find Your Dream Home to Buy
            </h3>
            
            <p class="text-slate-200 text-sm sm:text-base leading-relaxed max-w-md">
              Explore houses, modern condos, luxury villas, and plots ready for investment across prime Ethiopian locations.
            </p>

            <RouterLink
              to="/properties?listing_type=sale"
              class="inline-flex items-center gap-2 text-sm font-bold text-emerald-400 hover:text-emerald-300 pt-2"
              @click.stop
            >
              <span>Browse For Sale</span>
              <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </RouterLink>
          </div>
        </article>

        <!-- Rent card -->
        <article
          class="relative overflow-hidden rounded-2xl group cursor-pointer border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-2xl transition-all duration-300 min-h-[380px] sm:min-h-[420px] flex flex-col justify-end p-6 sm:p-10"
          @click="navigateTo('rent')"
        >
          <div class="absolute inset-0 z-0">
            <img
              src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=1000&auto=format&fit=crop&q=80"
              alt="Apartments for rent in Addis Ababa"
              class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
              loading="lazy"
              decoding="async"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-900/60 to-slate-900/20"></div>
          </div>

          <div class="relative z-10 space-y-3 sm:space-y-4">
            <div class="inline-flex items-center gap-2 bg-blue-500/20 backdrop-blur-md border border-blue-400/30 text-blue-300 text-xs font-bold tracking-wider uppercase px-3 py-1 rounded-full">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 21V12h6v9"/>
              </svg>
              Properties for Rent
            </div>

            <h3 class="text-2xl font-extrabold text-white leading-tight">
              Find Your Ideal Rental Property
            </h3>
            <p class="text-slate-200 text-sm sm:text-base leading-relaxed max-w-md">
              Browse monthly rentals, furnished apartments, and commercial spaces with direct owner contact.
            </p>

            <RouterLink
              to="/properties?listing_type=rent"
              class="inline-flex items-center gap-2 text-sm font-bold text-blue-400 hover:text-blue-300 pt-2"
              @click.stop
            >
              <span>Browse For Rent</span>
              <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </RouterLink>
          </div>
        </article>

      </div>
    </div>
  </section>
</template>

<script setup>
import { RouterLink, useRouter } from 'vue-router'

const router = useRouter()

function navigateTo(type) {
  router.push({ name: 'properties', query: { listing_type: type } })
}
</script>
