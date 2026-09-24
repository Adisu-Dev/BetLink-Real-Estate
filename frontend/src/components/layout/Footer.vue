<template>
  <footer class="bg-white dark:bg-slate-900 border-t border-slate-200/80 dark:border-slate-800 text-slate-600 dark:text-slate-400 transition-colors duration-200" aria-label="Site footer">
    <!-- Main Footer Container -->
    <div class="max-w-7xl mx-auto px-6 py-10">
      <!-- 4-Column Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-8">
        
        <!-- Column 1: Brand Logo, Tagline & Social Icons -->
        <div class="space-y-3.5">
          <RouterLink 
            to="/" 
            class="flex items-center gap-2.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 rounded-xl w-fit" 
            aria-label="BetLink home"
          >
            <div class="w-8 h-8 bg-slate-900 dark:bg-white rounded-xl flex items-center justify-center shadow-md">
              <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4 text-white dark:text-slate-900" aria-hidden="true">
                <path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                <path d="M9 21V12h6v9" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
              </svg>
            </div>
            <span class="text-lg font-black tracking-tight text-slate-900 dark:text-white">
              BetLink
            </span>
          </RouterLink>

          <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed max-w-xs">
            {{ t('footer.description', 'Making property discovery, purchase, and rental seamless, transparent, and verified across Ethiopia.') }}
          </p>

          <!-- Social Icons -->
          <div class="flex items-center gap-2 pt-1">
            <a 
              v-for="social in socials" 
              :key="social.name" 
              :href="social.href" 
              :aria-label="social.name"
              target="_blank"
              rel="noopener noreferrer"
              class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center transition-all duration-200 hover:scale-105"
            >
              <component :is="social.icon" class="w-3.5 h-3.5" />
            </a>
          </div>
        </div>

        <!-- Column 2: Explore -->
        <div class="space-y-3">
          <h3 class="text-slate-900 dark:text-white font-bold text-xs uppercase tracking-wider">
            {{ t('footer.explore', 'Explore') }}
          </h3>
          <ul class="space-y-2 text-xs" role="list">
            <li v-for="link in exploreLinks" :key="link.name">
              <RouterLink 
                :to="link.to"
                class="text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:underline underline-offset-4 font-medium transition-colors duration-150"
              >
                {{ link.name }}
              </RouterLink>
            </li>
          </ul>
        </div>

        <!-- Column 3: For Owners -->
        <div class="space-y-3">
          <h3 class="text-slate-900 dark:text-white font-bold text-xs uppercase tracking-wider">
            {{ t('footer.for_owners', 'For Owners') }}
          </h3>
          <ul class="space-y-2 text-xs" role="list">
            <li v-for="link in ownerLinks" :key="link.name">
              <RouterLink 
                :to="link.to"
                class="text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:underline underline-offset-4 font-medium transition-colors duration-150"
              >
                {{ link.name }}
              </RouterLink>
            </li>
          </ul>
        </div>

        <!-- Column 4: Company & Legal -->
        <div class="space-y-3">
          <h3 class="text-slate-900 dark:text-white font-bold text-xs uppercase tracking-wider">
            {{ t('footer.company_legal', 'Company & Legal') }}
          </h3>
          <ul class="space-y-2 text-xs" role="list">
            <li v-for="link in companyAndLegalLinks" :key="link.name">
              <RouterLink 
                :to="link.to"
                class="text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:underline underline-offset-4 font-medium transition-colors duration-150"
              >
                {{ link.name }}
              </RouterLink>
            </li>
          </ul>
        </div>

      </div>

      <!-- Sleek Bottom Copyright & Platform Status Bar -->
      <div class="border-t border-slate-200/80 dark:border-slate-800 mt-8 pt-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400 font-medium">
        <p>{{ t('footer.copyright', '© 2026 BetLink Real Estate Platform. All rights reserved.') }}</p>
        
        <!-- Platform Operational Status Badge -->
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[11px] font-semibold text-slate-700 dark:text-slate-300 shadow-2xs">
          <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse" aria-hidden="true"></span>
          <span>{{ t('footer.platform_operational', 'Platform Operational') }}</span>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed, defineComponent, h } from 'vue'
import { RouterLink } from 'vue-router'
import { useLanguage } from '@/composables/useLanguage'
import { useAuthStore } from '@/stores/auth'

const { t } = useLanguage()
const authStore = useAuthStore()

const FacebookIcon = defineComponent({ render: () => h('svg', { viewBox: '0 0 24 24', fill: 'currentColor' }, [h('path', { d: 'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z' })]) })
const TwitterIcon  = defineComponent({ render: () => h('svg', { viewBox: '0 0 24 24', fill: 'currentColor' }, [h('path', { d: 'M22 4.01c-1 .49-1.98.689-3 .99-1.121-1.265-2.783-1.335-4.38-.737S11.977 6.323 12 8v1c-3.245.083-6.135-1.395-8-4 0 0-4.182 7.433 4 11-1.872 1.247-3.739 2.088-6 2 3.308 1.803 6.913 2.423 10.034 1.517 3.58-1.04 6.522-3.723 7.651-7.742a13.84 13.84 0 00.497-3.753C20.18 7.773 21.692 5.25 22 4.009z' })]) })
const InstagramIcon= defineComponent({ render: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [h('rect', { x:'2', y:'2', width:'20', height:'20', rx:'5', ry:'5' }), h('path', { d:'M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z' }), h('line', { x1:'17.5', y1:'6.5', x2:'17.51', y2:'6.5' })]) })
const TelegramIcon = defineComponent({ render: () => h('svg', { viewBox: '0 0 24 24', fill: 'currentColor' }, [h('path', { d:'M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-1.97 9.289c-.145.658-.537.818-1.084.508l-3-2.21-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L7.16 14.215l-2.963-.924c-.643-.204-.657-.643.136-.953l11.57-4.461c.537-.194 1.006.131.66.371z' })]) })

const socials = [
  { name: 'Facebook',  href: 'https://facebook.com', icon: FacebookIcon },
  { name: 'Twitter',   href: 'https://twitter.com', icon: TwitterIcon },
  { name: 'Instagram', href: 'https://instagram.com', icon: InstagramIcon },
  { name: 'Telegram',  href: 'https://t.me', icon: TelegramIcon },
]

const exploreLinks = computed(() => [
  { name: t('nav.properties', 'Properties for Sale'), to: '/properties?listing_type=sale' },
  { name: t('hero.tab_rent', 'Properties for Rent'), to: '/properties?listing_type=rent' },
  { name: t('nav.short_stays', 'Short Stay Rentals'), to: '/properties?listing_type=short_rent' },
  { name: t('nav.agents', 'Verified Agents'), to: '/agents' },
  { name: t('home.popular_locations_title', 'All Locations'), to: '/locations' },
])

const ownerLinks = computed(() => {
  const role = authStore.primaryRole?.toLowerCase()
  if (role === 'admin') {
    return [
      { name: t('nav.list_property', 'Manage Properties'), to: '/admin/properties' },
      { name: t('nav.dashboard', 'Admin Dashboard'), to: '/admin/dashboard' },
      { name: t('dashboard.verifications', 'Verifications'), to: '/admin/verifications' },
      { name: t('footer.pricing_plans', 'Pricing & Plans'), to: '/pricing' },
    ]
  }
  return [
    { name: t('nav.list_property', 'List a Property'), to: '/owner/properties/create' },
    { name: t('nav.dashboard', 'Owner Dashboard'), to: '/owner/dashboard' },
    { name: t('footer.get_verified', 'Get Verified'), to: '/owner/verification' },
    { name: t('footer.pricing_plans', 'Pricing & Plans'), to: '/pricing' },
  ]
})

const companyAndLegalLinks = computed(() => [
  { name: t('nav.about', 'About BetLink'), to: '/about' },
  { name: t('nav.blog', 'Real Estate Blog'), to: '/blog' },
  { name: t('nav.contact', 'Contact Support'), to: '/contact' },
  { name: t('nav.faqs', 'FAQs'), to: '/faqs' },
  { name: t('footer.privacy_policy', 'Privacy Policy'), to: '/privacy' },
  { name: t('footer.terms_of_service', 'Terms of Service'), to: '/terms' },
])
</script>
