<template>
  <div class="flex items-center gap-2 text-xs sm:text-sm select-none font-medium">
    <RouterLink
      to="/"
      class="text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium transition-colors"
    >
      {{ t('nav.home', 'Home') }}
    </RouterLink>
    <span class="text-slate-300 dark:text-slate-600">/</span>
    <span class="text-slate-700 dark:text-slate-200 font-semibold">{{ currentPageName }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import { useAuth } from '../../composables/useAuth'
import { useLanguage } from '../../composables/useLanguage'

const route = useRoute()
const { user } = useAuth()
const { t } = useLanguage()

const segmentMap = {
  users: 'user_management_title',
  management: 'user_management_title',
  appointments: 'appointments',
  reports: 'reports',
  verifications: 'verifications',
  analytics: 'analytics',
  settings: 'settings',
  profile: 'profile',
  messages: 'direct_messages',
  favorites: 'favorites',
  leads: 'leads',
  bookings: 'bookings',
  reviews: 'reviews',
}

const currentPageName = computed(() => {
  const path = route.path
  const segments = path.split('/').filter(Boolean)
  
  if (segments.length === 0) return t('nav.home', 'Home')
  
  const lastSegment = segments[segments.length - 1]
  const isBuyer = (user.value?.role || '').toLowerCase().includes('buyer') || path.startsWith('/buyer')

  if (lastSegment === 'dashboard') {
    return isBuyer ? t('buyer_dashboard', 'Buyer Dashboard') : t('dashboard', 'Dashboard')
  }

  if (lastSegment === 'properties') {
    return t('properties', 'Properties')
  }
  
  // 1. Check segment translation map
  if (segmentMap[lastSegment]) {
    const key = segmentMap[lastSegment]
    const translation = t(key)
    if (translation && translation !== key) return translation
  }

  // 2. Direct key translation
  const direct = t(lastSegment)
  if (direct && direct !== lastSegment) return direct

  // 3. Fallback to capitalized segment
  return lastSegment.charAt(0).toUpperCase() + lastSegment.slice(1).replace(/-/g, ' ')
})
</script>
