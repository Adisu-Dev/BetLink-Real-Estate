<template>
  <!-- Mobile Backdrop Overlay -->
  <div 
    v-if="isMobileOpen"
    class="fixed inset-0 bg-black/60 backdrop-blur-xs z-30 lg:hidden"
    @click="$emit('close')"
  />

  <!-- Clean Unified Sidebar (Light: bg-white / Dark: bg-slate-900) -->
  <aside
    :class="[
      'w-64 bg-white dark:bg-slate-900 border-r border-slate-200/80 dark:border-slate-800 text-slate-700 dark:text-slate-300 flex flex-col justify-between shrink-0 z-40 select-none transition-all duration-300 ease-in-out',
      'fixed inset-y-0 left-0 lg:static lg:translate-x-0',
      isMobileOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full'
    ]"
  >
    <!-- Brand Logo Header & Continuous Navigation List -->
    <div class="flex-1 flex flex-col overflow-y-auto">
      
      <!-- Brand Logo Header -->
      <div class="h-16 flex items-center px-6 border-b border-slate-100 dark:border-slate-800 flex-shrink-0">
        <RouterLink to="/" class="flex items-center gap-3 group">
          <div class="w-8 h-8 rounded-xl bg-slate-900 dark:bg-white flex items-center justify-center text-white dark:text-slate-900 font-black text-sm shadow-md shadow-slate-900/10 group-hover:scale-105 transition-transform flex-shrink-0">
            <span>B</span>
          </div>
          <div>
            <div class="text-sm font-black text-slate-900 dark:text-white leading-tight tracking-tight">BetLink</div>
            <div class="text-[10px] text-slate-400 dark:text-slate-500 font-medium leading-tight">Ethiopia Real Estate</div>
          </div>
        </RouterLink>
      </div>

      <!-- Unified Continuous Menu List -->
      <div class="p-4 flex-1 flex flex-col">
        <!-- Header Section Label -->
        <div class="px-3 mb-2.5">
          <p class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ userRoleLabel }}</p>
        </div>

        <!-- Continuous Navigation Links -->
        <nav class="flex flex-col gap-1">
          <RouterLink
            v-for="item in navItems" 
            :key="item.id"
            :to="item.route"
            :class="[
              'flex items-center gap-3.5 px-3.5 py-2.5 rounded-2xl font-semibold text-xs sm:text-sm transition-all duration-200',
              isActive(item.route)
                ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold border border-slate-200/80 dark:border-slate-700/80 shadow-xs'
                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white'
            ]"
            @click="$emit('close')"
          >
            <span 
              v-html="item.icon" 
              :class="[
                'w-4 h-4 flex-shrink-0 transition-colors',
                isActive(item.route) ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-500'
              ]"
            />
            <span class="flex-1 truncate">{{ item.label }}</span>
            <span 
              v-if="item.badge"
              class="ml-auto text-[10px] bg-rose-500 text-white px-2 py-0.5 rounded-full font-bold shadow-xs"
            >
              {{ item.badge }}
            </span>
          </RouterLink>
        </nav>
      </div>
    </div>

    <!-- Bottom Action: Logout -->
    <div class="p-4 pt-1">
      <button
        @click="showLogoutModal = true"
        class="w-full flex items-center gap-3.5 px-3.5 py-2.5 rounded-2xl text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-rose-50 dark:hover:bg-rose-950/30 hover:text-rose-600 dark:hover:text-rose-400 transition-all duration-200"
      >
        <svg class="w-4 h-4 text-slate-400 group-hover:text-rose-600 dark:group-hover:text-rose-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
        <span>{{ t('logout', 'Logout') }}</span>
      </button>
    </div>

    <!-- Logout Confirmation Modal -->
    <ConfirmModal
      :isOpen="showLogoutModal"
      title="Logout of BetLink?"
      message="Are you sure you want to logout?"
      confirmLabel="Logout"
      cancelLabel="Cancel"
      :danger="true"
      @confirm="handleLogoutConfirm"
      @cancel="showLogoutModal = false"
    />
  </aside>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import { useAuth } from '../../composables/useAuth'
import { useLanguage } from '../../composables/useLanguage'
import ConfirmModal from './ConfirmModal.vue'

const props = defineProps({
  isMobileOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close'])

const route = useRoute()
const { user, logout } = useAuth()
const { t } = useLanguage()
const showLogoutModal = ref(false)

const handleLogoutConfirm = async () => {
  showLogoutModal.value = false
  emit('close')
  await logout()
}

const userRoleLabel = computed(() => {
  const role = user.value?.role || 'admin'
  const roleLabels = {
    admin: t('administrator'),
    owner: t('property_owner'),
    buyer: t('buyer_renter'),
    agent: t('real_estate_agent')
  }
  return roleLabels[role] || role.toUpperCase()
})

const navItems = computed(() => {
  const role = user.value?.role || 'admin'
  
  const iconDashboard = '<svg fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>'
  const iconManagement = '<svg fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>'
  const iconProperties = '<svg fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 101.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>'
  const iconAppointments = '<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>'
  const iconReports = '<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>'
  const iconVerifications = '<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 10-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
  const iconAnalytics = '<svg fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>'
  const iconSettings = '<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.533 1.533 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>'

  if (role === 'admin') {
    return [
      { id: 'dashboard', label: t('dashboard'), route: '/admin/dashboard', icon: iconDashboard },
      { id: 'management', label: t('management'), route: '/admin/users', icon: iconManagement },
      { id: 'properties', label: t('properties'), route: '/admin/properties', icon: iconProperties },
      { id: 'appointments', label: t('appointments'), route: '/admin/appointments', icon: iconAppointments },
      { id: 'reports', label: t('reports'), route: '/admin/reports', icon: iconReports },
      { id: 'verifications', label: t('verifications'), route: '/admin/verifications', icon: iconVerifications },
      { id: 'settings', label: t('settings'), route: '/admin/settings', icon: iconSettings },
    ]
  }

  const roleSpecificItems = {
    owner: [
      { id: 'dashboard', label: t('dashboard'), route: '/owner/dashboard', icon: iconDashboard },
      { id: 'properties', label: t('properties'), route: '/owner/properties', icon: iconProperties },
      { id: 'appointments', label: t('appointments'), route: '/owner/appointments', icon: iconAppointments },
      { id: 'inbox', label: t('inquiries'), route: '/owner/inquiries', icon: iconManagement },
      { id: 'reports', label: t('reports', 'Reports'), route: '/owner/reports', icon: iconReports },
      { id: 'analytics', label: t('analytics'), route: '/owner/analytics', icon: iconAnalytics },
      { id: 'verification', label: t('verifications'), route: '/owner/verification', icon: iconVerifications },
      { id: 'settings', label: t('settings'), route: '/owner/settings', icon: iconSettings },
    ],
    agent: [
      { id: 'dashboard', label: t('dashboard'), route: '/agent/dashboard', icon: iconDashboard },
      { id: 'properties', label: t('properties'), route: '/agent/properties', icon: iconProperties },
      { id: 'leads', label: t('leads', 'Leads'), route: '/agent/leads', icon: iconManagement },
      { id: 'appointments', label: t('appointments'), route: '/agent/appointments', icon: iconAppointments },
      { id: 'reports', label: t('reports', 'Reports'), route: '/agent/reports', icon: iconReports },
      { id: 'analytics', label: t('analytics'), route: '/agent/analytics', icon: iconAnalytics },
      { id: 'verification', label: t('verifications'), route: '/agent/verification', icon: iconVerifications },
      { id: 'settings', label: t('settings'), route: '/agent/settings', icon: iconSettings },
    ],
    buyer: [
      { id: 'dashboard', label: t('dashboard'), route: '/buyer/dashboard', icon: iconDashboard },
      { id: 'properties', label: t('properties'), route: '/buyer/properties', icon: iconProperties },
      { id: 'favorites', label: t('favorites', 'Favorites'), route: '/buyer/favorites', icon: '<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>' },
      { id: 'appointments', label: t('appointments'), route: '/buyer/appointments', icon: iconAppointments },
      { id: 'messages', label: t('direct_messages'), route: '/buyer/messages', icon: iconManagement },
      { id: 'reports', label: t('reports', 'Reports'), route: '/buyer/reports', icon: iconReports },
      { id: 'settings', label: t('settings'), route: '/buyer/settings', icon: iconSettings },
    ]
  }

  return roleSpecificItems[role] || []
})

function isActive(compareRoute) {
  const currentPath = route.path.toLowerCase()
  const targetPath = compareRoute.toLowerCase()
  if (currentPath === targetPath) return true
  if (targetPath === '/admin/users' && (currentPath === '/admin/management' || currentPath.startsWith('/admin/users'))) return true
  if (targetPath === '/admin/management' && (currentPath === '/admin/users' || currentPath.startsWith('/admin/users'))) return true
  return targetPath !== '/admin/dashboard' && targetPath !== '/owner/dashboard' && targetPath !== '/buyer/dashboard' && targetPath !== '/agent/dashboard' && currentPath.startsWith(targetPath)
}

function handleLogout() {
  emit('logout-confirm')
  logout()
}
</script>
