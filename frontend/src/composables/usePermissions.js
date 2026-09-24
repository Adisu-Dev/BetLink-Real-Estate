import { computed } from 'vue'
import { useAuth } from './useAuth'

export function usePermissions() {
  const { user, hasRole } = useAuth()

  // Permission definitions for each role
  const rolePermissions = {
    admin: [
      'view_all_users',
      'manage_users',
      'view_all_properties',
      'manage_properties',
      'approve_properties',
      'reject_properties',
      'manage_verification',
      'view_all_appointments',
      'manage_appointments',
      'view_all_payments',
      'manage_payments',
      'view_all_subscriptions',
      'manage_subscriptions',
      'view_reports',
      'manage_reports',
      'view_reviews',
      'manage_reviews',
      'view_all_messages',
      'manage_blogs',
      'manage_faqs',
      'view_analytics',
      'manage_system_settings',
      'view_activity_logs'
    ],
    owner: [
      'view_own_properties',
      'create_property',
      'edit_own_property',
      'delete_own_property',
      'manage_property_images',
      'view_own_appointments',
      'manage_own_appointments',
      'view_own_inquiries',
      'send_messages',
      'view_own_messages',
      'view_own_analytics',
      'manage_subscription',
      'request_verification'
    ],
    buyer: [
      'view_properties',
      'save_property',
      'compare_properties',
      'save_search',
      'book_appointment',
      'view_own_appointments',
      'send_messages',
      'view_own_messages',
      'book_short_stay',
      'view_own_bookings',
      'write_review',
      'view_own_reviews'
    ],
    agent: [
      'view_own_listings',
      'create_listing',
      'edit_own_listing',
      'delete_own_listing',
      'manage_listing_images',
      'view_leads',
      'manage_leads',
      'view_own_appointments',
      'manage_own_appointments',
      'send_messages',
      'view_own_messages',
      'view_own_analytics',
      'request_verification',
      'view_own_reviews'
    ]
  }

  // Check if user has a specific permission
  const hasPermission = (permission) => {
    if (!user.value) return false
    const userPermissions = rolePermissions[user.value.role] || []
    return userPermissions.includes(permission)
  }

  // Check if user has any of the specified permissions
  const hasAnyPermission = (permissions) => {
    if (!Array.isArray(permissions)) return false
    return permissions.some(permission => hasPermission(permission))
  }

  // Check if user has all of the specified permissions
  const hasAllPermissions = (permissions) => {
    if (!Array.isArray(permissions)) return false
    return permissions.every(permission => hasPermission(permission))
  }

  // Get all permissions for current user
  const getUserPermissions = computed(() => {
    if (!user.value) return []
    return rolePermissions[user.value.role] || []
  })

  // Check if user can access a route
  const canAccessRoute = (route) => {
    if (!user.value) return false

    // Public routes
    const publicRoutes = ['/', '/properties', '/agents', '/blog', '/about', '/contact', '/faqs']
    if (publicRoutes.includes(route)) return true

    // Auth routes (only for non-authenticated)
    const authRoutes = ['/login', '/register', '/forgot-password', '/reset-password']
    if (authRoutes.includes(route)) return !user.value

    // Role-based dashboard routes
    const routeRole = route.split('/')[1] // Extract role from route like /admin/dashboard
    return user.value.role === routeRole
  }

  // Role-specific action permissions
  const canManageProperties = computed(() => {
    return hasAnyPermission(['manage_properties', 'edit_own_property', 'edit_own_listing'])
  })

  const canApproveProperties = computed(() => {
    return hasPermission('approve_properties')
  })

  const canViewAllUsers = computed(() => {
    return hasPermission('view_all_users')
  })

  const canManageUsers = computed(() => {
    return hasPermission('manage_users')
  })

  const canViewAnalytics = computed(() => {
    return hasAnyPermission(['view_analytics', 'view_own_analytics'])
  })

  const canManageVerification = computed(() => {
    return hasPermission('manage_verification')
  })

  const canRequestVerification = computed(() => {
    return hasPermission('request_verification')
  })

  const canViewAllPayments = computed(() => {
    return hasPermission('view_all_payments')
  })

  const canManageSystemSettings = computed(() => {
    return hasPermission('manage_system_settings')
  })

  // Property-specific permissions
  const canCreateProperty = computed(() => {
    return hasAnyPermission(['create_property', 'create_listing'])
  })

  const canEditProperty = (propertyOwnerId) => {
    if (hasPermission('manage_properties')) return true
    if (hasAnyPermission(['edit_own_property', 'edit_own_listing'])) {
      return user.value && user.value.id === propertyOwnerId
    }
    return false
  }

  const canDeleteProperty = (propertyOwnerId) => {
    if (hasPermission('manage_properties')) return true
    if (hasAnyPermission(['delete_own_property', 'delete_own_listing'])) {
      return user.value && user.value.id === propertyOwnerId
    }
    return false
  }

  // Appointment permissions
  const canBookAppointment = computed(() => {
    return hasPermission('book_appointment')
  })

  const canManageAppointment = (appointmentOwnerId) => {
    if (hasPermission('manage_appointments')) return true
    if (hasAnyPermission(['manage_own_appointments'])) {
      return user.value && user.value.id === appointmentOwnerId
    }
    return false
  }

  // Message permissions
  const canSendMessage = computed(() => {
    return hasPermission('send_messages')
  })

  // Review permissions
  const canWriteReview = computed(() => {
    return hasPermission('write_review')
  })

  return {
    hasPermission,
    hasAnyPermission,
    hasAllPermissions,
    getUserPermissions,
    canAccessRoute,
    // Computed permissions
    canManageProperties,
    canApproveProperties,
    canViewAllUsers,
    canManageUsers,
    canViewAnalytics,
    canManageVerification,
    canRequestVerification,
    canViewAllPayments,
    canManageSystemSettings,
    canCreateProperty,
    canEditProperty,
    canDeleteProperty,
    canBookAppointment,
    canManageAppointment,
    canSendMessage,
    canWriteReview
  }
}
