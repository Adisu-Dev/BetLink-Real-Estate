import { defineStore } from 'pinia'
import { ref } from 'vue'
import { cachedGet, clearApiCache } from '../services/api'
import { ENDPOINTS } from '../config/api'

export const useNotificationStore = defineStore('notification', () => {
  const notifications = ref([])
  const unreadCount = ref(0)
  const isLoading = ref(false)
  const lastFetched = ref(null)

  const CACHE_TTL = 30 * 1000 // 30 seconds

  async function fetchNotifications(force = false) {
    const now = Date.now()
    if (!force && lastFetched.value && (now - lastFetched.value < CACHE_TTL)) {
      return notifications.value
    }

    isLoading.value = true
    try {
      const res = await cachedGet(ENDPOINTS.NOTIFICATIONS.LIST, {
        skipCache: force,
        ttl: 30000
      })
      const list = res?.data?.data || res?.data || res || []
      notifications.value = Array.isArray(list) ? list : []
      unreadCount.value = notifications.value.filter(n => !n.read && !n.read_at).length
      lastFetched.value = Date.now()
      return notifications.value
    } catch (error) {
      return notifications.value
    } finally {
      isLoading.value = false
    }
  }

  async function fetchUnreadCount() {
    try {
      const res = await cachedGet(ENDPOINTS.NOTIFICATIONS.UNREAD_COUNT, { ttl: 15000 })
      unreadCount.value = res?.data?.unread_count ?? res?.unread_count ?? unreadCount.value
      return unreadCount.value
    } catch {
      return unreadCount.value
    }
  }

  function markAsRead(id) {
    const target = notifications.value.find(n => n.id === id)
    if (target && !target.read) {
      target.read = true
      unreadCount.value = Math.max(0, unreadCount.value - 1)
    }
  }

  function markAllAsRead() {
    notifications.value.forEach(n => { n.read = true })
    unreadCount.value = 0
  }

  function invalidateCache() {
    lastFetched.value = null
    clearApiCache('/notifications')
  }

  return {
    notifications,
    unreadCount,
    isLoading,
    fetchNotifications,
    fetchUnreadCount,
    markAsRead,
    markAllAsRead,
    invalidateCache
  }
})
