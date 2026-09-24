import api from './api'
import { ENDPOINTS } from '../config/api'

export const agentService = {
  /**
   * Get agent dashboard KPIs, leads, and performance data
   */
  async getDashboard() {
    return api.get(ENDPOINTS.AGENT.DASHBOARD)
  },

  /**
   * Get agent managed properties with filters
   * @param {Object} params
   */
  async getProperties(params = {}) {
    return api.get(ENDPOINTS.AGENT.PROPERTIES, { params })
  },

  /**
   * Get agent leads (conversations / inquiries from prospective buyers)
   * @param {Object} params
   */
  async getLeads(params = {}) {
    return api.get(ENDPOINTS.AGENT.LEADS, { params })
  },

  /**
   * Update lead pipeline status and notes
   * @param {number|string} leadId
   * @param {Object} data { status, notes }
   */
  async updateLeadStatus(leadId, data) {
    return api.put(ENDPOINTS.AGENT.UPDATE_LEAD_STATUS(leadId), data)
  },

  /**
   * Get agent analytics, trends, and conversion funnel
   * @param {Object} params { time_range }
   */
  async getAnalytics(params = {}) {
    return api.get(ENDPOINTS.AGENT.ANALYTICS, { params })
  },

  /**
   * Get agent verification credentials and approval status
   */
  async getVerifications() {
    return api.get(ENDPOINTS.AGENT.VERIFICATIONS)
  },

  /**
   * Submit agent professional license / ID document for verification
   * @param {FormData|Object} payload
   */
  async submitVerification(payload) {
    return api.post(ENDPOINTS.AGENT.VERIFICATIONS, payload)
  },

  /**
   * Get verified public agents directory
   * @param {Object} params { search, specialization, location, page }
   */
  async getPublicAgents(params = {}) {
    return api.get(ENDPOINTS.AGENT.PUBLIC_AGENTS, { params })
  },
}

export default agentService
