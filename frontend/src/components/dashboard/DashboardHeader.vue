<template>
  <header class="sticky top-0 z-30 h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-6 shadow-sm">
    <!-- Left Section -->
    <div class="flex items-center gap-4 flex-1 min-w-0">
      <!-- Mobile Menu Toggle -->
      <button 
        @click="$emit('toggle-sidebar')"
        class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors"
        aria-label="Toggle sidebar"
      >
        <DashboardIcon name="menu" class="w-6 h-6 text-gray-700" />
      </button>

      <!-- Page Title -->
      <div class="hidden sm:block min-w-0">
        <h1 class="text-lg font-semibold text-gray-900 truncate">{{ title }}</h1>
        <p v-if="subtitle" class="text-xs text-gray-500 truncate">{{ subtitle }}</p>
      </div>

      <!-- Global Search Bar -->
      <div class="flex-1 max-w-xl mx-4 hidden md:block">
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <DashboardIcon name="search" class="w-5 h-5 text-gray-400" />
          </div>
          <input
            v-model="localSearchQuery"
            @input="handleSearchInput"
            @keydown.enter="handleSearchEnter"
            type="search"
            placeholder="Search properties, users, appointments..."
            class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          />
          <!-- Clear button -->
          <button
            v-if="localSearchQuery"
            @click="clearSearch"
            class="absolute inset-y-0 right-0 pr-3 flex items-center"
          >
            <DashboardIcon name="close" class="w-4 h-4 text-gray-400 hover:text-gray-600" />
          </button>
        </div>

        <!-- Search Results Dropdown -->
        <transition name="dropdown">
          <div
            v-if="showSearchResults && localSearchQuery"
            v-click-outside="closeSearchResults"
            class="absolute mt-2 w-full max-w-xl bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden"
          >
            <div class="max-h-96 overflow-y-auto">
              <div v-if="isSearching" class="px-4 py-8 text-center text-gray-500 text-sm">
                Searching listings...
              </div>
              <div v-else-if="liveSearchResults.length === 0" class="px-4 py-8 text-center text-gray-500 text-sm">
                No properties found for "{{ localSearchQuery }}"
              </div>
              <div v-else>
                <button
                  v-for="result in liveSearchResults"
                  :key="`${result.type}-${result.id}`"
                  @click="handleResultClick(result)"
                  class="w-full px-4 py-3 hover:bg-gray-50 transition-colors text-left border-b border-gray-100 last:border-b-0 flex items-center gap-3"
                >
                  <div :class="getResultIconBg(result.type)" class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0">
                    <DashboardIcon
                      :name="getResultIcon(result.type)"
                      :class="getResultIconColor(result.type)"
                      class="w-5 h-5"
                    />
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ result.title }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ result.subtitle }}</p>
                  </div>
                  <span :class="getResultBadgeColor(result.type)" class="text-xs font-medium px-2 py-1 rounded">
                    {{ result.type }}
                  </span>
                </button>
              </div>
            </div>
          </div>
        </transition>
      </div>
    </div>

    <!-- Right Section -->
    <div class="flex items-center gap-3">
      <!-- Notifications -->
      <div class="relative">
        <button 
          @click="toggleNotifications"
          class="relative p-2 rounded-lg hover:bg-gray-100 transition-colors"
          aria-label="Notifications"
        >
          <DashboardIcon name="bell" class="w-6 h-6 text-gray-700" />
          <span v-if="unreadCount > 0" class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
            {{ unreadCount > 9 ? '9+' : unreadCount }}
          </span>
        </button>

        <!-- Notifications Dropdown -->
        <transition name="dropdown">
          <div 
            v-if="showNotifications"
            v-click-outside="closeNotifications"
            class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden"
          >
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-gray-50">
              <h3 class="font-semibold text-gray-900">Notifications</h3>
              <button class="text-xs text-blue-600 hover:text-blue-700 font-medium">Mark all read</button>
            </div>
            <div class="max-h-96 overflow-y-auto">
              <div v-if="notifications.length === 0" class="px-4 py-8 text-center text-gray-500 text-sm">
                No new notifications
              </div>
              <button
                v-for="notification in notifications"
                :key="notification.id"
                @click="handleNotificationClick(notification)"
                class="w-full px-4 py-3 hover:bg-gray-50 transition-colors text-left border-b border-gray-100 last:border-b-0"
                :class="{ 'bg-blue-50': !notification.read }"
              >
                <div class="flex items-start gap-3">
                  <div :class="getNotificationIconBg(notification.type)" class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0">
                    <DashboardIcon 
                      :name="getNotificationIconName(notification.type)" 
                      :class="getNotificationIconColor(notification.type)" 
                      class="w-5 h-5"
                    />
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">{{ notification.title }}</p>
                    <p class="text-xs text-gray-600 mt-0.5 line-clamp-2">{{ notification.message }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ notification.time }}</p>
                  </div>
                </div>
              </button>
            </div>
            <div class="px-4 py-3 border-t border-gray-200 text-center bg-gray-50">
              <button class="text-sm text-blue-600 hover:text-blue-700 font-medium">View all notifications</button>
            </div>
          </div>
        </transition>
      </div>

      <!-- User Menu -->
      <div class="relative">
        <button 
          @click="toggleUserMenu"
          class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-gray-100 transition-colors"
          aria-label="User menu"
        >
          <img 
            :src="user.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name)" 
            :alt="user.name"
            class="w-8 h-8 rounded-full object-cover ring-2 ring-gray-200"
          />
          <div class="hidden md:block text-left">
            <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
            <p class="text-xs text-gray-500">{{ getRoleLabel(user.role) }}</p>
          </div>
          <DashboardIcon name="chevron-down" class="w-4 h-4 text-gray-500 hidden md:block" />
        </button>

        <!-- User Dropdown -->
        <transition name="dropdown">
          <div 
            v-if="showUserMenu"
            v-click-outside="closeUserMenu"
            class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden"
          >
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
              <p class="font-semibold text-gray-900 truncate">{{ user.name }}</p>
              <p class="text-xs text-gray-600 truncate">{{ user.email }}</p>
            </div>
            <div class="py-2">
              <RouterLink 
                :to="getProfilePath()"
                class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 text-gray-700 hover:text-gray-900 transition-colors"
              >
                <DashboardIcon name="profile" class="w-4 h-4" />
                <span class="text-sm">Profile</span>
              </RouterLink>
              <RouterLink 
                :to="getSettingsPath()"
                class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 text-gray-700 hover:text-gray-900 transition-colors"
              >
                <DashboardIcon name="settings" class="w-4 h-4" />
                <span class="text-sm">Settings</span>
              </RouterLink>
            </div>
            <div class="border-t border-gray-200 py-2">
              <button 
                @click="handleLogout"
                class="w-full flex items-center gap-3 px-4 py-2 hover:bg-red-50 text-red-600 hover:text-red-700 transition-colors"
              >
                <DashboardIcon name="logout" class="w-4 h-4" />
                <span class="text-sm font-medium">Logout</span>
              </button>
            </div>
          </div>
        </transition>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import DashboardIcon from './DashboardIcon.vue'
import { searchService } from '../../services/searchService'
import { useNotificationStore } from '../../stores/notificationStore'

const router = useRouter()

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  user: {
    type: Object,
    required: true
  },
  searchQuery: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['toggle-sidebar', 'logout', 'search'])

const showNotifications = ref(false)
const showUserMenu = ref(false)
const localSearchQuery = ref(props.searchQuery)
const showSearchResults = ref(false)
const isSearching = ref(false)
const liveSearchResults = ref([])
let searchDebounce = null

function handleSearchInput() {
  showSearchResults.value = true
  emit('search', localSearchQuery.value)

  clearTimeout(searchDebounce)
  const q = localSearchQuery.value.trim()
  if (!q) {
    liveSearchResults.value = []
    return
  }

  searchDebounce = setTimeout(async () => {
    isSearching.value = true
    try {
      const res = await searchService.suggestions(q)
      const list = res.data?.data || res.data || []
      liveSearchResults.value = list.map(item => ({
        id: item.id,
        type: 'property',
        title: item.title,
        subtitle: `${item.type || 'Property'} • ETB ${Number(item.price || 0).toLocaleString()}`,
        slug: item.slug || item.id,
      }))
    } catch {
      liveSearchResults.value = []
    } finally {
      isSearching.value = false
    }
  }, 250)
}

function handleSearchEnter() {
  if (localSearchQuery.value.trim()) {
    closeSearchResults()
    router.push({ path: '/properties', query: { q: localSearchQuery.value.trim() } })
  }
}

function clearSearch() {
  localSearchQuery.value = ''
  liveSearchResults.value = []
  showSearchResults.value = false
  emit('search', '')
}

function closeSearchResults() {
  showSearchResults.value = false
}

function handleResultClick(result) {
  closeSearchResults()
  if (result.slug) {
    router.push(`/properties/${result.slug}`)
  }
}

function getResultIcon(type) {
  const icons = {
    property: 'building',
    user: 'profile',
    appointment: 'calendar'
  }
  return icons[type] || 'search'
}

function getResultIconBg(type) {
  const bgs = {
    property: 'bg-blue-100',
    user: 'bg-green-100',
    appointment: 'bg-purple-100'
  }
  return bgs[type] || 'bg-gray-100'
}

function getResultIconColor(type) {
  const colors = {
    property: 'text-blue-600',
    user: 'text-green-600',
    appointment: 'text-purple-600'
  }
  return colors[type] || 'text-gray-600'
}

function getResultBadgeColor(type) {
  const colors = {
    property: 'bg-blue-100 text-blue-700',
    user: 'bg-green-100 text-green-700',
    appointment: 'bg-purple-100 text-purple-700'
  }
  return colors[type] || 'bg-gray-100 text-gray-700'
}

// Dynamic notifications from notificationStore
const notificationStore = useNotificationStore()
const notifications = computed(() => notificationStore.notifications)
const unreadCount = computed(() => notificationStore.unreadCount)

onMounted(() => {
  notificationStore.fetchNotifications()
})

function toggleNotifications() {
  showNotifications.value = !showNotifications.value
  showUserMenu.value = false
}

function toggleUserMenu() {
  showUserMenu.value = !showUserMenu.value
  showNotifications.value = false
}

function closeNotifications() {
  showNotifications.value = false
}

function closeUserMenu() {
  showUserMenu.value = false
}

function handleNotificationClick(notification) {
  notification.read = true
  // TODO: Navigate to notification target
  console.log('Notification clicked:', notification)
}

function getRoleLabel(role) {
  const labels = {
    admin: 'Administrator',
    owner: 'Property Owner',
    buyer: 'Property Buyer',
    agent: 'Real Estate Agent'
  }
  return labels[role] || role
}

function getProfilePath() {
  return `/${props.user.role}/profile`
}

function getSettingsPath() {
  return `/${props.user.role}/settings`
}

function handleLogout() {
  emit('logout')
}

function getNotificationIconName(type) {
  const icons = {
    success: 'check',
    info: 'info',
    appointment: 'calendar',
    warning: 'alert',
  }
  return icons[type] || 'info'
}

function getNotificationIconBg(type) {
  const bgs = {
    success: 'bg-green-100',
    info: 'bg-blue-100',
    appointment: 'bg-purple-100',
    warning: 'bg-orange-100',
  }
  return bgs[type] || 'bg-gray-100'
}

function getNotificationIconColor(type) {
  const colors = {
    success: 'text-green-600',
    info: 'text-blue-600',
    appointment: 'text-purple-600',
    warning: 'text-orange-600',
  }
  return colors[type] || 'text-gray-600'
}

// Click outside directive
const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = (event) => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value()
      }
    }
    document.addEventListener('click', el.clickOutsideEvent)
  },
  unmounted(el) {
    document.removeEventListener('click', el.clickOutsideEvent)
  }
}
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
