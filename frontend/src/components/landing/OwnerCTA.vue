<template>
  <section class="py-12 md:py-16 bg-slate-50 dark:bg-slate-950 transition-colors duration-200" aria-label="Property owner call to action">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="relative rounded-3xl bg-slate-900 dark:bg-slate-900 text-white overflow-hidden shadow-xl p-8 sm:p-12 lg:p-16 border border-slate-800">
        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14 items-center">
          <!-- Content -->
          <div class="space-y-6 text-center lg:text-left">
            <div class="inline-flex items-center gap-2 bg-slate-800/80 border border-slate-700 text-slate-200 text-xs font-bold tracking-wider uppercase px-3.5 py-1.5 rounded-full">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
              </svg>
              <span>For Owners</span>
            </div>

            <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight">
              Are You a Property Owner or Developer?
            </h2>

            <p class="text-slate-300 text-sm sm:text-base leading-relaxed max-w-xl">
              List your home, apartment, or commercial space and reach thousands of qualified buyers and tenants every day.
            </p>

            <!-- Buttons -->
            <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 pt-2">
              <RouterLink
                to="/owner/properties?add=1"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-bold text-xs sm:text-sm transition-colors cursor-pointer shadow-2xs"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                List Property
              </RouterLink>

              <RouterLink
                to="/about"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-bold text-xs sm:text-sm transition-colors cursor-pointer shadow-2xs"
              >
                About
              </RouterLink>
            </div>
          </div>

          <!-- Image with overlay cards -->
          <div class="relative">
            <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-white/10">
              <img
                src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&auto=format&fit=crop&q=80"
                alt="Modern villa in Addis Ababa"
                class="w-full h-80 sm:h-96 object-cover"
                loading="lazy"
                decoding="async"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 to-transparent"></div>
            </div>

            <!-- Floating stat cards -->
            <div class="absolute -bottom-4 right-4 sm:right-6 bg-white dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl shadow-xl p-4 space-y-0.5 border border-slate-200 dark:border-slate-700">
              <p class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">2,500+</p>
              <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Owners</p>
            </div>

            <div class="absolute top-4 left-4 sm:left-6 bg-white dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl shadow-xl p-4 space-y-0.5 border border-slate-200 dark:border-slate-700">
              <p class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">99.4%</p>
              <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Success Rate</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { RouterLink } from 'vue-router'
</script>
