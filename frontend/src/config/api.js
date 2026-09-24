/**
 * BetLink API Configuration & Endpoints
 */

export const API_BASE_URL = import.meta.env.VITE_API_URL || import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api/v1'

export const ENDPOINTS = {
  // Auth
  AUTH: {
    REGISTER: '/auth/register',
    SEND_OTP: '/auth/send-otp',
    VERIFY_OTP: '/auth/verify-otp',
    LOGIN: '/auth/login',
    LOGOUT: '/auth/logout',
    ME: '/auth/me',
    UPDATE_PASSWORD: '/auth/password',
    FORGOT_PASSWORD: '/auth/forgot-password',
    RESET_PASSWORD: '/auth/reset-password',
  },

  // Public Properties & Search
  PROPERTIES: {
    LIST: '/properties',
    FEATURED: '/properties/featured',
    DETAIL: (slug) => `/properties/${slug}`,
    TYPES: '/property-types',
    CATEGORIES: '/categories',
    AMENITIES: '/amenities',
    REVIEWS: (propertyId) => `/properties/${propertyId}/reviews`,
    AVAILABILITY: (propertyId) => `/properties/${propertyId}/availability`,
  },

  SEARCH: {
    BASIC: '/search',
    MAP: '/search/map',
    SUGGESTIONS: '/search/suggestions',
  },

  LOCATIONS: {
    CITIES: '/locations/cities',
    SUB_CITIES: (cityId) => `/locations/cities/${cityId}/sub-cities`,
    NEIGHBORHOODS: (subCityId) => `/locations/sub-cities/${subCityId}/neighborhoods`,
  },

  // Authenticated User Features
  PROFILE: {
    GET: '/profile',
    UPDATE: '/profile',
    AVATAR: '/profile/avatar',
    DELETE: '/profile',
    PUBLIC: (userId) => `/users/${userId}`,
  },

  NOTIFICATIONS: {
    LIST: '/notifications',
    UNREAD_COUNT: '/notifications/unread-count',
    MARK_READ: (id) => `/notifications/${id}/read`,
    MARK_ALL_READ: '/notifications/read-all',
  },

  FAVORITES: {
    LIST: '/favorites',
    ADD: (propertyId) => `/favorites/${propertyId}`,
    REMOVE: (propertyId) => `/favorites/${propertyId}`,
    CHECK: (propertyId) => `/favorites/${propertyId}/check`,
  },

  APPOINTMENTS: {
    LIST: '/appointments',
    CREATE: '/appointments',
    DETAIL: (id) => `/appointments/${id}`,
    CONFIRM: (id) => `/appointments/${id}/confirm`,
    CANCEL: (id) => `/appointments/${id}/cancel`,
    COMPLETE: (id) => `/appointments/${id}/complete`,
    OWNER_LIST: '/owner/appointments',
    OWNER_CALENDAR: '/owner/appointments/calendar',
  },

  CONVERSATIONS: {
    LIST: '/conversations',
    CREATE: '/conversations',
    DETAIL: (id) => `/conversations/${id}`,
    MARK_READ: (id) => `/conversations/${id}/read`,
    READ: (id) => `/conversations/${id}/read`,
    MESSAGES: (id) => `/conversations/${id}/messages`,
    SEND_MESSAGE: (id) => `/conversations/${id}/messages`,
  },

  BOOKINGS: {
    LIST: '/bookings',
    CREATE: '/bookings',
    DETAIL: (id) => `/bookings/${id}`,
    CANCEL: (id) => `/bookings/${id}/cancel`,
  },

  REVIEWS: {
    CREATE: (propertyId) => `/properties/${propertyId}/reviews`,
    UPDATE: (id) => `/reviews/${id}`,
    DELETE: (id) => `/reviews/${id}`,
    RESPOND: (id) => `/reviews/${id}/response`,
  },

  REPORTS: {
    CREATE: '/reports',
  },

  // Owner Property Operations
  OWNER: {
    DASHBOARD: '/owner/dashboard',
    PROPERTIES: '/owner/properties',
    PROPERTY_DETAIL: (id) => `/owner/properties/${id}`,
    CREATE_PROPERTY: '/owner/properties',
    UPDATE_PROPERTY: (id) => `/owner/properties/${id}`,
    DELETE_PROPERTY: (id) => `/owner/properties/${id}`,
    PUBLISH_PROPERTY: (id) => `/owner/properties/${id}/publish`,
    UPLOAD_IMAGES: (id) => `/owner/properties/${id}/images`,
    DELETE_IMAGE: (propertyId, imageId) => `/owner/properties/${propertyId}/images/${imageId}`,
    SET_PRIMARY_IMAGE: (propertyId, imageId) => `/owner/properties/${propertyId}/images/${imageId}/primary`,
    REORDER_IMAGES: (propertyId) => `/owner/properties/${propertyId}/images/reorder`,
  },

  // Admin Endpoints
  ADMIN: {
    DASHBOARD: '/admin/dashboard',
    USERS: '/admin/users',
    USER_DETAIL: (id) => `/admin/users/${id}`,
    SUSPEND_USER: (id) => `/admin/users/${id}/suspend`,
    BAN_USER: (id) => `/admin/users/${id}/ban`,
    ACTIVATE_USER: (id) => `/admin/users/${id}/activate`,
    DELETE_USER: (id) => `/admin/users/${id}`,
    PROPERTIES: '/admin/properties',
    PROPERTY_DETAIL: (id) => `/admin/properties/${id}`,
    APPROVE_PROPERTY: (id) => `/admin/properties/${id}/approve`,
    REJECT_PROPERTY: (id) => `/admin/properties/${id}/reject`,
    FEATURE_PROPERTY: (id) => `/admin/properties/${id}/feature`,
    DELETE_PROPERTY: (id) => `/admin/properties/${id}`,
    REPORTS: '/admin/reports',
    UPDATE_REPORT: (id) => `/admin/reports/${id}`,
    VERIFICATIONS: '/admin/verifications',
    APPROVE_VERIFICATION: (id) => `/admin/verifications/${id}/approve`,
    REJECT_VERIFICATION: (id) => `/admin/verifications/${id}/reject`,
    ANALYTICS_PROPERTIES: '/admin/analytics/properties',
    ANALYTICS_USERS: '/admin/analytics/users',
    SETTINGS: '/admin/settings',
    UPDATE_SETTINGS: '/admin/settings',
  },

  // Buyer Endpoints
  BUYER: {
    DASHBOARD: '/buyer/dashboard',
    SEARCH: '/buyer/search',
    PROPERTIES: '/buyer/properties',
  },

  // Owner Endpoints
  OWNER: {
    DASHBOARD: '/owner/dashboard',
    PROPERTIES: '/owner/properties',
    APPOINTMENTS: '/owner/appointments',
  },

  // Agent Endpoints
  AGENT: {
    DASHBOARD: '/agent/dashboard',
    LEADS: '/agent/leads',
    UPDATE_LEAD_STATUS: (id) => `/agent/leads/${id}/status`,
    PROPERTIES: '/agent/properties',
    ANALYTICS: '/agent/analytics',
    VERIFICATIONS: '/agent/verifications',
    PUBLIC_AGENTS: '/agents',
  },

  // Blogs & Public Pages
  BLOGS: {
    LIST: '/blogs',
    CATEGORIES: '/blogs/categories',
    DETAIL: (slug) => `/blogs/${slug}`,
  },

  CONTACT: {
    SEND: '/contact',
    FAQS: '/faqs',
  },
}
