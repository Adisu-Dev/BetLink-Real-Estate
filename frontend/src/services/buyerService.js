import api, { cachedGet } from './api'
import { ENDPOINTS } from '../config/api'

export const buyerService = {
  /**
   * Get buyer dashboard metrics and data
   */
  async getDashboard() {
    try {
      return await cachedGet(ENDPOINTS.BUYER.DASHBOARD, { ttl: 60000, swr: true })
    } catch {
      return {
        data: {
          stats: {
            savedProperties: 0,
            upcomingAppointments: 0,
            activeInquiries: 0,
            recentViews: 0,
            shortStays: 0
          },
          monthlyTrends: [],
          subCityDistribution: [],
          recommended: []
        }
      }
    }
  },

  /**
   * Real-time global search across properties, appointments, and messages
   * @param {string} query
   */
  async search(query) {
    if (!query || !query.trim()) {
      return {
        data: {
          properties: [],
          appointments: [],
          messages: [],
          total: 0
        }
      }
    }

    return cachedGet(ENDPOINTS.BUYER.SEARCH, {
      params: { q: query.trim() },
      ttl: 15000
    })
  },

  /**
   * Get filtered properties for Buyer marketplace
   * @param {Object} params
   */
  async getProperties(params = {}) {
    return cachedGet(ENDPOINTS.BUYER.PROPERTIES, { params, ttl: 30000 })
  }
}

export default buyerService
