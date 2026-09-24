import api from './api'
import { ENDPOINTS } from '../config/api'

export const authService = {
  /**
   * Send 6-digit verification OTP to user's email
   * @param {Object} data { email, name, phone, password, role }
   */
  async sendOtp(data) {
    return api.post(ENDPOINTS.AUTH.SEND_OTP, data)
  },

  /**
   * Verify OTP and complete account activation
   * @param {Object} data { email, otp }
   */
  async verifyOtp(data) {
    return api.post(ENDPOINTS.AUTH.VERIFY_OTP, data)
  },

  /**
   * Register a new user
   * @param {Object} data { name, email, phone, password, password_confirmation, role }
   */
  async register(data) {
    return api.post(ENDPOINTS.AUTH.REGISTER, data)
  },

  /**
   * Login user
   * @param {Object} credentials { email, password }
   */
  async login(credentials) {
    return api.post(ENDPOINTS.AUTH.LOGIN, credentials)
  },

  /**
   * Logout user
   */
  async logout() {
    return api.post(ENDPOINTS.AUTH.LOGOUT)
  },

  /**
   * Get current authenticated user details
   */
  async me() {
    return api.get(ENDPOINTS.AUTH.ME)
  },

  /**
   * Update authenticated user password
   * @param {Object} data { current_password, password, password_confirmation }
   */
  async updatePassword(data) {
    return api.put(ENDPOINTS.AUTH.UPDATE_PASSWORD, data)
  },

  /**
   * Request password reset email
   * @param {Object} data { email }
   */
  async forgotPassword(data) {
    return api.post(ENDPOINTS.AUTH.FORGOT_PASSWORD, data)
  },

  /**
   * Reset password with token
   * @param {Object} data { token, email, password, password_confirmation }
   */
  async resetPassword(data) {
    return api.post(ENDPOINTS.AUTH.RESET_PASSWORD, data)
  },
}

export default authService
