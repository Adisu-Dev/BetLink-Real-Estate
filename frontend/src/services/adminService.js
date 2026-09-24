import api, { clearApiCache } from './api'
import { ENDPOINTS } from '../config/api'

export const adminService = {
  /**
   * Get Admin dashboard metrics & KPIs
   */
  async getDashboard() {
    return api.get(ENDPOINTS.ADMIN.DASHBOARD)
  },

  /**
   * Get Admin platform analytics
   */
  async getAnalytics() {
    return api.get('/admin/analytics')
  },

  /**
   * Get system users list (paginated)
   * @param {Object} params { q, role, status, per_page, page }
   */
  async getUsers(params = {}) {
    return api.get(ENDPOINTS.ADMIN.USERS, { params })
  },

  /**
   * Get user details
   * @param {number|string} id
   */
  async getUser(id) {
    return api.get(ENDPOINTS.ADMIN.USER_DETAIL(id))
  },

  /**
   * Suspend a user
   * @param {number|string} id
   */
  async suspendUser(id) {
    return api.post(ENDPOINTS.ADMIN.SUSPEND_USER(id))
  },

  /**
   * Ban a user
   * @param {number|string} id
   */
  async banUser(id) {
    return api.post(ENDPOINTS.ADMIN.BAN_USER(id))
  },

  /**
   * Activate a user
   * @param {number|string} id
   */
  async activateUser(id) {
    return api.post(ENDPOINTS.ADMIN.ACTIVATE_USER(id))
  },

  /**
   * Create a new user account
   * @param {Object} userData { name, email, phone, role, status, password }
   */
  async createUser(userData) {
    const res = await api.post(ENDPOINTS.ADMIN.USERS, userData)
    clearApiCache()
    return res
  },

  /**
   * Update user status (active, suspended, banned)
   * @param {number|string} id
   * @param {string} status
   */
  async updateUserStatus(id, status) {
    const res = await api.put(`${ENDPOINTS.ADMIN.USERS}/${id}/status`, { status })
    clearApiCache()
    return res
  },

  /**
   * Delete a user
   * @param {number|string} id
   */
  async deleteUser(id) {
    const res = await api.delete(ENDPOINTS.ADMIN.DELETE_USER(id))
    clearApiCache()
    return res
  },

  /**
   * Update property status (active, draft, sold, rented, pending, rejected)
   * @param {number|string} id
   * @param {string} status
   */
  async updatePropertyStatus(id, status) {
    const res = await api.put(`${ENDPOINTS.ADMIN.PROPERTIES}/${id}/status`, { status })
    clearApiCache()
    return res
  },

  /**
   * Get all properties for admin moderation
   * @param {Object} params { q, status, listing_type, per_page, page }
   */
  async getProperties(params = {}) {
    return api.get(ENDPOINTS.ADMIN.PROPERTIES, { params })
  },

  /**
   * Get property detail for admin inspection
   * @param {number|string} id
   */
  async getProperty(id) {
    return api.get(ENDPOINTS.ADMIN.PROPERTY_DETAIL(id))
  },

  /**
   * Approve and publish pending property
   * @param {number|string} id
   */
  async approveProperty(id) {
    const res = await api.post(ENDPOINTS.ADMIN.APPROVE_PROPERTY(id))
    clearApiCache()
    return res
  },

  /**
   * Reject pending property with reason
   * @param {number|string} id
   * @param {string} reason
   */
  async rejectProperty(id, reason) {
    const res = await api.post(ENDPOINTS.ADMIN.REJECT_PROPERTY(id), { reason })
    clearApiCache()
    return res
  },

  /**
   * Toggle or configure featured flag and rules on property
   * @param {number|string} id
   * @param {Object} data { is_featured, featured_priority, featured_from, featured_until, featured_reason }
   */
  async toggleFeature(id, data = {}) {
    const res = await api.post(ENDPOINTS.ADMIN.FEATURE_PROPERTY(id), data)
    clearApiCache()
    return res
  },

  /**
   * Force delete property
   * @param {number|string} id
   */
  async deleteProperty(id) {
    const res = await api.delete(ENDPOINTS.ADMIN.DELETE_PROPERTY(id))
    clearApiCache()
    return res
  },

  /**
   * Get reports list
   * @param {Object} params { status, reason, per_page, page }
   */
  async getReports(params = {}) {
    return api.get(ENDPOINTS.ADMIN.REPORTS, { params })
  },

  /**
   * Update report status and admin notes
   * @param {number|string} id
   * @param {Object} data { status, admin_notes }
   */
  async updateReport(id, data) {
    return api.put(ENDPOINTS.ADMIN.UPDATE_REPORT(id), data)
  },

  /**
   * Get verification requests list
   * @param {Object} params { status, type, per_page, page }
   */
  async getVerifications(params = {}) {
    return api.get(ENDPOINTS.ADMIN.VERIFICATIONS, { params })
  },

  /**
   * Approve verification request
   * @param {number|string} id
   * @param {string} notes
   */
  async approveVerification(id, notes = '') {
    const res = await api.post(ENDPOINTS.ADMIN.APPROVE_VERIFICATION(id), { notes })
    clearApiCache()
    return res
  },

  /**
   * Reject verification request
   * @param {number|string} id
   * @param {string} notes
   */
  async rejectVerification(id, notes) {
    const res = await api.post(ENDPOINTS.ADMIN.REJECT_VERIFICATION(id), { notes })
    clearApiCache()
    return res
  },

  /**
   * Get property analytics
   * @param {Object} params { property_id, period }
   */
  async getPropertyAnalytics(params = {}) {
    return api.get(ENDPOINTS.ADMIN.ANALYTICS_PROPERTIES, { params })
  },

  /**
   * Get user analytics
   */
  async getUserAnalytics() {
    return api.get(ENDPOINTS.ADMIN.ANALYTICS_USERS)
  },

  /**
   * Get system settings
   */
  async getSettings() {
    return api.get(ENDPOINTS.ADMIN.SETTINGS)
  },

  /**
   * Update system settings
   * @param {Array} settings [{ key, value }]
   */
  async updateSettings(settings) {
    return api.put(ENDPOINTS.ADMIN.UPDATE_SETTINGS, { settings })
  },
}

export default adminService
