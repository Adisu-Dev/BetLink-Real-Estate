<template>
  <section class="py-12 md:py-16 bg-slate-50/60 dark:bg-slate-950/60 border-y border-slate-200/80 dark:border-slate-800 transition-colors duration-200" aria-label="User testimonials">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section header -->
      <div class="text-center max-w-2xl mx-auto mb-12">
        <p class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-2">{{ t('property.verified') }}</p>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-3">
          {{ t('home.testimonials_title') }}
        </h2>
        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400">
          {{ t('home.testimonials_subtitle') }}
        </p>
      </div>

      <!-- Testimonial grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
        <article
          v-for="testimonial in testimonials"
          :key="testimonial.name"
          class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-7 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between space-y-6"
        >
          <!-- Top row with rating -->
          <div class="space-y-3">
            <div class="flex items-center gap-1">
              <svg
                v-for="star in 5"
                :key="star"
                class="w-4 h-4"
                :class="star <= testimonial.rating ? 'text-amber-400 fill-current' : 'text-slate-200 dark:text-slate-700 fill-current'"
                viewBox="0 0 20 20"
                aria-hidden="true"
              >
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
            </div>

            <!-- Review text -->
            <p class="text-slate-600 dark:text-slate-300 text-xs sm:text-sm leading-relaxed">
              "{{ testimonial.review }}"
            </p>
          </div>

          <!-- User info -->
          <div class="flex items-center gap-3.5 pt-4 border-t border-slate-100 dark:border-slate-800">
            <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 flex items-center justify-center font-bold text-xs flex-shrink-0">
              {{ testimonial.initials }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm truncate">{{ testimonial.name }}</p>
              <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ testimonial.role }} &bull; {{ testimonial.location }}</p>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>
</template>

<script setup>
import { useLanguage } from '../../composables/useLanguage'

const { t } = useLanguage()

const testimonials = [
  {
    name: 'Abebe Kebede',
    initials: 'AK',
    role: 'Home Buyer',
    location: 'Bole, Addis Ababa',
    rating: 5,
    review: 'BetLink made finding my dream home in Bole completely seamless. The verified property listings gave me full confidence and I was able to talk directly with the developer.',
  },
  {
    name: 'Marta Tesfaye',
    initials: 'MT',
    role: 'Tenant',
    location: 'CMC, Addis Ababa',
    rating: 5,
    review: 'I found a wonderful 2-bedroom apartment for my family within 3 days. The search filters were precise and the viewing booking was hassle-free.',
  },
  {
    name: 'Solomon Haile',
    initials: 'SH',
    role: 'Property Owner',
    location: 'Kazanchis, Addis Ababa',
    rating: 5,
    review: 'Listing my luxury apartment on BetLink brought in serious inquiries within hours. The dashboard makes managing appointments and inquiries very smooth.',
  },
]
</script>
