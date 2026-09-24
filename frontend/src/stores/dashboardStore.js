import { defineStore } from 'pinia'
import { ref } from 'vue'
import { cachedGet, clearApiCache } from '../services/api'
import { ENDPOINTS } from '../config/api'

export const useDashboardStore = defineStore('dashboard', () => {
  const stats = ref({
    savedProperties: 0,
    upcomingAppointments: 0,
    activeInquiries: 0,
    recentViews: 0,
    shortStays: 0
  })
  const monthlyTrends = ref([])
  const subCityDistribution = ref([])
  const recommended = ref([])
  const upcomingAppointmentsList = ref([])
  const isLoading = ref(false)
  const lastFetched = ref(null)

  const CACHE_TTL = 60 * 1000 // 60 seconds

  async function fetchDashboardData(force = false) {
    const now = Date.now()
    if (!force && lastFetched.value && (now - lastFetched.value < CACHE_TTL)) {
      return {
        stats: stats.value,
        monthlyTrends: monthlyTrends.value,
        subCityDistribution: subCityDistribution.value,
        recommended: recommended.value,
        upcomingAppointmentsList: upcomingAppointmentsList.value
      }
    }

    try {
      const res = await cachedGet(ENDPOINTS.BUYER.DASHBOARD, {
        skipCache: force,
        ttl: 60000,
        swr: true
      })

      const data = res?.data || res || {}
      if (data.stats) stats.value = { ...stats.value, ...data.stats }
      if (data.monthlyTrends) monthlyTrends.value = data.monthlyTrends
      if (data.subCityDistribution) subCityDistribution.value = data.subCityDistribution
      if (data.recommended) recommended.value = data.recommended
      if (data.upcomingAppointmentsList) upcomingAppointmentsList.value = data.upcomingAppointmentsList

      lastFetched.value = Date.now()
      return data
    } catch (error) {
      // Return existing state or default safe baseline
      return {
        stats: stats.value,
        monthlyTrends: monthlyTrends.value,
        subCityDistribution: subCityDistribution.value,
        recommended: recommended.value,
        upcomingAppointmentsList: upcomingAppointmentsList.value
      }
    } finally {
      isLoading.value = false
    }
  }

  function incrementSavedProperties() {
    stats.value.savedProperties = (stats.value.savedProperties || 0) + 1
    invalidateCache()
  }

  function decrementSavedProperties() {
    stats.value.savedProperties = Math.max(0, (stats.value.savedProperties || 1) - 1)
    invalidateCache()
  }

  function setSavedProperties(count) {
    stats.value.savedProperties = Math.max(0, count)
    invalidateCache()
  }

  function invalidateCache() {
    lastFetched.value = null
    clearApiCache('/buyer/dashboard')
    clearApiCache('/buyer/reports')
  }

  return {
    stats,
    monthlyTrends,
    subCityDistribution,
    recommended,
    upcomingAppointmentsList,
    isLoading,
    fetchDashboardData,
    incrementSavedProperties,
    decrementSavedProperties,
    setSavedProperties,
    invalidateCache
  }
})
