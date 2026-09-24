import api, { cachedGet } from './api'
import { ENDPOINTS } from '../config/api'

export const notificationService = {
  /**
   * Get user notifications from live backend
   */
  async getNotifications(params = {}) {
    try {
      return await cachedGet(ENDPOINTS.NOTIFICATIONS.LIST, { params, ttl: 20000 })
    } catch {
      return {
        data: {
          data: [],
          total: 0
        }
      }
    }
  },

  /**
   * Get unread notifications count
   */
  async getUnreadCount() {
    try {
      return await cachedGet(ENDPOINTS.NOTIFICATIONS.UNREAD_COUNT, { ttl: 20000 })
    } catch {
      return { data: { count: 0 } }
    }
  },

  /**
   * Mark single notification as read
   */
  async markAsRead(id) {
    return api.put(ENDPOINTS.NOTIFICATIONS.MARK_READ(id))
  },

  /**
   * Mark all notifications as read
   */
  async markAllAsRead() {
    return api.put(ENDPOINTS.NOTIFICATIONS.MARK_ALL_READ)
  },
}

export default notificationService
