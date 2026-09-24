import { useAuth } from './useAuth'
import { useToast } from './useToast'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api/v1'

export function useHttp() {
  const { logout } = useAuth()
  const { showError, showSuccess } = useToast()

  /**
   * Make an HTTP request with proper headers and error handling
   */
  async function request(endpoint, options = {}) {
    const token = localStorage.getItem('betlink_auth_token')
    const method = options.method || 'GET'
    const headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      ...(token && { 'Authorization': `Bearer ${token}` }),
      ...options.headers,
    }

    const config = {
      method,
      headers,
    }

    // Add body for non-GET requests
    if (options.body && method !== 'GET') {
      config.body = typeof options.body === 'string' 
        ? options.body 
        : JSON.stringify(options.body)
    }

    try {
      const response = await fetch(`${API_BASE_URL}${endpoint}`, config)

      // Handle response
      let data
      const contentType = response.headers.get('content-type')
      
      if (contentType && contentType.includes('application/json')) {
        data = await response.json()
      } else {
        data = await response.text()
      }

      // Handle errors
      if (!response.ok) {
        // If unauthorized, logout user
        if (response.status === 401) {
          logout()
          return { success: false, error: 'Session expired. Please login again.' }
        }

        // Extract error message
        const errorMessage = 
          data?.message || 
          data?.error || 
          data?.errors?.[0]?.message ||
          'An error occurred. Please try again.'

        return {
          success: false,
          error: errorMessage,
          status: response.status,
          data
        }
      }

      return {
        success: true,
        data: data?.data || data,
        message: data?.message,
        status: response.status
      }
    } catch (error) {
      console.error('HTTP Error:', error)
      return {
        success: false,
        error: 'Network error. Please check your connection.',
        status: 0
      }
    }
  }

  return {
    /**
     * GET request
     */
    get: (endpoint, options = {}) => 
      request(endpoint, { ...options, method: 'GET' }),

    /**
     * POST request
     */
    post: (endpoint, body, options = {}) => 
      request(endpoint, { ...options, method: 'POST', body }),

    /**
     * PUT request
     */
    put: (endpoint, body, options = {}) => 
      request(endpoint, { ...options, method: 'PUT', body }),

    /**
     * PATCH request
     */
    patch: (endpoint, body, options = {}) => 
      request(endpoint, { ...options, method: 'PATCH', body }),

    /**
     * DELETE request
     */
    delete: (endpoint, options = {}) => 
      request(endpoint, { ...options, method: 'DELETE' }),

    /**
     * Upload file (FormData)
     */
    uploadFile: async (endpoint, formData, options = {}) => {
      const token = localStorage.getItem('betlink_auth_token')
      const headers = {
        'Accept': 'application/json',
        ...(token && { 'Authorization': `Bearer ${token}` }),
        ...options.headers,
      }

      try {
        const response = await fetch(`${API_BASE_URL}${endpoint}`, {
          method: 'POST',
          headers,
          body: formData,
        })

        const data = await response.json()

        if (!response.ok) {
          if (response.status === 401) {
            logout()
            return { success: false, error: 'Session expired. Please login again.' }
          }

          const errorMessage = data?.message || data?.error || 'Upload failed'
          return { success: false, error: errorMessage, data }
        }

        return { success: true, data: data?.data || data }
      } catch (error) {
        console.error('Upload Error:', error)
        return { success: false, error: 'Upload failed. Please try again.' }
      }
    }
  }
}
