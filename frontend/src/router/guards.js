/**
 * BetLink Navigation Guards
 */

export function setupGuards(router) {
  router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('betlink_auth_token')
    let user = null

    try {
      const userStr = localStorage.getItem('betlink_user')
      if (userStr) user = JSON.parse(userStr)
    } catch {
      user = null
    }

    // Set page title
    if (to.meta.title) {
      document.title = `${to.meta.title} | BetLink Real Estate`
    } else {
      document.title = 'BetLink - Ethiopian Real Estate Platform'
    }

    // Robust user roles extraction
    let userRoles = []
    if (Array.isArray(user?.roles)) {
      userRoles = user.roles.map(r => (typeof r === 'string' ? r : r?.name)).filter(Boolean)
    }
    if (user?.role) {
      const roleStr = typeof user.role === 'string' ? user.role : user.role?.name
      if (roleStr && !userRoles.includes(roleStr)) {
        userRoles.push(roleStr)
      }
    }
    if (userRoles.length === 0 && user) {
      userRoles = ['buyer']
    }

    // Role alias expansion
    const isBuyer = userRoles.some(r => ['buyer', 'buyer_tenant', 'buyer / tenant', 'buyer / renter', 'buyer_renter'].includes(r?.toLowerCase()))
    if (isBuyer) {
      ['buyer', 'buyer_tenant', 'Buyer / Tenant', 'buyer / tenant'].forEach(alias => {
        if (!userRoles.includes(alias)) userRoles.push(alias)
      })
    }
    const isOwner = userRoles.some(r => ['owner', 'property owner', 'property_owner'].includes(r?.toLowerCase()))
    if (isOwner) {
      ['owner', 'property owner', 'property_owner'].forEach(alias => {
        if (!userRoles.includes(alias)) userRoles.push(alias)
      })
    }
    // Unified Dual-Role User Architecture: Regular users can seamlessly act as both Buyer and Owner
    const isRegularUser = userRoles.some(r => ['buyer', 'owner', 'renter', 'buyer_tenant', 'buyer / tenant'].includes(r?.toLowerCase()))
    if (isRegularUser) {
      ['buyer', 'owner', 'buyer_tenant'].forEach(alias => {
        if (!userRoles.includes(alias)) userRoles.push(alias)
      })
    }
    const isAgent = userRoles.some(r => ['agent', 'real estate agent', 'real_estate_agent'].includes(r?.toLowerCase()))
    if (isAgent) {
      ['agent', 'real estate agent', 'real_estate_agent'].forEach(alias => {
        if (!userRoles.includes(alias)) userRoles.push(alias)
      })
    }

    const isAuthenticated = Boolean(token && user)

    // Check if route requires authentication
    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth)

    if (requiresAuth) {
      if (!isAuthenticated) {
        return next({
          name: 'login',
          query: { redirect: to.fullPath },
        })
      }

      // Check role permissions across matched route records
      const matchedWithRoles = to.matched.filter(
        (record) => record.meta.roles || record.meta.role
      )

      for (const record of matchedWithRoles) {
        const requiredRoles = record.meta.roles
          ? record.meta.roles
          : [record.meta.role]

        const hasRequiredRole = requiredRoles.some((role) =>
          userRoles.map(r => String(r).toLowerCase()).includes(String(role).toLowerCase())
        ) || userRoles.map(r => String(r).toLowerCase()).includes('admin')

        if (!hasRequiredRole) {
          return next({ name: 'unauthorized' })
        }
      }
    }

    // Check if route is for guests only (e.g. login, register, forgot-password)
    const isGuestOnly = to.matched.some((record) => record.meta.guest)

    if (isGuestOnly && isAuthenticated) {
      let dashboardRoute = '/buyer/dashboard'
      if (userRoles.includes('admin')) dashboardRoute = '/admin/dashboard'
      else if (userRoles.includes('owner')) dashboardRoute = '/owner/dashboard'
      else if (userRoles.includes('agent')) dashboardRoute = '/agent/dashboard'

      return next(dashboardRoute)
    }

    next()
  })
}

export default setupGuards
