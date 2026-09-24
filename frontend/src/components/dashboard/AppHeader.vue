<template>
  <header class="h-16 w-full sticky top-0 z-30 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 px-4 sm:px-6 lg:px-8 flex items-center justify-between flex-shrink-0 select-none transition-colors duration-200">
    
    <!-- Left: Mobile Toggle & Dynamic Localized Title/Greeting -->
    <div class="flex items-center gap-3 sm:gap-4 min-w-0">
      <!-- Mobile Sidebar Toggle -->
      <button
        @click="$emit('toggle-drawer')"
        class="lg:hidden p-2 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors flex-shrink-0 cursor-pointer"
        aria-label="Toggle navigation menu"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <!-- Left-Aligned Title & Greeting/Subtitle (Localized) -->
      <div class="min-w-0 text-left">
        <h1 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white tracking-tight truncate leading-tight">
          {{ roleTitle }}
        </h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 truncate leading-tight hidden sm:block mt-0.5 font-medium">
          Welcome back, {{ userDisplayName }}
        </p>
      </div>
    </div>

    <!-- Right Controls: Theme Switcher, Language Selector, Notifications, Messages, User Profile -->
    <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
      
      <!-- 1. Dark/Light Mode Theme Toggle Button -->
      <button 
        @click="toggleTheme" 
        type="button"
        class="p-2 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors relative group focus:outline-none cursor-pointer"
        :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
        aria-label="Toggle dark/light mode"
      >
        <svg 
          v-if="isDark" 
          class="w-5 h-5 text-amber-400" 
          fill="none" 
          stroke="currentColor" 
          stroke-width="2" 
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <svg 
          v-else 
          class="w-5 h-5 text-slate-600 group-hover:text-slate-900 transition-colors" 
          fill="none" 
          stroke="currentColor" 
          stroke-width="2" 
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
      </button>

      <!-- 2. Multilingual Language Selector Dropdown -->
      <LanguageSwitcher />

      <!-- 3. Interactive Notifications Bell Dropdown -->
      <div class="relative">
        <button 
          type="button"
          @click="toggleNotifications"
          class="relative p-2 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer"
          :title="t('notifications')"
          aria-label="Notifications"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 15.071V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v4.071a2.032 2.032 0 01-.595 1.524L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          <span 
            v-if="unreadNotificationsCount > 0" 
            class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-[10px] font-black rounded-full flex items-center justify-center ring-2 ring-white dark:ring-slate-900 shadow-xs"
          >
            {{ unreadNotificationsCount > 9 ? '9+' : unreadNotificationsCount }}
          </span>
        </button>

        <!-- Notification Dropdown Menu -->
        <transition name="fade">
          <div 
            v-if="isNotificationsOpen" 
            class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden z-50 animate-in fade-in zoom-in-95 duration-150"
          >
            <!-- Dropdown Header -->
            <div class="px-4 py-3 bg-slate-50/80 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-900 dark:text-white">Notifications</span>
                <span v-if="unreadNotificationsCount > 0" class="px-1.5 py-0.2 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-extrabold rounded-full">
                  {{ unreadNotificationsCount }} new
                </span>
              </div>
              <button 
                type="button"
                @click="markAllNotificationsAsRead"
                class="text-[11px] text-slate-600 dark:text-slate-400 hover:underline font-bold cursor-pointer"
              >
                Mark all read
              </button>
            </div>

            <!-- Notifications List -->
            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/80">
              <div 
                v-for="item in notificationList" 
                :key="item.id"
                @click="handleNotificationClick(item)"
                :class="[
                  'p-3.5 flex items-start gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors cursor-pointer text-left',
                  !item.read ? 'bg-slate-50 dark:bg-slate-800/60 font-semibold' : ''
                ]"
              >
                <!-- Notification Type Icon -->
                <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0 text-sm font-bold border border-slate-200 dark:border-slate-700">
                  <span v-if="item.type === 'appointment'">📅</span>
                  <span v-else-if="item.type === 'message'">💬</span>
                  <span v-else>🔔</span>
                </div>

                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between gap-1">
                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ item.title }}</p>
                    <span class="text-[10px] text-slate-400 shrink-0">{{ formatRelativeTime(item.created_at) }}</span>
                  </div>
                  <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-2 mt-0.5 leading-relaxed">{{ item.message }}</p>
                </div>
              </div>

              <!-- Empty Notifications State -->
              <div v-if="notificationList.length === 0" class="p-6 text-center text-xs text-slate-400">
                No notifications right now.
              </div>
            </div>

            <!-- Dropdown Footer -->
            <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-800 text-center">
              <RouterLink 
                :to="alertsRoute"
                @click="isNotificationsOpen = false"
                class="text-xs font-bold text-slate-900 dark:text-white hover:underline"
              >
                View all appointments & alerts →
              </RouterLink>
            </div>
          </div>
        </transition>

        <!-- Invisible Backdrop -->
        <div v-if="isNotificationsOpen" @click="isNotificationsOpen = false" class="fixed inset-0 z-40"></div>
      </div>

      <!-- 4. Interactive Messages Envelope Dropdown -->
      <div class="relative">
        <button 
          type="button"
          @click="toggleMessages"
          class="relative p-2 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer"
          :title="t('direct_messages')"
          aria-label="Direct Messages"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          <span 
            v-if="unreadMessagesCount > 0" 
            class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 bg-blue-600 text-white text-[10px] font-black rounded-full flex items-center justify-center ring-2 ring-white dark:ring-slate-900 shadow-xs"
          >
            {{ unreadMessagesCount > 9 ? '9+' : unreadMessagesCount }}
          </span>
        </button>

        <!-- Recent Messages Dropdown Menu -->
        <transition name="fade">
          <div 
            v-if="isMessagesOpen" 
            class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden z-50 animate-in fade-in zoom-in-95 duration-150"
          >
            <!-- Dropdown Header -->
            <div class="px-4 py-3 bg-slate-50/80 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
              <span class="text-xs font-bold text-slate-900 dark:text-white">Recent Messages</span>
              <div class="flex items-center gap-2.5">
                <button
                  v-if="unreadMessagesCount > 0"
                  type="button"
                  @click="markAllMessagesAsRead"
                  class="text-[11px] text-blue-600 dark:text-blue-400 hover:underline font-bold cursor-pointer"
                >
                  Mark all read
                </button>
                <RouterLink 
                  :to="messagesRoute" 
                  @click="isMessagesOpen = false"
                  class="text-[11px] text-slate-900 dark:text-white hover:underline font-bold"
                >
                  Open Inbox
                </RouterLink>
              </div>
            </div>

            <!-- Recent Messages List -->
            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/80">
              <div 
                v-for="conv in recentMessages" 
                :key="conv.id"
                @click="handleMessageClick(conv)"
                class="p-3.5 flex items-start gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors cursor-pointer text-left"
              >
                <!-- Avatar Preview -->
                <div class="relative shrink-0">
                  <img
                    v-if="conv.other_user?.avatar"
                    :src="conv.other_user.avatar"
                    :alt="conv.other_user.name"
                    class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-slate-700"
                  />
                  <div v-else class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs flex items-center justify-center">
                    {{ (conv.other_user?.name?.[0] || 'O').toUpperCase() }}
                  </div>
                  <span v-if="conv.unread_count > 0" class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-blue-600 rounded-full ring-2 ring-white dark:ring-slate-900"></span>
                </div>

                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between gap-1">
                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ conv.other_user?.name || 'Property Owner' }}</p>
                    <span class="text-[10px] text-slate-400 shrink-0">{{ formatRelativeTime(conv.updated_at) }}</span>
                  </div>
                  <p class="text-[11px] text-slate-700 dark:text-slate-300 font-semibold truncate">{{ conv.property?.title || 'Property Inquiry' }}</p>
                  <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">
                    {{ conv.messages?.[conv.messages?.length - 1]?.body || 'Start chatting...' }}
                  </p>
                </div>
              </div>

              <!-- Empty Messages State -->
              <div v-if="recentMessages.length === 0" class="p-6 text-center text-xs text-slate-400">
                No recent conversation threads.
              </div>
            </div>

            <!-- Dropdown Footer -->
            <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-800 text-center">
              <RouterLink 
                :to="messagesRoute"
                @click="isMessagesOpen = false"
                class="text-xs font-bold text-slate-900 dark:text-white hover:underline"
              >
                Go to Messenger →
              </RouterLink>
            </div>
          </div>
        </transition>

        <!-- Invisible Backdrop -->
        <div v-if="isMessagesOpen" @click="isMessagesOpen = false" class="fixed inset-0 z-40"></div>
      </div>

      <!-- Dual-Role Persona Switcher Toggle (For regular verified users) -->
      <div v-if="!isAdmin" class="hidden md:flex items-center mr-1">
        <button
          v-if="isBuyerMode"
          @click="switchToOwnerMode"
          type="button"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 rounded-xl text-xs font-bold transition-all shadow-xs cursor-pointer active:scale-95"
          title="Switch to Owner Dashboard to manage your properties"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
          <span>Switch to Owner Mode</span>
        </button>

        <button
          v-else-if="isOwnerMode"
          @click="switchToBuyerMode"
          type="button"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold transition-all shadow-xs cursor-pointer active:scale-95"
          title="Switch to Buyer Dashboard to view favorites & bookings"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
          </svg>
          <span>Switch to Buyer Mode</span>
        </button>
      </div>

      <!-- Vertical Divider -->
      <div class="h-6 w-px bg-slate-200 dark:bg-slate-800 mx-0.5 hidden sm:block"></div>

      <!-- 5. User Menu Dropdown -->
      <div class="relative">
        <button
          @click="isUserMenuOpen = !isUserMenuOpen"
          class="flex items-center gap-2.5 p-1 sm:px-2.5 sm:py-1.5 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors group cursor-pointer"
        >
          <img
            v-if="userAvatar && !avatarError"
            :src="userAvatar"
            :alt="user?.name"
            @error="avatarError = true"
            class="w-8 h-8 rounded-full object-cover border border-slate-200 dark:border-slate-700/60 shadow-2xs shrink-0"
          />
          <div v-else class="w-8 h-8 rounded-full bg-slate-900 dark:bg-slate-800 border border-slate-700/60 text-white font-bold text-xs flex items-center justify-center shrink-0">
            {{ userInitials }}
          </div>
          <div class="hidden sm:block text-left">
            <p class="text-xs font-bold text-slate-900 dark:text-white transition-colors leading-tight truncate max-w-[130px]">{{ user?.name || 'BetLink User' }}</p>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-tight mt-0.5 font-medium">{{ userRole }}</p>
          </div>
          <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-700 dark:group-hover:text-white transition-colors ml-0.5 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        <!-- Dropdown Menu -->
        <transition name="fade">
          <div
            v-if="isUserMenuOpen"
            class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 py-2 z-50 overflow-hidden"
          >
            <div class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 sm:hidden">
              <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ user?.name || 'BetLink User' }}</p>
              <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ userRole }}</p>
            </div>

            <RouterLink
              :to="`/${user?.role || 'buyer'}/profile`"
              @click="isUserMenuOpen = false"
              class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors font-medium"
            >
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              {{ t('profile') }}
            </RouterLink>

            <RouterLink
              :to="`/${user?.role || 'buyer'}/settings`"
              @click="isUserMenuOpen = false"
              class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors font-medium"
            >
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              {{ t('settings') }}
            </RouterLink>

            <RouterLink
              to="/"
              @click="isUserMenuOpen = false"
              class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors font-medium"
            >
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              {{ t('public_portal') }}
            </RouterLink>

            <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>

            <button
              @click="showLogoutModal = true; isUserMenuOpen = false"
              class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors font-semibold cursor-pointer"
            >
              <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              {{ t('logout', 'Logout') }}
            </button>
          </div>
        </transition>

        <!-- Invisible Backdrop -->
        <div
          v-if="isUserMenuOpen"
          @click="isUserMenuOpen = false"
          class="fixed inset-0 z-40"
        />
      </div>

    </div>

    <!-- Logout Modal -->
    <LogoutConfirmationModal 
      v-if="showLogoutModal"
      @confirm="handleLogoutConfirm"
      @cancel="showLogoutModal = false"
    />
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { useTheme } from '@/composables/useTheme'
import { useLanguage } from '@/composables/useLanguage'
import { useNotificationStore } from '@/stores/notificationStore'
import { conversationService } from '@/services/conversationService'
import LogoutConfirmationModal from './LogoutConfirmationModal.vue'
import LanguageSwitcher from '../common/LanguageSwitcher.vue'

defineEmits(['toggle-drawer'])

const router = useRouter()
const notificationStore = useNotificationStore()
const isUserMenuOpen = ref(false)
const isNotificationsOpen = ref(false)
const isMessagesOpen = ref(false)
const showLogoutModal = ref(false)

const recentMessages = ref([])

const { user, logout } = useAuth()
const { isDark, toggleTheme } = useTheme()
const { t } = useLanguage()

const notificationList = computed(() => notificationStore.notifications)
const unreadNotificationsCount = computed(() => notificationStore.unreadCount)

const unreadMessagesCount = computed(() => {
  const total = recentMessages.value.reduce((sum, c) => sum + (Number(c.unread_count) || 0), 0)
  return total > 0 ? total : recentMessages.value.filter(c => (c.unread_count || 0) > 0).length
})

const roleTitle = computed(() => {
  const role = user.value?.role || 'buyer'
  const titles = {
    admin: t('admin_dashboard'),
    owner: t('owner_dashboard'),
    buyer: t('buyer_dashboard'),
    agent: t('agent_dashboard')
  }
  return titles[role] || t('dashboard')
})

const userDisplayName = computed(() => {
  return user.value?.name || 'User'
})

const userRole = computed(() => {
  const role = user.value?.role || 'buyer'
  const roleLabels = {
    admin: t('administrator'),
    owner: t('property_owner'),
    buyer: t('buyer_renter'),
    agent: t('real_estate_agent')
  }
  return roleLabels[role] || role.charAt(0).toUpperCase() + role.slice(1)
})

const messagesRoute = computed(() => {
  const role = user.value?.role?.toLowerCase() || 'buyer'
  if (role === 'admin') return '/admin/reports'
  if (role === 'owner') return '/owner/messages'
  return '/buyer/messages'
})

const alertsRoute = computed(() => {
  const role = user.value?.role?.toLowerCase() || 'buyer'
  if (role === 'admin') return '/admin/verifications'
  if (role === 'owner') return '/owner/appointments'
  return '/buyer/appointments'
})

const avatarError = ref(false)
const userAvatar = computed(() => {
  const av = user.value?.avatar_url || user.value?.avatar || ''
  if (typeof av === 'string' && av.startsWith('/storage/')) {
    return `http://127.0.0.1:8000${av}`
  }
  return av
})

const userInitials = computed(() => {
  const name = user.value?.name || 'BetLink User'
  const parts = name.split(' ').filter(Boolean)
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return name.slice(0, 2).toUpperCase()
})

async function fetchNotifications() {
  await notificationStore.fetchNotifications()
}

async function fetchRecentConversations() {
  try {
    const res = await conversationService.getConversations({ per_page: 5 })
    const payload = res?.data || {}
    recentMessages.value = (payload.data || payload || []).slice(0, 5)
  } catch {}
}

function toggleNotifications() {
  isNotificationsOpen.value = !isNotificationsOpen.value
  if (isNotificationsOpen.value) {
    isMessagesOpen.value = false
    isUserMenuOpen.value = false
    fetchNotifications()
  }
}

function toggleMessages() {
  isMessagesOpen.value = !isMessagesOpen.value
  if (isMessagesOpen.value) {
    isNotificationsOpen.value = false
    isUserMenuOpen.value = false
    fetchRecentConversations()
  }
}

async function markAllNotificationsAsRead() {
  notificationStore.markAllAsRead()
}

async function markAllMessagesAsRead() {
  recentMessages.value.forEach(c => { c.unread_count = 0 })
  await conversationService.markAllAsRead()
}

async function handleNotificationClick(item) {
  if (item && item.id) {
    notificationStore.markAsRead(item.id)
  }
  isNotificationsOpen.value = false

  if (item && item.link) {
    router.push(item.link)
  }
}

async function handleMessageClick(conv) {
  if (conv) {
    conv.unread_count = 0
  }
  isMessagesOpen.value = false
  if (conv?.id) {
    await conversationService.markAsRead(conv.id)
  }

  const role = user.value?.role?.toLowerCase() || 'buyer'
  if (role === 'owner') {
    router.push(`/owner/messages?conversation=${conv.id}`)
  } else {
    router.push(`/buyer/messages?conversation=${conv.id}`)
  }
}

function formatRelativeTime(dateStr) {
  if (!dateStr) return ''
  try {
    const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000)
    if (diff < 60) return 'Just now'
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`
    return `${Math.floor(diff / 86400)}d ago`
  } catch {
    return ''
  }
}

function handleLogoutConfirm() {
  logout()
}

onMounted(() => {
  fetchNotifications()
  fetchRecentConversations()
  window.addEventListener('message-read', fetchRecentConversations)
  window.addEventListener('conversations-updated', fetchRecentConversations)
})

onUnmounted(() => {
  window.removeEventListener('message-read', fetchRecentConversations)
  window.removeEventListener('conversations-updated', fetchRecentConversations)
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.15s ease-in-out, transform 0.15s ease-in-out;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
