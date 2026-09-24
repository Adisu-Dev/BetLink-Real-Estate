<template>
  <div class="flex h-screen overflow-hidden bg-gray-100">

    <!-- ── SIDEBAR ─────────────────────────────────────────────────── -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-50 flex flex-col bg-[#1a2035] text-white transition-all duration-300 select-none',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        'w-56'
      ]"
    >
      <!-- Logo -->
      <div class="flex items-center gap-3 px-5 py-4 border-b border-white/10">
        <div class="w-9 h-9 rounded-lg bg-emerald-500 flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
        </div>
        <div>
          <p class="text-base font-bold text-white leading-none">BetLink</p>
          <p class="text-[10px] text-gray-400 leading-none mt-0.5">{{ roleLabel }}</p>
        </div>
      </div>

      <!-- Nav - scrollable -->
      <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5 scrollbar-hide">
        <template v-for="(section, si) in navItems" :key="si">
          <p v-if="section.title" class="px-3 pt-4 pb-1 text-[10px] font-semibold text-gray-500 uppercase tracking-widest">
            {{ section.title }}
          </p>

          <template v-for="item in section.items" :key="item.label">
            <!-- Expandable group -->
            <template v-if="item.children">
              <button
                @click="toggleGroup(item.label)"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors"
                :class="anyChildActive(item.children)
                  ? 'bg-white/10 text-white'
                  : 'text-gray-400 hover:bg-white/10 hover:text-white'"
              >
                <span class="w-4 h-4 flex-shrink-0" v-html="item.icon"></span>
                <span class="flex-1 text-left font-medium">{{ item.label }}</span>
                <svg
                  class="w-3 h-3 transition-transform duration-200 flex-shrink-0"
                  :class="openGroups.includes(item.label) ? 'rotate-180' : ''"
                  fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>
              <div v-show="openGroups.includes(item.label)" class="ml-4 mt-0.5 space-y-0.5">
                <RouterLink
                  v-for="child in item.children"
                  :key="child.path"
                  :to="child.path"
                  class="flex items-center gap-2 pl-4 pr-3 py-1.5 rounded-lg text-xs transition-colors"
                  :class="isActive(child.path)
                    ? 'bg-emerald-600 text-white font-semibold'
                    : 'text-gray-400 hover:bg-white/10 hover:text-white'"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-current flex-shrink-0"></span>
                  {{ child.label }}
                </RouterLink>
              </div>
            </template>

            <!-- Plain link -->
            <RouterLink
              v-else
              :to="item.path"
              class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors font-medium"
              :class="isActive(item.path)
                ? 'bg-emerald-600 text-white shadow-sm'
                : 'text-gray-400 hover:bg-white/10 hover:text-white'"
            >
              <span class="w-4 h-4 flex-shrink-0" v-html="item.icon"></span>
              <span class="flex-1">{{ item.label }}</span>
              <span
                v-if="item.badge"
                class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none"
              >{{ item.badge }}</span>
            </RouterLink>
          </template>
        </template>
      </nav>

      <!-- Logout -->
      <div class="p-3 border-t border-white/10 flex-shrink-0">
        <button
          @click="showLogoutModal = true"
          class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-400 hover:bg-red-600 hover:text-white transition-all text-sm font-semibold"
        >
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          LOG OUT
        </button>
      </div>
    </aside>

    <!-- Mobile overlay -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-40 bg-black/50 lg:hidden"
      @click="sidebarOpen = false"
    />

    <!-- ── MAIN AREA ────────────────────────────────────────────────── -->
    <div class="flex flex-col flex-1 min-w-0 lg:ml-56 h-screen">

      <!-- Header -->
      <header class="flex-shrink-0 h-14 bg-white border-b border-gray-200 flex items-center px-4 lg:px-6 gap-4 shadow-sm z-30">
        <!-- Hamburger -->
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-1.5 rounded hover:bg-gray-100">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>

        <!-- System title (center) -->
        <div class="flex-1 text-center hidden md:block">
          <p class="text-sm font-bold text-gray-800">BetLink Property Management System</p>
          <p class="text-[10px] text-gray-500">Ethiopia Real Estate Marketplace · {{ currentYear }}</p>
        </div>

        <!-- Search -->
        <div class="relative flex-1 max-w-xs">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input
            v-model="searchQ"
            type="text"
            placeholder="Global Search..."
            class="w-full pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-gray-50"
          />
        </div>

        <!-- Notification bell -->
        <div class="relative">
          <button @click="bellOpen = !bellOpen" class="relative p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span v-if="notifCount > 0" class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">
              {{ notifCount }}
            </span>
          </button>
          <!-- Bell dropdown -->
          <div v-if="bellOpen" class="absolute right-0 top-10 w-72 bg-white border border-gray-200 rounded-lg shadow-xl z-50">
            <div class="px-4 py-2.5 border-b border-gray-100 flex items-center justify-between">
              <span class="text-sm font-semibold text-gray-800">Notifications</span>
              <button @click="notifCount = 0; bellOpen = false" class="text-[11px] text-emerald-600 font-medium hover:underline">Mark all read</button>
            </div>
            <div class="max-h-72 overflow-y-auto divide-y divide-gray-100">
              <div v-for="n in notifications" :key="n.id" class="px-4 py-3 hover:bg-gray-50 cursor-pointer" :class="{ 'bg-emerald-50': !n.read }">
                <p class="text-xs font-semibold text-gray-800">{{ n.title }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5">{{ n.body }}</p>
                <p class="text-[10px] text-gray-400 mt-1">{{ n.time }}</p>
              </div>
            </div>
            <div class="px-4 py-2 border-t border-gray-100 text-center">
              <button class="text-[11px] text-emerald-600 font-medium hover:underline">View All Notifications</button>
            </div>
          </div>
        </div>

        <!-- User chip -->
        <div class="relative">
          <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 pl-2 pr-3 py-1 rounded-lg hover:bg-gray-100 transition-colors border border-gray-200">
            <img
              :src="currentUser.avatar_url || currentUser.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(currentUser.name || 'User')}&background=1e293b&color=fff&size=64`"
              class="w-7 h-7 rounded-full object-cover"
              :alt="currentUser.name"
            />
            <div class="hidden sm:block text-left">
              <p class="text-xs font-semibold text-gray-800 leading-none">{{ currentUser.name }}</p>
              <p class="text-[10px] text-gray-500 leading-none mt-0.5">{{ roleLabel }}</p>
            </div>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <!-- User dropdown -->
          <div v-if="userMenuOpen" class="absolute right-0 top-10 w-44 bg-white border border-gray-200 rounded-lg shadow-xl z-50 overflow-hidden">
            <RouterLink :to="`/${currentUser.role}/profile`" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
              <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              Profile
            </RouterLink>
            <RouterLink :to="`/${currentUser.role}/settings`" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
              <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              Settings
            </RouterLink>
            <div class="border-t border-gray-100">
              <button @click="showLogoutModal = true; userMenuOpen = false" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- Page area -->
      <main class="flex-1 overflow-y-auto">
        <!-- Breadcrumb bar -->
        <div class="bg-white border-b border-gray-200 px-6 py-2 flex items-center gap-2 text-xs text-gray-500">
          <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          <span class="text-gray-400">/</span>
          <span class="font-medium text-gray-700">{{ pageTitle }}</span>
          <span v-if="pageSubtitle" class="text-gray-400">— {{ pageSubtitle }}</span>
        </div>

        <div class="p-4 lg:p-6">
          <router-view v-slot="{ Component }">
            <transition name="fade" mode="out-in">
              <component :is="Component" :key="$route.fullPath" />
            </transition>
          </router-view>
        </div>
      </main>

      <!-- Footer -->
      <footer class="flex-shrink-0 h-8 bg-[#1a2035] flex items-center justify-between px-6">
        <span class="text-[11px] text-gray-400">BetLink v1.0.0</span>
        <span class="text-[11px] text-gray-400">✦ Developed by BetLink Team ✦</span>
        <span class="text-[11px] text-gray-400">Ethiopia Real Estate © {{ currentYear }}</span>
      </footer>
    </div>

    <!-- Logout Modal -->
    <Teleport to="body">
      <div v-if="showLogoutModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black/60">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden">
          <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
              </svg>
            </div>
            <div>
              <p class="font-semibold text-gray-900">Logout of BetLink?</p>
              <p class="text-xs text-gray-500">Are you sure you want to logout?</p>
            </div>
          </div>
          <div class="px-6 py-4 text-sm text-gray-600">
            You will be redirected to the login page. Any unsaved changes will be lost.
          </div>
          <div class="px-6 pb-4 flex gap-3 justify-end">
            <button
              @click="showLogoutModal = false"
              class="px-5 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 font-medium"
            >
              Cancel
            </button>
            <button
              @click="doLogout"
              class="px-5 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'

const router = useRouter()
const route  = useRoute()

// ── User ──────────────────────────────────────────────────────────────
const currentUser = computed(() => {
  try { return JSON.parse(localStorage.getItem('betlink_user') || '{}') }
  catch { return {} }
})

const roleLabel = computed(() => ({
  admin: 'Administrator', owner: 'Property Owner',
  buyer: 'Property Seeker', agent: 'Real Estate Agent'
}[currentUser.value.role] || 'User'))

const currentYear = new Date().getFullYear()

// ── UI state ──────────────────────────────────────────────────────────
const sidebarOpen   = ref(false)
const bellOpen      = ref(false)
const userMenuOpen  = ref(false)
const showLogoutModal = ref(false)
const searchQ       = ref('')
const openGroups    = ref([])
const notifCount    = ref(3)

// ── Page meta ─────────────────────────────────────────────────────────
const pageTitle    = computed(() => route.meta.title    || 'Dashboard')
const pageSubtitle = computed(() => route.meta.subtitle || '')

// ── Notifications ─────────────────────────────────────────────────────
const notifications = ref([
  { id: 1, title: 'Property Approved', body: 'Listing "Bole Apartment" is now live.', time: '5 min ago', read: false },
  { id: 2, title: 'New Appointment', body: 'Viewing request for CMC Villa at 10 AM.', time: '1 hr ago', read: false },
  { id: 3, title: 'New Message', body: 'Sara Mohammed sent you a message.', time: '3 hrs ago', read: true },
])

// ── Close dropdowns on outside click ─────────────────────────────────
function handleOutsideClick(e) {
  if (!e.target.closest('[data-dropdown]')) {
    bellOpen.value     = false
    userMenuOpen.value = false
  }
}
onMounted(() => document.addEventListener('click', handleOutsideClick))
onBeforeUnmount(() => document.removeEventListener('click', handleOutsideClick))

// ── Sidebar helpers ───────────────────────────────────────────────────
function isActive(path) {
  return route.path === path || route.path.startsWith(path + '/')
}
function anyChildActive(children) {
  return children.some(c => isActive(c.path))
}
function toggleGroup(label) {
  const i = openGroups.value.indexOf(label)
  if (i > -1) openGroups.value.splice(i, 1)
  else openGroups.value.push(label)
}

// ── Logout ─────────────────────────────────────────────────────────────
function doLogout() {
  localStorage.removeItem('betlink_auth_token')
  localStorage.removeItem('betlink_user')
  showLogoutModal.value = false
  router.push('/login')
}

// ── Nav config ─────────────────────────────────────────────────────────
const ICONS = {
  home:     `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>`,
  users:    `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>`,
  building: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>`,
  calendar: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`,
  chart:    `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>`,
  settings: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`,
  alert:    `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`,
  heart:    `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>`,
  message:  `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>`,
  star:     `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>`,
  plus:     `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>`,
  profile:  `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>`,
  shield:   `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>`,
  search:   `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>`,
  bookmark: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>`,
  blog:     `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>`,
  logs:     `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>`,
  payment:  `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>`,
}

const navItems = computed(() => {
  const role = currentUser.value.role
  const defs = {
    admin: [
      { items: [
        { label: 'Control Center', path: '/admin/dashboard', icon: ICONS.home },
      ]},
      { title: 'Management', items: [
        { label: 'Manage Users',   icon: ICONS.users, children: [
            { label: 'All Users',        path: '/admin/users' },
            { label: 'Verification Req', path: '/admin/users/verification' },
          ]
        },
        { label: 'Properties', icon: ICONS.building, children: [
            { label: 'All Properties',   path: '/admin/properties' },
            { label: 'Pending Approval', path: '/admin/properties/pending' },
          ]
        },
        { label: 'Appointments', path: '/admin/appointments', icon: ICONS.calendar },
        { label: 'Payments',     path: '/admin/payments',     icon: ICONS.payment },
        { label: 'Subscriptions',path: '/admin/subscriptions',icon: ICONS.shield },
      ]},
      { title: 'Content', items: [
        { label: 'Reports',   path: '/admin/reports',   icon: ICONS.alert,   badge: '3' },
        { label: 'Reviews',   path: '/admin/reviews',   icon: ICONS.star },
        { label: 'Messages',  path: '/admin/messages',  icon: ICONS.message },
        { label: 'Blog',      path: '/admin/blogs',     icon: ICONS.blog },
      ]},
      { title: 'System', items: [
        { label: 'Analytics & Reports', path: '/admin/analytics', icon: ICONS.chart },
        { label: 'Activity Logs',       path: '/admin/logs',      icon: ICONS.logs },
        { label: 'Security Settings',   path: '/admin/settings',  icon: ICONS.settings },
      ]},
    ],
    owner: [
      { items: [
        { label: 'Dashboard', path: '/owner/dashboard', icon: ICONS.home },
      ]},
      { title: 'Properties', items: [
        { label: 'My Properties', path: '/owner/properties',        icon: ICONS.building },
        { label: 'Add Property',  path: '/owner/properties/create', icon: ICONS.plus },
      ]},
      { title: 'Activity', items: [
        { label: 'Appointments', path: '/owner/appointments', icon: ICONS.calendar, badge: '2' },
        { label: 'Inquiries',    path: '/owner/inquiries',    icon: ICONS.message },
        { label: 'Messages',     path: '/owner/messages',     icon: ICONS.message },
      ]},
      { title: 'Account', items: [
        { label: 'Analytics',    path: '/owner/analytics',   icon: ICONS.chart },
        { label: 'Verification', path: '/owner/verification',icon: ICONS.shield },
        { label: 'Profile',      path: '/owner/profile',     icon: ICONS.profile },
        { label: 'Settings',     path: '/owner/settings',    icon: ICONS.settings },
      ]},
    ],
    buyer: [
      { items: [
        { label: 'Dashboard', path: '/buyer/dashboard', icon: ICONS.home },
      ]},
      { title: 'Explore', items: [
        { label: 'Find Properties',  path: '/properties',          icon: ICONS.search },
        { label: 'Saved Properties', path: '/buyer/favorites',     icon: ICONS.heart },
        { label: 'Saved Searches',   path: '/buyer/saved-searches',icon: ICONS.bookmark },
      ]},
      { title: 'Activity', items: [
        { label: 'Appointments', path: '/buyer/appointments', icon: ICONS.calendar },
        { label: 'Messages',     path: '/buyer/messages',     icon: ICONS.message },
        { label: 'Bookings',     path: '/buyer/bookings',     icon: ICONS.building },
      ]},
      { title: 'Account', items: [
        { label: 'Reviews',  path: '/buyer/reviews',  icon: ICONS.star },
        { label: 'Profile',  path: '/buyer/profile',  icon: ICONS.profile },
        { label: 'Settings', path: '/buyer/settings', icon: ICONS.settings },
      ]},
    ],
    agent: [
      { items: [
        { label: 'Dashboard', path: '/agent/dashboard', icon: ICONS.home },
      ]},
      { title: 'Listings', items: [
        { label: 'My Listings',  path: '/agent/listings',        icon: ICONS.building },
        { label: 'Add Listing',  path: '/agent/listings/create', icon: ICONS.plus },
      ]},
      { title: 'Clients', items: [
        { label: 'Leads',        path: '/agent/leads',        icon: ICONS.users },
        { label: 'Appointments', path: '/agent/appointments', icon: ICONS.calendar },
        { label: 'Messages',     path: '/agent/messages',     icon: ICONS.message },
      ]},
      { title: 'Performance', items: [
        { label: 'Analytics',    path: '/agent/analytics',    icon: ICONS.chart },
        { label: 'Reviews',      path: '/agent/reviews',      icon: ICONS.star },
        { label: 'Verification', path: '/agent/verification', icon: ICONS.shield },
      ]},
      { title: 'Account', items: [
        { label: 'Profile',  path: '/agent/profile',  icon: ICONS.profile },
        { label: 'Settings', path: '/agent/settings', icon: ICONS.settings },
      ]},
    ],
  }
  return defs[role] || defs.buyer
})
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none }
.fade-enter-active, .fade-leave-active { transition: opacity 0.12s ease }
.fade-enter-from, .fade-leave-to { opacity: 0 }
</style>
