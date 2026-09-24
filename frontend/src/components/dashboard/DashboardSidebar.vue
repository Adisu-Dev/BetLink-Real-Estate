<template>
  <aside 
    :class="[
      'fixed inset-y-0 left-0 z-40 bg-navy-950 text-white transition-transform duration-300 flex flex-col',
      isOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
      isCollapsed ? 'w-20' : 'w-64'
    ]"
  >
    <!-- Logo Section - Fixed at top -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-navy-800 flex-shrink-0">
      <RouterLink to="/" class="flex items-center gap-2.5">
        <div class="w-8 h-8 bg-betlink-green rounded-lg flex items-center justify-center flex-shrink-0">
          <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 text-white">
            <path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            <path d="M9 21V12h6v9" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
          </svg>
        </div>
        <span v-if="!isCollapsed" class="text-xl font-bold">Bet<span class="text-gray-400">Link</span></span>
      </RouterLink>
      
      <button 
        v-if="!isCollapsed"
        @click="toggleSidebar" 
        class="lg:hidden p-1.5 rounded hover:bg-navy-800 transition-colors"
      >
        <DashboardIcon name="close" class="w-5 h-5" />
      </button>
    </div>

    <!-- Navigation - Scrollable middle section -->
    <nav class="flex-1 overflow-y-auto py-4 px-2 scrollbar-thin scrollbar-thumb-navy-800">
      <div v-for="(section, index) in navigationItems" :key="index" class="mb-6">
        <p v-if="section.title && !isCollapsed" class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
          {{ section.title }}
        </p>
        
        <template v-for="item in section.items" :key="item.path || item.label">
          <!-- Parent item without children -->
          <RouterLink 
            v-if="!item.children"
            :to="item.path"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group mb-1"
            :class="isActiveRoute(item.path) 
              ? 'bg-active-blue text-white' 
              : 'text-gray-300 hover:bg-navy-800 hover:text-white'"
          >
            <DashboardIcon 
              :name="item.icon"
              class="w-5 h-5 flex-shrink-0" 
              :class="isActiveRoute(item.path) ? 'text-white' : 'text-gray-400 group-hover:text-white'"
            />
            <span v-if="!isCollapsed" class="font-medium text-sm">{{ item.label }}</span>
            <span 
              v-if="item.badge && !isCollapsed" 
              class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"
            >
              {{ item.badge }}
            </span>
          </RouterLink>

          <!-- Parent item with children -->
          <div v-else class="mb-1">
            <button
              @click="toggleSubmenu(item.label)"
              class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group"
              :class="isAnyChildActive(item.children) 
                ? 'bg-navy-800 text-white' 
                : 'text-gray-300 hover:bg-navy-800 hover:text-white'"
            >
              <DashboardIcon 
                :name="item.icon"
                class="w-5 h-5 flex-shrink-0" 
                :class="isAnyChildActive(item.children) ? 'text-white' : 'text-gray-400 group-hover:text-white'"
              />
              <span v-if="!isCollapsed" class="font-medium text-sm flex-1 text-left">{{ item.label }}</span>
              <DashboardIcon 
                v-if="!isCollapsed"
                name="chevron-down"
                class="w-4 h-4 transition-transform duration-200" 
                :class="openSubmenus.includes(item.label) ? 'rotate-180' : ''"
              />
            </button>

            <!-- Submenu -->
            <div 
              v-if="!isCollapsed"
              v-show="openSubmenus.includes(item.label)"
              class="ml-8 mt-1 space-y-1"
            >
              <RouterLink
                v-for="child in item.children"
                :key="child.path"
                :to="child.path"
                class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors"
                :class="isActiveRoute(child.path)
                  ? 'bg-active-blue text-white'
                  : 'text-gray-400 hover:bg-navy-800 hover:text-white'"
              >
                {{ child.label }}
              </RouterLink>
            </div>
          </div>
        </template>
      </div>
    </nav>

    <!-- Logout Button - Fixed at bottom -->
    <div class="p-4 border-t border-navy-800 flex-shrink-0">
      <button 
        @click="handleLogout"
        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-red-600 hover:text-white transition-all duration-200"
      >
        <DashboardIcon name="logout" class="w-5 h-5 flex-shrink-0" />
        <span v-if="!isCollapsed" class="font-medium text-sm">Logout</span>
      </button>
    </div>
  </aside>

  <!-- Overlay for mobile -->
  <div 
    v-if="isOpen"
    @click="toggleSidebar"
    class="fixed inset-0 bg-black/50 z-30 lg:hidden"
  ></div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import DashboardIcon from './DashboardIcon.vue'

const props = defineProps({
  role: {
    type: String,
    required: true
  },
  isOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['toggle', 'logout'])

const route = useRoute()
const isCollapsed = ref(false)
const openSubmenus = ref([])

// Navigation items based on role - using icon names instead of SVG strings
const navigationItems = computed(() => {
  const navConfig = {
    admin: [
      {
        items: [
          { label: 'Dashboard', path: '/admin/dashboard', icon: 'home' },
        ]
      },
      {
        title: 'Management',
        items: [
          { label: 'Users', path: '/admin/users', icon: 'users' },
          { label: 'Properties', path: '/admin/properties', icon: 'building' },
          { label: 'Appointments', path: '/admin/appointments', icon: 'calendar' },
          { label: 'Verifications', path: '/admin/verifications', icon: 'shield' },
        ]
      },
      {
        title: 'Content & Audit',
        items: [
          { label: 'Reports', path: '/admin/reports', icon: 'alert', badge: '3' },
          { label: 'Messages', path: '/admin/messages', icon: 'message' },
        ]
      },
      {
        title: 'System',
        items: [
          { label: 'System Settings', path: '/admin/settings', icon: 'settings' },
          { label: 'Profile', path: '/admin/profile', icon: 'profile' },
        ]
      }
    ],
    owner: [
      {
        items: [
          { label: 'Dashboard', path: '/owner/dashboard', icon: 'home' },
        ]
      },
      {
        title: 'Properties',
        items: [
          { label: 'My Properties', path: '/owner/properties', icon: 'building' },
          { label: 'Add Property', path: '/owner/properties/create', icon: 'plus' },
        ]
      },
      {
        title: 'Activity',
        items: [
          { label: 'Appointments', path: '/owner/appointments', icon: 'calendar', badge: '2' },
          { label: 'Inquiries', path: '/owner/inquiries', icon: 'inquiry' },
          { label: 'Messages', path: '/owner/messages', icon: 'message' },
          { label: 'Reports', path: '/owner/reports', icon: 'clipboard' },
        ]
      },
      {
        title: 'Account',
        items: [
          { label: 'Analytics', path: '/owner/analytics', icon: 'chart' },
          { label: 'Verification', path: '/owner/verification', icon: 'shield' },
          { label: 'Profile', path: '/owner/profile', icon: 'profile' },
          { label: 'Settings', path: '/owner/settings', icon: 'settings' },
        ]
      }
    ],
    buyer: [
      {
        items: [
          { label: 'Dashboard', path: '/buyer/dashboard', icon: 'home' },
        ]
      },
      {
        title: 'Search',
        items: [
          { label: 'Find Properties', path: '/properties', icon: 'search' },
          { label: 'Saved Properties', path: '/buyer/favorites', icon: 'heart' },
          { label: 'Saved Searches', path: '/buyer/saved-searches', icon: 'bookmark' },
        ]
      },
      {
        title: 'Activity',
        items: [
          { label: 'Appointments', path: '/buyer/appointments', icon: 'calendar' },
          { label: 'Messages', path: '/buyer/messages', icon: 'message' },
          { label: 'Short Stays', path: '/buyer/bookings', icon: 'booking' },
          { label: 'Reports', path: '/buyer/reports', icon: 'clipboard' },
        ]
      },
      {
        title: 'Account',
        items: [
          { label: 'Reviews', path: '/buyer/reviews', icon: 'star' },
          { label: 'Profile', path: '/buyer/profile', icon: 'profile' },
          { label: 'Settings', path: '/buyer/settings', icon: 'settings' },
        ]
      }
    ],
    agent: [
      {
        items: [
          { label: 'Dashboard', path: '/agent/dashboard', icon: 'home' },
        ]
      },
      {
        title: 'Listings',
        items: [
          { label: 'My Listings', path: '/agent/listings', icon: 'building' },
          { label: 'Add Property', path: '/agent/listings/create', icon: 'plus' },
        ]
      },
      {
        title: 'Clients',
        items: [
          { label: 'Leads / Inquiries', path: '/agent/leads', icon: 'leads' },
          { label: 'Appointments', path: '/agent/appointments', icon: 'calendar' },
          { label: 'Messages', path: '/agent/messages', icon: 'message' },
        ]
      },
      {
        title: 'Performance',
        items: [
          { label: 'Reports', path: '/agent/reports', icon: 'clipboard' },
          { label: 'Analytics', path: '/agent/analytics', icon: 'chart' },
          { label: 'Verification', path: '/agent/verification', icon: 'shield' },
          { label: 'Reviews', path: '/agent/reviews', icon: 'star' },
        ]
      },
      {
        title: 'Account',
        items: [
          { label: 'Profile', path: '/agent/profile', icon: 'profile' },
          { label: 'Settings', path: '/agent/settings', icon: 'settings' },
        ]
      }
    ]
  }

  return navConfig[props.role] || navConfig.buyer
})

function isActiveRoute(path) {
  return route.path === path || route.path.startsWith(path + '/')
}

function isAnyChildActive(children) {
  return children.some(child => isActiveRoute(child.path))
}

function toggleSubmenu(label) {
  const index = openSubmenus.value.indexOf(label)
  if (index > -1) {
    openSubmenus.value.splice(index, 1)
  } else {
    openSubmenus.value.push(label)
  }
}

function toggleSidebar() {
  emit('toggle')
}

function handleLogout() {
  emit('logout')
}
</script>

<style scoped>
.scrollbar-thin::-webkit-scrollbar {
  width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
  background: transparent;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
  background: #243b53;
  border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background: #334e68;
}
</style>
