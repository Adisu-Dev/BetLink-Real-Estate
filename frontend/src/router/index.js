import { createRouter, createWebHistory } from 'vue-router'
import { setupGuards } from './guards.js'

// Layouts
const PublicLayout = () => import('../layouts/PublicLayout.vue')
const DashboardLayout = () => import('../layouts/DashboardLayout.vue')

// Public Pages
const Home = () => import('../pages/Home.vue')
const PropertiesPage = () => import('../pages/public/PropertiesPage.vue')
const PropertyDetails = () => import('../pages/public/PropertyDetails.vue')
const SearchPage = () => import('../pages/property/SearchPage.vue')
const AgentsPage = () => import('../pages/public/AgentsPage.vue')
const BlogPage = () => import('../pages/public/BlogPage.vue')
const AboutPage = () => import('../pages/public/AboutPage.vue')
const ContactPage = () => import('../pages/public/ContactPage.vue')
const FaqsPage = () => import('../pages/public/FaqsPage.vue')
const CategoriesPage = () => import('../pages/public/CategoriesPage.vue')
const LocationsPage = () => import('../pages/public/LocationsPage.vue')
const PricingPage = () => import('../pages/public/PricingPage.vue')
const PrivacyPage = () => import('../pages/public/PrivacyPage.vue')
const TermsPage = () => import('../pages/public/TermsPage.vue')

// Auth Pages
const Login = () => import('../pages/auth/Login.vue')
const Register = () => import('../pages/auth/Register.vue')
const ForgotPassword = () => import('../pages/auth/ForgotPassword.vue')
const ResetPassword = () => import('../pages/auth/ResetPassword.vue')

// Dashboard Pages - Common
const DashboardHome = () => import('../pages/dashboard/DashboardHome.vue')
const ProfilePage = () => import('../pages/dashboard/ProfilePage.vue')
const SettingsPage = () => import('../pages/dashboard/SettingsPage.vue')
const AppointmentsPage = () => import('../pages/dashboard/AppointmentsPage.vue')
const MessagesPage = () => import('../pages/dashboard/MessagesPage.vue')
const ReviewsPage = () => import('../pages/dashboard/ReviewsPage.vue')
const DashboardNotFound = () => import('../pages/dashboard/DashboardNotFound.vue')

// Buyer Pages
const BuyerPropertiesPage = () => import('../pages/dashboard/BuyerPropertiesPage.vue')
const FavoritesPage = () => import('../pages/dashboard/FavoritesPage.vue')
const SavedSearchesPage = () => import('../pages/dashboard/SavedSearchesPage.vue')
const BookingsPage = () => import('../pages/dashboard/BookingsPage.vue')
const BuyerReportsPage = () => import('../pages/dashboard/BuyerReportsPage.vue')

// Owner Pages
const OwnerDashboard = () => import('../pages/dashboard/OwnerDashboard.vue')
const OwnerPropertiesPage = () => import('../pages/dashboard/OwnerPropertiesPage.vue')
const PropertyForm = () => import('../pages/property/PropertyForm.vue')
const OwnerInquiriesPage = () => import('../pages/dashboard/OwnerInquiriesPage.vue')
const OwnerReportsPage = () => import('../pages/dashboard/OwnerReportsPage.vue')
const OwnerAnalyticsPage = () => import('../pages/dashboard/OwnerAnalyticsPage.vue')
const OwnerVerificationPage = () => import('../pages/dashboard/OwnerVerificationPage.vue')

// Agent Pages
const AgentDashboard = () => import('../pages/dashboard/AgentDashboard.vue')
const AgentPropertiesPage = () => import('../pages/dashboard/AgentPropertiesPage.vue')
const AgentLeadsPage = () => import('../pages/dashboard/AgentLeadsPage.vue')
const AgentReportsPage = () => import('../pages/dashboard/AgentReportsPage.vue')
const AgentAnalyticsPage = () => import('../pages/dashboard/AgentAnalyticsPage.vue')
const AgentVerificationPage = () => import('../pages/dashboard/AgentVerificationPage.vue')

// Admin Pages
const AdminDashboard = () => import('../pages/dashboard/AdminDashboard.vue')
const AdminUsersPage = () => import('../pages/dashboard/AdminUsersPage.vue')
const AdminPropertiesPage = () => import('../pages/dashboard/AdminPropertiesPage.vue')
const AdminReportsPage = () => import('../pages/dashboard/AdminReportsPage.vue')
const AdminVerificationsPage = () => import('../pages/dashboard/AdminVerificationsPage.vue')
const AdminAnalyticsPage = () => import('../pages/dashboard/AdminAnalyticsPage.vue')
const AdminSettingsPage = () => import('../pages/dashboard/AdminSettingsPage.vue')

// Error Pages
const Unauthorized = () => import('../pages/Unauthorized.vue')

const publicRoutes = [
  {
    path: '/',
    component: PublicLayout,
    children: [
      { path: '', name: 'home', component: Home, meta: { title: 'Ethiopian Real Estate Platform' } },
      { path: 'properties', name: 'properties', component: PropertiesPage, meta: { title: 'Browse Properties' } },
      { path: 'properties/:id', name: 'property-details', component: PropertyDetails, meta: { title: 'Property Details' } },
      { path: 'search', name: 'search', component: SearchPage, meta: { title: 'Search Properties' } },
      { path: 'agents', name: 'agents', component: AgentsPage, meta: { title: 'Verified Agents' } },
      { path: 'blog', name: 'blog', component: BlogPage, meta: { title: 'Real Estate Insights' } },
      { path: 'categories', name: 'categories', component: CategoriesPage, meta: { title: 'Property Categories' } },
      { path: 'locations', name: 'locations', component: LocationsPage, meta: { title: 'Explore Locations' } },
      { path: 'about', name: 'about', component: AboutPage, meta: { title: 'About Us' } },
      { path: 'contact', name: 'contact', component: ContactPage, meta: { title: 'Contact Support' } },
      { path: 'faqs', name: 'faqs', component: FaqsPage, meta: { title: 'Frequently Asked Questions' } },
      { path: 'pricing', name: 'pricing', component: PricingPage, meta: { title: 'Pricing & Plans' } },
      { path: 'privacy', name: 'privacy', component: PrivacyPage, meta: { title: 'Privacy Policy' } },
      { path: 'terms', name: 'terms', component: TermsPage, meta: { title: 'Terms of Service' } },
      { path: 'login', name: 'login', component: Login, meta: { guest: true, title: 'Sign In' } },
      { path: 'register', name: 'register', component: Register, meta: { guest: true, title: 'Create Account' } },
      { path: 'forgot-password', name: 'forgot-password', component: ForgotPassword, meta: { guest: true, title: 'Forgot Password' } },
      { path: 'reset-password', name: 'reset-password', component: ResetPassword, meta: { guest: true, title: 'Reset Password' } },
    ]
  }
]

const authRoutes = []

const buyerRoutes = [
  { 
    path: 'dashboard',
    name: 'buyer-dashboard',
    component: DashboardHome,
    meta: { title: 'Dashboard' }
  },
  { 
    path: 'properties',
    name: 'buyer-properties',
    component: BuyerPropertiesPage,
    meta: { title: 'Properties' }
  },
  { 
    path: 'properties/:id',
    name: 'buyer-property-details',
    component: PropertyDetails,
    meta: { title: 'Property Details' }
  },
  { 
    path: 'favorites',
    name: 'buyer-favorites',
    component: FavoritesPage,
    meta: { title: 'Favorites' }
  },
  { 
    path: 'saved-searches',
    name: 'buyer-saved-searches',
    component: SavedSearchesPage,
    meta: { title: 'Saved Searches' }
  },
  { 
    path: 'appointments',
    name: 'buyer-appointments',
    component: AppointmentsPage,
    meta: { title: 'Appointments' }
  },
  { 
    path: 'messages',
    name: 'buyer-messages',
    component: MessagesPage,
    meta: { title: 'Messages' }
  },
  { 
    path: 'reports',
    name: 'buyer-reports',
    component: BuyerReportsPage,
    meta: { title: 'Reports' }
  },
  { 
    path: 'bookings',
    name: 'buyer-bookings',
    component: BookingsPage,
    meta: { title: 'Bookings' }
  },
  { 
    path: 'reviews',
    name: 'buyer-reviews',
    component: ReviewsPage,
    meta: { title: 'Reviews' }
  },
  { 
    path: 'profile',
    name: 'buyer-profile',
    component: ProfilePage,
    meta: { title: 'Profile' }
  },
  { 
    path: 'settings',
    name: 'buyer-settings',
    component: SettingsPage,
    meta: { title: 'Settings' }
  },
]

const ownerRoutes = [
  { 
    path: 'dashboard',
    name: 'owner-dashboard',
    component: OwnerDashboard,
    meta: { title: 'Dashboard' }
  },
  { 
    path: 'properties',
    name: 'owner-properties',
    component: OwnerPropertiesPage,
    meta: { title: 'Properties' }
  },
  { 
    path: 'properties/create',
    name: 'owner-properties-create',
    redirect: '/owner/properties?add=1',
  },
  { 
    path: 'properties/:id',
    name: 'owner-property-details',
    component: PropertyDetails,
    meta: { title: 'Property Details' }
  },
  { 
    path: 'properties/:id/edit',
    name: 'owner-properties-edit',
    component: PropertyForm,
    meta: { title: 'Edit Property' }
  },
  { 
    path: 'appointments',
    name: 'owner-appointments',
    component: AppointmentsPage,
    meta: { title: 'Appointments' }
  },
  { 
    path: 'inquiries',
    name: 'owner-inquiries',
    component: OwnerInquiriesPage,
    meta: { title: 'Inquiries' }
  },
  { 
    path: 'messages',
    name: 'owner-messages',
    component: MessagesPage,
    meta: { title: 'Messages' }
  },
  { 
    path: 'reports',
    name: 'owner-reports',
    component: OwnerReportsPage,
    meta: { title: 'Reports' }
  },
  { 
    path: 'analytics',
    name: 'owner-analytics',
    component: OwnerAnalyticsPage,
    meta: { title: 'Analytics' }
  },
  { 
    path: 'verification',
    name: 'owner-verification',
    component: OwnerVerificationPage,
    meta: { title: 'Verification' }
  },
  { 
    path: 'profile',
    name: 'owner-profile',
    component: ProfilePage,
    meta: { title: 'Profile' }
  },
  { 
    path: 'settings',
    name: 'owner-settings',
    component: SettingsPage,
    meta: { title: 'Settings' }
  },
]

const agentRoutes = [
  { 
    path: 'dashboard',
    name: 'agent-dashboard',
    component: AgentDashboard,
    meta: { title: 'Dashboard' }
  },
  { 
    path: 'properties',
    name: 'agent-properties',
    component: AgentPropertiesPage,
    meta: { title: 'Properties' }
  },
  { 
    path: 'properties/:id',
    name: 'agent-property-details',
    component: PropertyDetails,
    meta: { title: 'Property Details' }
  },
  { 
    path: 'leads',
    name: 'agent-leads',
    component: AgentLeadsPage,
    meta: { title: 'Leads' }
  },
  { 
    path: 'appointments',
    name: 'agent-appointments',
    component: AppointmentsPage,
    meta: { title: 'Appointments' }
  },
  { 
    path: 'messages',
    name: 'agent-messages',
    component: MessagesPage,
    meta: { title: 'Messages' }
  },
  { 
    path: 'reports',
    name: 'agent-reports',
    component: AgentReportsPage,
    meta: { title: 'Reports' }
  },
  { 
    path: 'analytics',
    name: 'agent-analytics',
    component: AgentAnalyticsPage,
    meta: { title: 'Analytics' }
  },
  { 
    path: 'verification',
    name: 'agent-verification',
    component: AgentVerificationPage,
    meta: { title: 'Verification' }
  },
  { 
    path: 'profile',
    name: 'agent-profile',
    component: ProfilePage,
    meta: { title: 'Profile' }
  },
  { 
    path: 'reviews',
    name: 'agent-reviews',
    component: ReviewsPage,
    meta: { title: 'Reviews' }
  },
  { 
    path: 'settings',
    name: 'agent-settings',
    component: SettingsPage,
    meta: { title: 'Settings' }
  },
]

const adminRoutes = [
  { 
    path: 'dashboard',
    name: 'admin-dashboard',
    component: AdminDashboard,
    meta: { title: 'Dashboard' }
  },
  { 
    path: 'users', 
    alias: 'management',
    name: 'admin-users', 
    component: AdminUsersPage, 
    meta: { title: 'Users & Management' } 
  },
  { 
    path: 'properties',
    name: 'admin-properties',
    component: AdminPropertiesPage,
    meta: { title: 'Properties' }
  },
  { 
    path: 'properties/:id',
    name: 'admin-property-details',
    component: PropertyDetails,
    meta: { title: 'Property Details' }
  },
  { 
    path: 'appointments',
    name: 'admin-appointments',
    component: AppointmentsPage,
    meta: { title: 'Appointments' }
  },
  { 
    path: 'reports',
    name: 'admin-reports',
    component: AdminReportsPage,
    meta: { title: 'Reports' }
  },
  { 
    path: 'verifications',
    name: 'admin-verifications',
    component: AdminVerificationsPage,
    meta: { title: 'Verifications' }
  },
  { 
    path: 'messages',
    name: 'admin-messages',
    component: MessagesPage,
    meta: { title: 'Messages' }
  },
  { 
    path: 'analytics',
    redirect: { name: 'admin-dashboard' },
  },
  { 
    path: 'settings',
    name: 'admin-settings',
    component: AdminSettingsPage,
    meta: { title: 'Settings' }
  },
  { 
    path: 'profile',
    name: 'admin-profile',
    component: ProfilePage,
    meta: { title: 'Profile' }
  },
  {
    path: ':pathMatch(.*)*',
    component: DashboardNotFound,
    meta: { title: 'Not Found' }
  }
]

const dashboardRoutes = [
  {
    path: '/buyer',
    component: DashboardLayout,
    meta: { requiresAuth: true, role: 'buyer' },
    children: buyerRoutes,
    redirect: '/buyer/dashboard'
  },
  {
    path: '/owner',
    component: DashboardLayout,
    meta: { requiresAuth: true, role: 'owner' },
    children: ownerRoutes,
    redirect: '/owner/dashboard'
  },
  {
    path: '/agent',
    component: DashboardLayout,
    meta: { requiresAuth: true, role: 'agent' },
    children: agentRoutes,
    redirect: '/agent/dashboard'
  },
  {
    path: '/admin',
    component: DashboardLayout,
    meta: { requiresAuth: true, role: 'admin' },
    children: adminRoutes,
    redirect: '/admin/dashboard'
  },
]

const errorRoutes = [
  { 
    path: '/403', 
    name: 'unauthorized', 
    component: Unauthorized,
    meta: { title: 'Unauthorized' }
  },
  { 
    path: '/:pathMatch(.*)*', 
    name: 'not-found', 
    redirect: '/' 
  }
]

const routes = [
  ...publicRoutes,
  ...authRoutes,
  ...dashboardRoutes,
  ...errorRoutes,
]

if (typeof window !== 'undefined' && 'scrollRestoration' in window.history) {
  window.history.scrollRestoration = 'manual'
}

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (to.hash) return { el: to.hash, behavior: 'smooth' }
    return { top: 0, left: 0 }
  }
})

router.afterEach((to) => {
  if (!to.hash) {
    window.scrollTo(0, 0)
  }
})

// Attach navigation guards
setupGuards(router)

export default router
