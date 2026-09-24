import api, { cachedGet } from './api'
import { ENDPOINTS } from '../config/api'

const metaCache = {
  types: null,
  categories: {},
  amenities: null,
}

export const propertyService = {
  /**
   * Get public active properties list (paginated)
   * @param {Object} params { listing_type, featured, per_page, page }
   */
  async getProperties(params = {}) {
    return cachedGet(ENDPOINTS.PROPERTIES.LIST, { params, ttl: 30000 })
  },

  /**
   * Get featured properties
   */
  async getFeatured() {
    return cachedGet(ENDPOINTS.PROPERTIES.FEATURED, { ttl: 60000 })
  },

  /**
   * Get public property details by ID or slug
   * @param {string|number} slugOrId
   */
  async getPropertyBySlug(slugOrId) {
    return cachedGet(ENDPOINTS.PROPERTIES.DETAIL(slugOrId), { ttl: 60000 })
  },

  /**
   * Get all active property types with categories
   */
  async getPropertyTypes() {
    if (metaCache.types) return metaCache.types
    const res = await api.get(ENDPOINTS.PROPERTIES.TYPES)
    metaCache.types = res
    return res
  },

  /**
   * Get active categories (optionally filtered by property_type_id)
   * @param {Object} params { property_type_id }
   */
  async getCategories(params = {}) {
    const key = params.property_type_id || 'all'
    if (metaCache.categories[key]) return metaCache.categories[key]
    const res = await api.get(ENDPOINTS.PROPERTIES.CATEGORIES, { params })
    metaCache.categories[key] = res
    return res
  },

  /**
   * Get all active amenities
   */
  async getAmenities() {
    if (metaCache.amenities) return metaCache.amenities
    const res = await api.get(ENDPOINTS.PROPERTIES.AMENITIES)
    metaCache.amenities = res
    return res
  },

  /**
   * Get owner's property listings
   * @param {Object} params { status, per_page, page }
   */
  async getMyProperties(params = {}) {
    return api.get(ENDPOINTS.OWNER.PROPERTIES, { params })
  },

  /**
   * Get single owner property by ID (for edit/view)
   * @param {number|string} id
   */
  async getOwnerProperty(id) {
    return api.get(ENDPOINTS.OWNER.PROPERTY_DETAIL(id))
  },

  /**
   * Create a new property listing (Owner/Admin)
   * @param {Object|FormData} data
   */
  async createProperty(data) {
    const isFormData = data instanceof FormData
    return api.post(ENDPOINTS.OWNER.CREATE_PROPERTY, data, {
      headers: isFormData ? { 'Content-Type': 'multipart/form-data' } : {},
    })
  },

  /**
   * Update an existing property listing
   * @param {number|string} id
   * @param {Object} data
   */
  async updateProperty(id, data) {
    return api.put(ENDPOINTS.OWNER.UPDATE_PROPERTY(id), data)
  },

  /**
   * Delete a property
   * @param {number|string} id
   */
  async deleteProperty(id) {
    return api.delete(ENDPOINTS.OWNER.DELETE_PROPERTY(id))
  },

  /**
   * Submit draft or rejected property for approval
   * @param {number|string} id
   */
  async publishProperty(id) {
    return api.post(ENDPOINTS.OWNER.PUBLISH_PROPERTY(id))
  },

  /**
   * Upload images to a property
   * @param {number|string} propertyId
   * @param {FormData} formData
   */
  async uploadImages(propertyId, formData) {
    return api.post(ENDPOINTS.OWNER.UPLOAD_IMAGES(propertyId), formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
  },

  /**
   * Delete a property image
   * @param {number|string} propertyId
   * @param {number|string} imageId
   */
  async deleteImage(propertyId, imageId) {
    return api.delete(ENDPOINTS.OWNER.DELETE_IMAGE(propertyId, imageId))
  },

  /**
   * Set primary/featured image for property
   * @param {number|string} propertyId
   * @param {number|string} imageId
   */
  async setPrimaryImage(propertyId, imageId) {
    return api.put(ENDPOINTS.OWNER.SET_PRIMARY_IMAGE(propertyId, imageId))
  },

  /**
   * Reorder property images
   * @param {number|string} propertyId
   * @param {Array|Object} order Array of image IDs in new order [id1, id2, ...] or { order: [...] }
   */
  async reorderImages(propertyId, order) {
    const payload = Array.isArray(order) ? { order } : (order?.order ? { order: order.order } : { order: order?.images || [] })
    return api.put(ENDPOINTS.OWNER.REORDER_IMAGES(propertyId), payload)
  },
}

export default propertyService
