<template>
  <div class="space-y-5">
    <!-- Loading State -->
    <div v-if="isLoading" class="p-12 text-center text-slate-400">
      <div class="inline-flex items-center gap-2">
        <span class="w-4 h-4 rounded-full border-2 border-emerald-500 border-t-transparent animate-spin"></span>
        Loading live analytics from database...
      </div>
    </div>

    <template v-else>
      <!-- Key Metrics -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs">
          <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold mb-1">Total Users</p>
          <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ stats.total_users ?? 0 }}</p>
          <p class="text-xs text-emerald-600 mt-2 font-medium">+{{ stats.new_users_this_month ?? 0 }} registered this month</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs">
          <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold mb-1">Active Listings</p>
          <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ stats.active_properties ?? 0 }}</p>
          <p class="text-xs text-emerald-600 mt-2 font-medium">{{ stats.pending_properties ?? 0 }} pending approval</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs">
          <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold mb-1">Total Appointments</p>
          <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ stats.total_appointments ?? 0 }}</p>
          <p class="text-xs text-blue-600 mt-2 font-medium">Tour bookings scheduled</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs">
          <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold mb-1">Sales Portfolio Value</p>
          <p class="text-2xl font-bold text-emerald-600">ETB {{ formatCurrency(stats.sales_analytics?.total_sales_portfolio || 0) }}</p>
          <p class="text-xs text-slate-400 mt-2">Active sale listings sum</p>
        </div>
      </div>

      <!-- User Growth & Monthly Listings Charts -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Monthly Listings Chart -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Monthly Listing Creation</h3>
            <span class="text-[10px] uppercase font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">Past 6 Months</span>
          </div>
          <div class="flex items-end gap-2 h-44 border-b border-slate-100 dark:border-slate-800 pb-2">
            <div v-for="(bar, i) in monthlyListings" :key="i" class="flex-1 flex flex-col items-center gap-1.5">
              <span class="text-[10px] font-bold text-slate-700 dark:text-slate-300">{{ bar.value }}</span>
              <div class="w-full flex items-end justify-center h-32">
                <div
                  class="w-full max-w-[32px] rounded-t bg-emerald-500 hover:bg-emerald-600 transition-all duration-300"
                  :style="{ height: `${Math.min(100, Math.max(12, (bar.value / Math.max(1, maxListings)) * 100))}%` }"
                ></div>
              </div>
              <span class="text-[10px] text-slate-400 font-medium">{{ bar.month }}</span>
            </div>
          </div>
        </div>

        <!-- Inventory by Status -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
          <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Inventory Breakdown by Status</h3>
          <div class="space-y-3.5">
            <div v-for="(count, status) in (stats.properties_by_status || {})" :key="status" class="space-y-1">
              <div class="flex justify-between text-xs font-semibold text-slate-700 dark:text-slate-300">
                <span class="capitalize">{{ status }}</span>
                <span>{{ count }} ({{ Math.round((count / Math.max(1, stats.total_properties)) * 100) }}%)</span>
              </div>
              <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                <div 
                  class="h-full bg-slate-800 dark:bg-slate-200 rounded-full"
                  :style="{ width: `${(count / Math.max(1, stats.total_properties)) * 100}%` }"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- User Distribution by Role -->
      <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">User Distribution by Role</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div v-for="role in userRoles" :key="role.label" class="text-center p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ role.count }}</p>
            <p class="text-xs text-slate-600 dark:text-slate-300 font-semibold mt-1">{{ role.label }}</p>
            <p class="text-[10px] text-slate-400">{{ role.percentage }}% of community</p>
          </div>
        </div>
      </div>

      <!-- Revenue & Portfolio Breakdown -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs">
          <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold mb-1">Total Sales Portfolio</p>
          <p class="text-xl font-bold text-emerald-600">ETB {{ formatCurrency(stats.sales_analytics?.total_sales_portfolio || 0) }}</p>
          <p class="text-[10px] text-slate-400 mt-1">Average sale: ETB {{ formatCurrency(stats.sales_analytics?.avg_sale_price || 0) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs">
          <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold mb-1">Total Rental Portfolio</p>
          <p class="text-xl font-bold text-blue-600">ETB {{ formatCurrency(stats.sales_analytics?.total_rental_portfolio || 0) }}</p>
          <p class="text-[10px] text-slate-400 mt-1">Average rent: ETB {{ formatCurrency(stats.sales_analytics?.avg_rental_price || 0) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs">
          <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold mb-1">Closed Real Estate Deals</p>
          <p class="text-xl font-bold text-purple-600">{{ stats.sales_analytics?.closed_deals_count || 0 }} Completed</p>
          <p class="text-[10px] text-slate-400 mt-1">{{ stats.sales_analytics?.active_deals_count || 0 }} currently active on market</p>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { adminService } from '@/services/adminService'
import { useToastStore } from '@/stores/toast'

const toastStore = useToastStore()
const isLoading = ref(true)
const stats = ref({})

const fetchAnalytics = async () => {
  isLoading.value = true
  try {
    const res = await adminService.getDashboard()
    stats.value = res.data || res || {}
  } catch (err) {
    toastStore.error('Failed to load analytics: ' + (err.message || 'Error'))
  } finally {
    isLoading.value = false
  }
}

const monthlyListings = computed(() => {
  return stats.value.monthly_listings || []
})

const maxListings = computed(() => {
  const values = monthlyListings.value.map(m => m.value)
  return Math.max(1, ...values)
})

const userRoles = computed(() => {
  const roles = stats.value.users_by_role || {}
  const total = Math.max(1, (roles.buyers || 0) + (roles.owners || 0) + (roles.agents || 0) + (roles.admins || 0))
  return [
    { label: 'Buyers & Renters', count: roles.buyers || 0, percentage: Math.round(((roles.buyers || 0) / total) * 100) },
    { label: 'Property Owners', count: roles.owners || 0, percentage: Math.round(((roles.owners || 0) / total) * 100) },
    { label: 'Real Estate Agents', count: roles.agents || 0, percentage: Math.round(((roles.agents || 0) / total) * 100) },
    { label: 'System Admins', count: roles.admins || 0, percentage: Math.round(((roles.admins || 0) / total) * 100) },
  ]
})

const formatCurrency = (val) => {
  if (!val) return '0'
  if (val >= 1000000) {
    return (val / 1000000).toFixed(2) + 'M'
  }
  if (val >= 1000) {
    return (val / 1000).toFixed(1) + 'K'
  }
  return Number(val).toLocaleString()
}

onMounted(() => {
  fetchAnalytics()
})
</script>
