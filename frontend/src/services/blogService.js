import api from './api'
import { ENDPOINTS } from '../config/api'

export const blogService = {
  /**
   * Get published blogs list (paginated)
   * @param {Object} params { category, q, per_page, page }
   */
  async getBlogs(params = {}) {
    return api.get(ENDPOINTS.BLOGS.LIST, { params })
  },

  /**
   * Get blog categories with count
   */
  async getCategories() {
    return api.get(ENDPOINTS.BLOGS.CATEGORIES)
  },

  /**
   * Get blog detail by slug with related posts
   * @param {string} slug
   */
  async getBlogBySlug(slug) {
    return api.get(ENDPOINTS.BLOGS.DETAIL(slug))
  },
}

export default blogService
