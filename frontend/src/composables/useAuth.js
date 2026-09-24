import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useToastStore } from '../stores/toast'

export function useAuth() {
  const authStore = useAuthStore()
  const toastStore = useToastStore()
  const router = useRouter()

  const user = computed(() => authStore.user)
  const token = computed(() => authStore.token)
  const isAuthenticated = computed(() => authStore.isAuthenticated)
  const isLoading = computed(() => authStore.isLoading)

  const login = async (credentials) => {
    return await authStore.login(credentials)
  }

  const register = async (userData) => {
    return await authStore.register(userData)
  }

  const sendOtp = async (data) => {
    return await authStore.sendOtp(data)
  }

  const verifyOtp = async (data) => {
    return await authStore.verifyOtp(data)
  }

  const logout = async () => {
    await authStore.logout()
    toastStore.success('Successfully logged out!')
    if (router) {
      await router.push('/login')
    } else {
      window.location.href = '/login'
    }
  }

  const hasRole = (role) => {
    if (!authStore.user) return false
    const roles = Array.isArray(authStore.user.roles)
      ? authStore.user.roles
      : (authStore.user.role ? [authStore.user.role] : [])
    if (Array.isArray(role)) {
      return role.some((r) => roles.includes(r))
    }
    return roles.includes(role)
  }

  const getDashboardRoute = () => {
    return authStore.dashboardRoute
  }

  const checkAuth = () => {
    return authStore.isAuthenticated
  }

  const initAuth = async () => {
    return await authStore.init()
  }

  const verifyToken = async () => {
    const res = await authStore.fetchMe()
    return !!res
  }

  const updatePassword = async (data) => {
    return await authStore.updatePassword(data)
  }

  return {
    user,
    token,
    isAuthenticated,
    isLoading,
    login,
    register,
    sendOtp,
    verifyOtp,
    logout,
    hasRole,
    getDashboardRoute,
    checkAuth,
    initAuth,
    verifyToken,
    updatePassword,
    authStore,
  }
}

