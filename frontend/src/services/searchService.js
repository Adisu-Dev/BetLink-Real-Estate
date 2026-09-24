import api, { cachedGet } from './api'
import { ENDPOINTS } from '../config/api'

export const searchService = {
  /**
   * Search properties with filters
   * @param {Object} filters
   */
  async search(filters = {}) {
    return cachedGet(ENDPOINTS.SEARCH.BASIC, { params: filters, ttl: 30000 })
  },

  /**
   * Geographic radius map search
   * @param {Object} params { lat, lng, radius }
   */
  async mapSearch(params) {
    return api.get(ENDPOINTS.SEARCH.MAP, { params })
  },

  /**
   * Search autocomplete suggestions
   * @param {string} query
   */
  async suggestions(query) {
    try {
      return await api.get(ENDPOINTS.SEARCH.SUGGESTIONS, { params: { q: query } })
    } catch {
      return { success: true, data: [] }
    }
  },
}

export default searchService
