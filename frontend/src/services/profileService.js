import api from './api'
import { ENDPOINTS } from '../config/api'

export const profileService = {
  /**
   * Get user profile details
   */
  async getProfile() {
    return api.get(ENDPOINTS.PROFILE.GET)
  },

  /**
   * Update profile information
   * @param {Object} data { name, phone, bio, city_id, ... }
   */
  async updateProfile(data) {
    return api.put(ENDPOINTS.PROFILE.UPDATE, data)
  },

  /**
   * Upload user avatar
   * @param {FormData} formData
   */
  async uploadAvatar(formData) {
    return api.post(ENDPOINTS.PROFILE.AVATAR, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
  },

  /**
   * Delete account
   * @param {string} password
   */
  async deleteAccount(password) {
    return api.delete(ENDPOINTS.PROFILE.DELETE, { data: { password } })
  },

  /**
   * Get public profile by user ID
   * @param {number|string} userId
   */
  async getPublicProfile(userId) {
    return api.get(ENDPOINTS.PROFILE.PUBLIC(userId))
  },
}

export default profileService
