import api, { cachedGet, clearApiCache } from './api'
import { ENDPOINTS } from '../config/api'

export const appointmentService = {
  /**
   * Get user appointments (as visitor, owner, or agent)
   * @param {Object} params { status, per_page, page }
   */
  async getAppointments(params = {}) {
    return cachedGet(ENDPOINTS.APPOINTMENTS.LIST, { params, ttl: 15000 })
  },

  /**
   * Get available slots for property based on seller schedule & bookings
   * @param {number|string} propertyId
   * @param {string} date YYYY-MM-DD
   */
  async getAvailableSlots(propertyId, date) {
    return api.get(`/properties/${propertyId}/available-slots`, { params: { date } })
  },

  /**
   * Get owner working availability schedule
   */
  async getOwnerAvailability() {
    return api.get('/owner/availability')
  },

  /**
   * Save owner working availability schedule
   * @param {Array} schedules
   */
  async saveOwnerAvailability(schedules) {
    return api.post('/owner/availability', { schedules })
  },

  /**
   * Book an appointment for a property tour
   * @param {Object} data { property_id, scheduled_at, note, viewing_type }
   */
  async bookAppointment(data) {
    clearApiCache('/appointments')
    return api.post(ENDPOINTS.APPOINTMENTS.CREATE, data)
  },

  /**
   * Reschedule or update appointment
   * @param {number|string} id
   * @param {Object} data
   */
  async updateAppointment(id, data) {
    clearApiCache('/appointments')
    return api.put(`/appointments/${id}`, data)
  },

  /**
   * Confirm or approve appointment (owner / agent / admin)
   * @param {number|string} id
   */
  async confirmAppointment(id) {
    clearApiCache('/appointments')
    try {
      return await api.put(`/appointments/${id}/confirm`)
    } catch (e) {
      return await api.patch(`/owner/appointments/${id}/approve`)
    }
  },

  /**
   * Mark appointment as completed
   * @param {number|string} id
   */
  async completeAppointment(id) {
    clearApiCache('/appointments')
    try {
      return await api.put(`/appointments/${id}/complete`)
    } catch (e) {
      return await api.patch(`/owner/appointments/${id}/complete`)
    }
  },

  /**
   * Cancel appointment with a reason
   * @param {number|string} id
   * @param {string} reason
   */
  async cancelAppointment(id, reason = '') {
    clearApiCache('/appointments')
    return api.put(ENDPOINTS.APPOINTMENTS.CANCEL(id), { reason })
  },

  /**
   * Delete an appointment record
   * @param {number|string} id
   */
  async deleteAppointment(id) {
    clearApiCache('/appointments')
    return api.delete(`/appointments/${id}`)
  },

  /**
   * Get appointment details by ID
   * @param {number|string} id
   */
  async getAppointment(id) {
    return cachedGet(ENDPOINTS.APPOINTMENTS.DETAIL(id), { ttl: 15000 })
  }
}

export default appointmentService
