import api, { cachedGet, clearApiCache } from './api'
import { ENDPOINTS } from '../config/api'

export const ownerService = {
  /**
   * Get authenticated owner dashboard metrics and data with SWR Caching
   */
  async getDashboard(force = true) {
    return cachedGet(ENDPOINTS.OWNER.DASHBOARD, {
      skipCache: force,
      ttl: force ? 0 : 15000
    })
  },

  /**
   * Get owner properties with filters & snappy SWR caching
   * @param {Object} params { search, status, listing_type, per_page, page }
   * @param {boolean} force
   */
  async getProperties(params = {}, force = false) {
    return cachedGet(ENDPOINTS.OWNER.PROPERTIES, {
      params,
      skipCache: force,
      ttl: 15000 // 15 seconds instant memory cache
    })
  },

  /**
   * Upload property image file (laptop / mobile)
   * @param {File} file
   */
  async uploadImage(file) {
    const formData = new FormData()
    formData.append('image', file)
    return api.post(`${ENDPOINTS.OWNER.PROPERTIES}/upload-image`, formData)
  },

  /**
   * Create a new property listing
   * @param {Object} data
   */
  async createProperty(data) {
    const res = await api.post(ENDPOINTS.OWNER.PROPERTIES, data)
    clearApiCache()
    return res
  },

  /**
   * Update existing property
   * @param {number|string} id
   * @param {Object} data
   */
  async updateProperty(id, data) {
    const res = await api.put(`${ENDPOINTS.OWNER.PROPERTIES}/${id}`, data)
    clearApiCache()
    return res
  },

  /**
   * Delete property listing
   * @param {number|string} id
   */
  async deleteProperty(id) {
    const res = await api.delete(`${ENDPOINTS.OWNER.PROPERTIES}/${id}`)
    clearApiCache()
    return res
  },

  /**
   * Toggle property status (active vs draft)
   * @param {number|string} id
   */
  async togglePropertyStatus(id) {
    const res = await api.post(`${ENDPOINTS.OWNER.PROPERTIES}/${id}/toggle-status`)
    clearApiCache()
    return res
  },

  /**
   * Get owner tour appointments
   * @param {Object} params { status, per_page, page }
   */
  async getAppointments(params = {}) {
    return api.get('/owner/appointments', { params })
  },

  /**
   * Approve tour request
   * @param {number|string} appointmentId
   */
  async confirmAppointment(appointmentId) {
    const res = await api.patch(`/owner/appointments/${appointmentId}/approve`)
    clearApiCache('/owner/dashboard')
    return res
  },

  /**
   * Reject tour request
   * @param {number|string} appointmentId
   * @param {string} reason
   */
  async cancelAppointment(appointmentId, reason = '') {
    const res = await api.patch(`/owner/appointments/${appointmentId}/reject`, { reason })
    clearApiCache('/owner/dashboard')
    return res
  },

  /**
   * Complete tour request
   * @param {number|string} appointmentId
   */
  async completeAppointment(appointmentId) {
    const res = await api.patch(`/owner/appointments/${appointmentId}/complete`)
    clearApiCache('/owner/dashboard')
    return res
  },

  /**
   * Get owner portfolio analytics
   */
  async getAnalytics(params = {}) {
    return api.get('/owner/analytics', { params })
  },

  /**
   * Get owner property verification documents
   */
  async getVerifications() {
    return api.get('/owner/verifications')
  },

  /**
   * Submit property verification document
   * @param {Object} data { property_id, document_type, document_url, notes }
   */
  async submitVerification(data) {
    const res = await api.post('/owner/verifications', data)
    clearApiCache()
    return res
  }
}

export default ownerService
