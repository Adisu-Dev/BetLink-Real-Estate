import api from './api'
import { ENDPOINTS } from '../config/api'

export const bookingService = {
  /**
   * Get property calendar availability and booked dates
   * @param {number|string} propertyId
   */
  async getAvailability(propertyId) {
    return api.get(ENDPOINTS.PROPERTIES.AVAILABILITY(propertyId))
  },

  /**
   * Create a short rental booking
   * @param {Object} data { property_id, check_in_date, check_out_date, guests_count, special_requests }
   */
  async createBooking(data) {
    return api.post(ENDPOINTS.BOOKINGS.CREATE, data)
  },

  /**
   * Get guest's bookings (paginated)
   * @param {Object} params { per_page, page }
   */
  async getBookings(params = {}) {
    return api.get(ENDPOINTS.BOOKINGS.LIST, { params })
  },

  /**
   * Get single booking details
   * @param {number|string} id
   */
  async getBooking(id) {
    return api.get(ENDPOINTS.BOOKINGS.DETAIL(id))
  },

  /**
   * Cancel booking
   * @param {number|string} id
   */
  async cancelBooking(id) {
    return api.put(ENDPOINTS.BOOKINGS.CANCEL(id))
  },
}

export default bookingService
