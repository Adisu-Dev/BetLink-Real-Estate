import api from './api'
import { ENDPOINTS } from '../config/api'

export const contactService = {
  /**
   * Submit contact form message
   * @param {Object} data { name, email, phone, subject, message }
   */
  async sendMessage(data) {
    return api.post(ENDPOINTS.CONTACT.SEND, data)
  },

  /**
   * Get active FAQs
   * @param {Object} params { category }
   */
  async getFaqs(params = {}) {
    return api.get(ENDPOINTS.CONTACT.FAQS, { params })
  },

  /**
   * Submit platform safety/abuse report
   * @param {Object} data { reportable_type, reportable_id, reason, description }
   */
  async submitReport(data) {
    return api.post(ENDPOINTS.REPORTS.CREATE, data)
  },
}

export default contactService
