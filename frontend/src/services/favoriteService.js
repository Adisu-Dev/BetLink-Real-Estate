import api, { clearApiCache } from './api'
import { ENDPOINTS } from '../config/api'
import { useDashboardStore } from '../stores/dashboardStore'

export const favoriteService = {
  /**
   * Get user favorites (paginated)
   * @param {Object} params { per_page, page }
   */
  async getFavorites(params = {}) {
    const response = await api.get(ENDPOINTS.FAVORITES.LIST, { params })
    const data = response?.data?.data || response?.data || []
    if (Array.isArray(data)) {
      try {
        const dashboardStore = useDashboardStore()
        dashboardStore.setSavedProperties(data.length)
      } catch {
        // ignore store initialization context if outside Vue component
      }
    }
    return response
  },

  /**
   * Add property to favorites
   * @param {number|string} propertyId
   */
  async addFavorite(propertyId) {
    const response = await api.post(ENDPOINTS.FAVORITES.ADD(propertyId))
    try {
      const dashboardStore = useDashboardStore()
      dashboardStore.incrementSavedProperties()
    } catch {
      clearApiCache('/buyer/dashboard')
    }
    return response
  },

  // Alias for compatibility
  async add(propertyId) {
    return this.addFavorite(propertyId)
  },

  /**
   * Remove property from favorites
   * @param {number|string} propertyId
   */
  async removeFavorite(propertyId) {
    const response = await api.delete(ENDPOINTS.FAVORITES.REMOVE(propertyId))
    try {
      const dashboardStore = useDashboardStore()
      dashboardStore.decrementSavedProperties()
    } catch {
      clearApiCache('/buyer/dashboard')
    }
    return response
  },

  // Alias for compatibility
  async remove(propertyId) {
    return this.removeFavorite(propertyId)
  },

  /**
   * Check if property is favorited
   * @param {number|string} propertyId
   */
  async checkFavorite(propertyId) {
    try {
      return await api.get(ENDPOINTS.FAVORITES.CHECK(propertyId))
    } catch {
      return { data: { is_favorited: false } }
    }
  },
}

export default favoriteService
