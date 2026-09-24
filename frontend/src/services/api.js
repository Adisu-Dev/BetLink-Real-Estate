import axios from 'axios'
import { API_BASE_URL } from '../config/api'

// In-memory Request Deduplication & SWR Response Cache
const inFlightRequests = new Map()
const responseCache = new Map()
const DEFAULT_CACHE_TTL = 30 * 1000 // 30 seconds

// Create Axios Instance with Snappy Timeout
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Accept': 'application/json',
  },
  timeout: 15000, // 15s to allow sufficient time for local database queries without timeout
})

// Request Interceptor: Attach Bearer Token & Handle Content-Type
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('betlink_auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }

    // If data is FormData, let browser/Axios set multipart/form-data with boundary
    if (config.data instanceof FormData) {
      delete config.headers['Content-Type']
    } else if (!config.headers['Content-Type']) {
      config.headers['Content-Type'] = 'application/json'
    }

    return config
  },
  (error) => Promise.reject(error)
)

// Response Interceptor: Status Code Normalization & 401 Redirect Loop Prevention
apiClient.interceptors.response.use(
  (response) => {
    // 200, 201, 204: Return standard payload from backend ApiResponse trait
    return response.data
  },
  (error) => {
    const status = error.response ? error.response.status : null
    const responseData = error.response ? error.response.data : null

    // Determine error message from backend ApiResponse or HTTP standard
    let message = responseData?.message || error.message || 'An unexpected error occurred'
    const errors = responseData?.errors || null

    if (status === 401) {
      // Clear invalid authentication tokens and state
      localStorage.removeItem('betlink_auth_token')
      localStorage.removeItem('betlink_user')

      const currentPath = window.location.pathname
      const isAuthPage = currentPath.includes('/login') || currentPath.includes('/register') || currentPath.includes('/forgot-password') || currentPath.includes('/reset-password')

      if (!isAuthPage) {
        // Redirect to login preserving original intended destination
        const redirectUrl = encodeURIComponent(window.location.pathname + window.location.search)
        window.location.href = `/login?redirect=${redirectUrl}`
      }
    } else if (status === 403) {
      message = responseData?.message || 'You do not have permission to perform this action.'
    } else if (status === 404) {
      message = responseData?.message || 'The requested resource was not found.'
    } else if (status === 409) {
      message = responseData?.message || 'A conflict occurred with the current state of the resource.'
    } else if (status === 422) {
      message = responseData?.message || 'Validation failed. Please check the form errors.'
    } else if (status === 429) {
      message = responseData?.message || 'Too many requests. Please try again later.'
    } else if (status >= 500) {
      message = responseData?.message || 'Server error. Our engineers have been notified.'
    }

    const formattedError = {
      success: false,
      status,
      message,
      errors,
      raw: error,
    }

    return Promise.reject(formattedError)
  }
)

/**
 * Enhanced Cached GET request wrapper
 * - In-flight deduplication: Simultaneous identical requests share the same promise.
 * - SWR Caching: Returns cached result in 0ms, refreshing in background if expired.
 */
export async function cachedGet(url, config = {}) {
  const cacheKey = `GET:${url}:${JSON.stringify(config.params || {})}`
  const now = Date.now()
  const ttl = config.ttl !== undefined ? config.ttl : DEFAULT_CACHE_TTL

  // 1. Check if valid in-memory cache exists
  if (!config.skipCache && responseCache.has(cacheKey)) {
    const cached = responseCache.get(cacheKey)
    if (now - cached.timestamp < ttl) {
      return cached.data
    }
  }

  // 2. Check if identical request is already currently in flight
  if (inFlightRequests.has(cacheKey)) {
    return inFlightRequests.get(cacheKey)
  }

  // 3. Dispatch fresh request with deduplication
  const requestPromise = apiClient.get(url, config)
    .then((data) => {
      responseCache.set(cacheKey, { data, timestamp: Date.now() })
      inFlightRequests.delete(cacheKey)
      return data
    })
    .catch((err) => {
      inFlightRequests.delete(cacheKey)
      throw err
    })

  inFlightRequests.set(cacheKey, requestPromise)
  return requestPromise
}

/**
 * Clear memory cache (called on mutation / logout)
 */
export function clearApiCache(urlPattern = null) {
  if (!urlPattern) {
    responseCache.clear()
    inFlightRequests.clear()
    return
  }
  for (const key of responseCache.keys()) {
    if (key.includes(urlPattern)) {
      responseCache.delete(key)
    }
  }
}

export default apiClient
