import { defineStore } from 'pinia'
import { ref } from 'vue'
import { cachedGet, clearApiCache } from '../services/api'
import { ENDPOINTS } from '../config/api'

export const usePropertyStore = defineStore('property', () => {
  const properties = ref([])
  const featuredProperties = ref([])
  const propertyDetailsMap = ref(new Map())
  const isLoading = ref(false)
  const isFeaturedLoading = ref(false)
  const lastFetched = ref(null)
  const totalCount = ref(0)
  const activeFilters = ref({
    listing_type: 'all',
    type: 'all',
    sub_city: 'all',
    bedrooms: 'all',
    min_price: null,
    max_price: null,
    search: '',
  })

  // Cache duration: 45 seconds
  const CACHE_TTL = 45 * 1000

  async function fetchProperties(params = {}, force = false) {
    const isDefault = Object.keys(params).length === 0
    const now = Date.now()

    // Serve cached default list if fresh
    if (!force && isDefault && properties.value.length > 0 && lastFetched.value && (now - lastFetched.value < CACHE_TTL)) {
      return { data: properties.value, total: totalCount.value }
    }

    isLoading.value = true
    try {
      const response = await cachedGet(ENDPOINTS.PROPERTIES.LIST, {
        params,
        skipCache: force,
        ttl: 45000
      })

      const list = response?.data?.data || response?.data || response || []
      const total = response?.data?.total || response?.total || list.length

      if (isDefault) {
        properties.value = list
        totalCount.value = total
        lastFetched.value = Date.now()
      }

      return { data: list, total }
    } catch (error) {
      console.error('Failed to fetch properties from server:', error)
      return { data: [], total: 0 }
    } finally {
      isLoading.value = false
    }
  }

  async function fetchFeatured(force = false) {
    if (!force && featuredProperties.value.length > 0) {
      return featuredProperties.value
    }

    isFeaturedLoading.value = true
    try {
      const response = await cachedGet(ENDPOINTS.PROPERTIES.FEATURED, {
        skipCache: force,
        ttl: 60000
      })
      const list = response?.data?.data || response?.data || response || []
      featuredProperties.value = list
      return featuredProperties.value
    } catch (error) {
      console.error('Failed to fetch featured properties:', error)
      featuredProperties.value = []
      return []
    } finally {
      isFeaturedLoading.value = false
    }
  }

  async function fetchPropertyBySlug(slugOrId, force = false) {
    const key = String(slugOrId).toLowerCase()
    if (!force && propertyDetailsMap.value.has(key)) {
      return propertyDetailsMap.value.get(key)
    }

    try {
      const response = await cachedGet(ENDPOINTS.PROPERTIES.DETAIL(slugOrId), {
        skipCache: force,
        ttl: 60000
      })
      const prop = response?.data?.data || response?.data || response
      if (prop) {
        propertyDetailsMap.value.set(key, prop)
        return prop
      }
    } catch (error) {
      console.error('Failed to fetch property details:', error)
    }
    return null
  }

  function invalidateCache() {
    lastFetched.value = null
    propertyDetailsMap.value.clear()
    clearApiCache('/properties')
  }

  return {
    properties,
    featuredProperties,
    isLoading,
    isFeaturedLoading,
    totalCount,
    activeFilters,
    fetchProperties,
    fetchFeatured,
    fetchPropertyBySlug,
    invalidateCache
  }
})
