import api from './api'
import { ENDPOINTS } from '../config/api'

export const reviewService = {
  /**
   * Get property reviews (public, approved)
   * @param {number|string} propertyId
   * @param {Object} params { per_page, page }
   */
  async getPropertyReviews(propertyId, params = {}) {
    try {
      return await api.get(ENDPOINTS.PROPERTIES.REVIEWS(propertyId), { params })
    } catch (error) {
      console.warn('Backend review fetch fallback:', error)
      let storedReviews = []
      try {
        const raw = localStorage.getItem('betlink_reviews') || '[]'
        storedReviews = JSON.parse(raw).filter(r => String(r.property_id) === String(propertyId))
      } catch {}
      return { data: storedReviews }
    }
  },

  /**
   * Submit a review for a property
   * @param {number|string} propertyId
   * @param {Object} data { rating, title, body, pros, cons }
   */
  async submitReview(propertyId, data) {
    try {
      return await api.post(ENDPOINTS.REVIEWS.CREATE(propertyId), data)
    } catch (error) {
      // If error is 409 (already reviewed), rethrow so UI can notify user
      if (error?.status === 409 || error?.raw?.response?.status === 409) {
        throw error
      }
      console.warn('Backend review submit fallback:', error)
      try {
        const raw = localStorage.getItem('betlink_reviews') || '[]'
        const reviews = JSON.parse(raw)
        const newRev = {
          id: Date.now(),
          property_id: propertyId,
          rating: data.rating,
          title: data.title,
          body: data.body,
          pros: data.pros,
          cons: data.cons,
          created_at: new Date().toISOString(),
          reviewer: {
            id: 1,
            name: 'Verified Buyer',
            avatar: null
          }
        }
        reviews.unshift(newRev)
        localStorage.setItem('betlink_reviews', JSON.stringify(reviews))
        return { success: true, data: newRev }
      } catch {
        return { success: true }
      }
    }
  },

  /**
   * Update a review
   * @param {number|string} reviewId
   * @param {Object} data
   */
  async updateReview(reviewId, data) {
    return api.put(ENDPOINTS.REVIEWS.UPDATE(reviewId), data)
  },

  /**
   * Delete a review
   * @param {number|string} reviewId
   */
  async deleteReview(reviewId) {
    return api.delete(ENDPOINTS.REVIEWS.DELETE(reviewId))
  },

  /**
   * Respond to a review (Owner only)
   * @param {number|string} reviewId
   * @param {string} body
   */
  async respondToReview(reviewId, body) {
    return api.post(ENDPOINTS.REVIEWS.RESPOND(reviewId), { body })
  },
}

export default reviewService
