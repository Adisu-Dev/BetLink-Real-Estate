import { defineStore } from 'pinia'
import { authService } from '../services/authService'

export const useAuthStore = defineStore('auth', {
  state: () => {
    let storedUser = null
    try {
      const userStr = localStorage.getItem('betlink_user')
      if (userStr) storedUser = JSON.parse(userStr)
    } catch {
      localStorage.removeItem('betlink_user')
    }

    const storedToken = localStorage.getItem('betlink_auth_token') || null

    return {
      user: storedUser,
      token: storedToken,
      isAuthenticated: !!storedToken && !!storedUser,
      isLoading: false,
      isInitialized: false,
    }
  },

  getters: {
    roles: (state) => {
      if (Array.isArray(state.user?.roles)) return state.user.roles
      if (state.user?.role) return [state.user.role]
      return []
    },
    isAdmin: (state) => (state.user?.roles || []).includes('admin') || state.user?.role === 'admin',
    isOwner: (state) => (state.user?.roles || []).includes('owner') || state.user?.role === 'owner',
    isBuyer: (state) => (state.user?.roles || []).includes('buyer') || state.user?.role === 'buyer',
    isAgent: (state) => (state.user?.roles || []).includes('agent') || state.user?.role === 'agent',

    primaryRole: (state) => {
      const userRoles = Array.isArray(state.user?.roles) ? state.user.roles : (state.user?.role ? [state.user.role] : [])
      if (userRoles.includes('admin')) return 'admin'
      if (userRoles.includes('owner')) return 'owner'
      if (userRoles.includes('agent')) return 'agent'
      if (userRoles.includes('buyer')) return 'buyer'
      return 'buyer'
    },

    userName: (state) => state.user?.name || `${state.user?.first_name || ''} ${state.user?.last_name || ''}`.trim() || 'User',
    userAvatar: (state) => state.user?.avatar_url || state.user?.avatar || '',

    dashboardRoute: (state) => {
      const userRoles = Array.isArray(state.user?.roles) ? state.user.roles : (state.user?.role ? [state.user.role] : [])
      if (userRoles.includes('admin')) return '/admin/dashboard'
      if (userRoles.includes('owner')) return '/owner/dashboard'
      if (userRoles.includes('agent')) return '/agent/dashboard'
      return '/buyer/dashboard'
    },
  },

  actions: {
    async init() {
      if (this.isInitialized) return
      this.isInitialized = true

      try {
        const userStr = localStorage.getItem('betlink_user')
        const token = localStorage.getItem('betlink_auth_token')
        if (token && userStr) {
          this.token = token
          this.user = JSON.parse(userStr)
          this.isAuthenticated = true
          // Background sync to always keep avatar, profile, and roles fresh from DB
          this.fetchMe().catch(() => {})
        } else if (token) {
          await this.fetchMe()
        }
      } catch (err) {
        // quiet fallback
      }
    },

    /**
     * Set authentication state & persist to local storage
     */
    setAuth(userData, token) {
      if (!userData) return
      const roles = Array.isArray(userData.roles) ? userData.roles : (userData.role ? [userData.role] : ['buyer'])
      const role = userData.role || roles[0] || 'buyer'
      const normalizedUser = {
        ...userData,
        role,
        roles,
      }

      this.user = normalizedUser
      if (token) this.token = token
      this.isAuthenticated = true

      try {
        localStorage.setItem('betlink_user', JSON.stringify(normalizedUser))
        if (token) localStorage.setItem('betlink_auth_token', token)
      } catch (e) {
        console.warn('Storage quota exceeded, storing clean profile:', e)
        const safeUser = { ...normalizedUser }
        if (typeof safeUser.avatar === 'string' && safeUser.avatar.startsWith('data:')) {
          delete safeUser.avatar
        }
        if (typeof safeUser.avatar_url === 'string' && safeUser.avatar_url.startsWith('data:')) {
          delete safeUser.avatar_url
        }
        try {
          localStorage.setItem('betlink_user', JSON.stringify(safeUser))
          if (token) localStorage.setItem('betlink_auth_token', token)
        } catch {}
      }
    },

    /**
     * Clear authentication state & local storage
     */
    clearAuth() {
      this.user = null
      this.token = null
      this.isAuthenticated = false

      localStorage.removeItem('betlink_user')
      localStorage.removeItem('betlink_auth_token')
      localStorage.removeItem('betlink_remembered_accounts')
    },

    /**
     * Authenticate user with email and password
     */
    async login(credentials) {
      this.isLoading = true
      try {
        const response = await authService.login(credentials)
        if (response.success && response.data) {
          this.setAuth(response.data.user, response.data.token)
          return { success: true, user: response.data.user, token: response.data.token }
        }
        return { success: false, error: response.message || 'Invalid credentials' }
      } catch (err) {
        return { success: false, error: err.message || 'Login failed' }
      } finally {
        this.isLoading = false
      }
    },

    /**
     * Send OTP for registration verification
     */
    async sendOtp(data) {
      this.isLoading = true
      try {
        const response = await authService.sendOtp(data)
        return response
      } catch (err) {
        const errMsg = err.message || (err.errors ? Object.values(err.errors).flat().join(' ') : 'Failed to send OTP code')
        throw new Error(errMsg)
      } finally {
        this.isLoading = false
      }
    },

    /**
     * Verify OTP and activate account
     */
    async verifyOtp(data) {
      this.isLoading = true
      try {
        const response = await authService.verifyOtp(data)
        if (response.success && response.data) {
          if (response.data.token) {
            this.setAuth(response.data.user, response.data.token)
          }
          return {
            success: true,
            user: response.data.user,
            token: response.data.token,
            email: response.data.email,
            message: response.message || 'Account activated successfully!'
          }
        }
        return { success: false, error: response.message || 'Verification failed' }
      } catch (err) {
        const errMsg = err.message || (err.errors ? Object.values(err.errors).flat().join(' ') : 'Verification failed')
        return { success: false, error: errMsg }
      } finally {
        this.isLoading = false
      }
    },

    /**
     * Register a new user
     */
    async register(data) {
      this.isLoading = true
      try {
        const response = await authService.register(data)
        if (response.success && response.data) {
          if (response.data.token) {
            this.setAuth(response.data.user, response.data.token)
          }
          return {
            success: true,
            user: response.data.user,
            token: response.data.token,
            email: response.data.email || data.email,
            auto_generated: response.data.auto_generated,
            message: response.message || response.data.message || 'Registration successful! Check your email for login credentials.'
          }
        }
        return { success: false, error: response.message || 'Registration failed' }
      } catch (err) {
        const errMsg = err.message || (err.errors ? Object.values(err.errors).flat().join(' ') : 'Registration failed')
        return { success: false, error: errMsg, errors: err.errors }
      } finally {
        this.isLoading = false
      }
    },

    /**
     * Logout user
     */
    async logout() {
      this.isLoading = true
      try {
        if (this.token) {
          await authService.logout()
        }
      } catch (err) {
        console.warn('Backend logout encountered error, clearing client auth:', err)
      } finally {
        this.clearAuth()
        this.isLoading = false
      }
    },

    /**
     * Fetch authenticated user data from backend
     */
    async fetchMe() {
      if (!this.token) return null
      try {
        const response = await authService.me()
        if (response && response.success && response.data) {
          const userData = response.data
          const roles = Array.isArray(userData.roles) ? userData.roles : (userData.role ? [userData.role] : ['buyer'])
          const normalized = {
            ...userData,
            role: userData.role || roles[0] || 'buyer',
            roles,
          }
          this.user = normalized
          this.isAuthenticated = true
          localStorage.setItem('betlink_user', JSON.stringify(normalized))
          return normalized
        }
        // If response explicitly returned 401 unauthorized
        if (response && response.status === 401) {
          this.clearAuth()
          return null
        }
        // Otherwise retain existing user if already present
        return this.user
      } catch (err) {
        // Only clear if 401 unauthorized
        if (err?.status === 401 || err?.response?.status === 401) {
          this.clearAuth()
          return null
        }
        return this.user
      }
    },

    /**
     * Update user password
     */
    async updatePassword(data) {
      this.isLoading = true
      try {
        const response = await authService.updatePassword(data)
        if (response.success) {
          this.clearAuth()
          return { success: true, message: response.message || 'Password updated successfully. Please sign in again.' }
        }
        return { success: false, error: response.message || 'Password update failed' }
      } catch (err) {
        const errMsg = err.message || (err.errors ? Object.values(err.errors).flat().join(' ') : 'Password update failed')
        return { success: false, error: errMsg }
      } finally {
        this.isLoading = false
      }
    },
  },
})
