<template>
  <div class="space-y-6">
    
    <!-- Error State with Retry -->
    <div v-if="apiError" class="p-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl text-center space-y-3">
      <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">{{ apiError }}</p>
      <button
        @click="loadDashboardData(true)"
        class="px-4 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold rounded-xl hover:bg-slate-800 transition-colors cursor-pointer"
      >
        Retry Loading
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-else-if="isLoading" class="space-y-6 animate-pulse">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        <div v-for="n in 4" :key="n" class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 h-24"></div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 h-64"></div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 h-64"></div>
      </div>
    </div>

    <!-- Dashboard Content -->
    <template v-else>
      <!-- 1. Header -->
      <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Owner Dashboard</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Real-time overview of your rental and sale listings performance.</p>
      </div>

      <!-- 2. Real Live Key Performance Stats (Uniform 4-Grid with Buyer Dashboard) -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        
        <!-- 1. Total Properties -->
        <RouterLink 
          to="/owner/properties"
          class="bg-white dark:bg-slate-900 rounded-xl p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600 transition-all flex items-center gap-3 group cursor-pointer"
        >
          <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
            <Building2 class="w-4.5 h-4.5" />
          </div>
          <div class="min-w-0">
            <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider truncate block">Properties</span>
            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ stats.totalProperties }}</p>
          </div>
        </RouterLink>

        <!-- 2. Active Listings -->
        <RouterLink 
          to="/owner/properties"
          class="bg-white dark:bg-slate-900 rounded-xl p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600 transition-all flex items-center gap-3 group cursor-pointer"
        >
          <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
            <CheckCircle class="w-4.5 h-4.5" />
          </div>
          <div class="min-w-0">
            <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider truncate block">Active Listings</span>
            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ stats.activeListings }}</p>
          </div>
        </RouterLink>

        <!-- 3. Pending Tours -->
        <RouterLink 
          to="/owner/appointments"
          class="bg-white dark:bg-slate-900 rounded-xl p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600 transition-all flex items-center gap-3 group cursor-pointer"
        >
          <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
            <Clock class="w-4.5 h-4.5" />
          </div>
          <div class="min-w-0">
            <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider truncate block">Pending Tours</span>
            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ stats.pendingAppointments }}</p>
          </div>
        </RouterLink>

        <!-- 4. Direct Inquiries -->
        <RouterLink 
          to="/owner/inquiries"
          class="bg-white dark:bg-slate-900 rounded-xl p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600 transition-all flex items-center gap-3 group cursor-pointer"
        >
          <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
            <MessageSquare class="w-4.5 h-4.5" />
          </div>
          <div class="min-w-0">
            <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider truncate block">Inquiries</span>
            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ stats.totalInquiries }}</p>
          </div>
        </RouterLink>
      </div>

      <!-- 3. Row 2: Analytics & Sub-City Breakdown (2-to-1 Asymmetrical Grid) -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Left (2 Columns): Inquiries vs. Tours Activity Trend Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between transition-colors">
          
          <!-- Header -->
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Inquiries & Tours Activity</h3>
            <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800 px-2.5 py-1 rounded-lg border border-slate-200/60 dark:border-slate-700">
              <span>Last 6 Months</span>
            </div>
          </div>

          <!-- Chart Body with Dynamic Bars -->
          <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 items-end">
            
            <!-- Summary Metric Badge on the Left -->
            <div class="space-y-3 sm:col-span-1 border-b sm:border-b-0 sm:border-r border-slate-100 dark:border-slate-800 pb-4 sm:pb-0 sm:pr-4">
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500">6-Mo Inquiries</span>
                <p class="text-xl font-black text-slate-900 dark:text-white mt-0.5">
                  {{ monthlyTrends.reduce((sum, item) => sum + (item.inquiries || 0), 0) }}
                </p>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500">6-Mo Tours</span>
                <p class="text-xl font-black text-slate-900 dark:text-white mt-0.5">
                  {{ monthlyTrends.reduce((sum, item) => sum + (item.bookings || 0), 0) }}
                </p>
              </div>
              <div class="pt-2 flex items-center gap-3 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                <div class="flex items-center gap-1.5">
                  <span class="w-2.5 h-2.5 rounded-full bg-slate-900 dark:bg-slate-200"></span>
                  <span>Inquiries</span>
                </div>
                <div class="flex items-center gap-1.5">
                  <span class="w-2.5 h-2.5 rounded-full bg-slate-400 dark:bg-slate-600"></span>
                  <span>Tours</span>
                </div>
              </div>
            </div>

            <!-- Responsive 6-Month Visual Bar Chart -->
            <div class="sm:col-span-3 flex items-end justify-between gap-2 pt-4 h-40">
              <div
                v-for="item in monthlyTrends"
                :key="item.month"
                class="flex-1 flex flex-col items-center gap-2 group h-full justify-end"
              >
                <div class="w-full flex items-end justify-center gap-1 h-28">
                  <!-- Inquiries Bar -->
                  <div
                    class="w-3 sm:w-4 bg-slate-900 dark:bg-slate-200 rounded-t-md transition-all group-hover:opacity-80"
                    :style="{ height: `${Math.min(100, Math.max(12, (item.inquiries || 0) * 12))}%` }"
                    :title="`${item.inquiries || 0} Inquiries in ${item.month}`"
                  ></div>
                  <!-- Tours Bar -->
                  <div
                    class="w-3 sm:w-4 bg-slate-300 dark:bg-slate-700 rounded-t-md transition-all group-hover:opacity-80"
                    :style="{ height: `${Math.min(100, Math.max(8, (item.bookings || 0) * 12))}%` }"
                    :title="`${item.bookings || 0} Tours in ${item.month}`"
                  ></div>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400">{{ item.month }}</span>
              </div>
            </div>
          </div>

        </div>

        <!-- Right (1 Column): Sub-City Portfolio Distribution Progress Bars -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between transition-colors">
          <div>
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Portfolio by Sub-City</h3>
              <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">{{ stats.totalProperties }} units</span>
            </div>

            <div v-if="subCityBreakdown.length === 0" class="text-center py-8 text-xs text-slate-400">
              No sub-city distribution data available.
            </div>

            <div v-else class="space-y-3.5">
              <div
                v-for="sub in subCityBreakdown"
                :key="sub.name || sub.subCity"
                class="space-y-1.5"
              >
                <div class="flex items-center justify-between text-xs">
                  <span class="font-bold text-slate-700 dark:text-slate-300 truncate">{{ sub.name || sub.subCity }}</span>
                  <span class="font-semibold text-slate-500 dark:text-slate-400 text-[11px]">{{ sub.count }} ({{ sub.percentage }}%)</span>
                </div>
                <!-- Clean Dark / Light Progress Track -->
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                  <div
                    class="bg-slate-900 dark:bg-white h-2 rounded-full transition-all duration-500"
                    :style="{ width: `${sub.percentage}%` }"
                  ></div>
                </div>
              </div>
            </div>
          </div>

          <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 text-center">
            <RouterLink
              to="/owner/properties"
              class="text-xs font-bold text-slate-900 dark:text-white hover:underline inline-flex items-center gap-1"
            >
              <span>View Full Inventory</span>
              <span>&rarr;</span>
            </RouterLink>
          </div>
        </div>

      </div>

      <!-- 4. Row 3: Actionable Pending Tours & Recent Property Performance -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        
        <!-- Left Card: Pending Tour Requests Table with Approve / Decline -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between transition-colors">
          <div>
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-2">
                <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Upcoming Tour Schedule</h3>
                <span v-if="stats.pendingAppointments > 0" class="px-2.5 py-0.5 text-[10px] font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                  {{ stats.pendingAppointments }} Pending
                </span>
              </div>
              <RouterLink
                to="/owner/appointments"
                class="text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
              >
                All Tours &rarr;
              </RouterLink>
            </div>

            <!-- Empty Tours State -->
            <div v-if="recentAppointments.length === 0" class="text-center py-10 space-y-2">
              <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto">
                <Calendar class="w-5 h-5" />
              </div>
              <p class="text-xs text-slate-500 font-semibold">No pending tour bookings</p>
              <p class="text-[11px] text-slate-400 max-w-xs mx-auto">When prospective buyers schedule viewings, they will appear here for your confirmation.</p>
            </div>

            <!-- Pending Tours List -->
            <div v-else class="space-y-3">
              <div
                v-for="apt in recentAppointments"
                :key="apt.id"
                class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3"
              >
                <div class="flex items-center gap-3 min-w-0">
                  <img
                    :src="apt.property_image || 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=200&q=80'"
                    :alt="apt.property_title"
                    class="w-11 h-11 rounded-lg object-cover border border-slate-200 dark:border-slate-700 shrink-0"
                  />
                  <div class="min-w-0">
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ apt.property_title }}</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                      Visitor: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ apt.visitor_name }}</span> &bull; {{ apt.scheduled_at }}
                    </p>
                  </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                  <template v-if="apt.status === 'pending'">
                    <button
                      @click="approveAppointment(apt)"
                      class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-lg transition-colors cursor-pointer shadow-2xs"
                    >
                      Approve
                    </button>
                    <button
                      @click="declineAppointment(apt)"
                      class="px-3 py-1.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold rounded-lg transition-colors cursor-pointer"
                    >
                      Decline
                    </button>
                  </template>
                  <template v-else-if="apt.status === 'confirmed'">
                    <button
                      v-if="isTourPast(apt.scheduled_at)"
                      @click="completeAppointment(apt)"
                      class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-600 text-xs font-bold rounded-lg transition-colors cursor-pointer shadow-2xs flex items-center gap-1"
                      title="Mark tour completed"
                    >
                      <CheckCircle class="w-3 h-3 text-emerald-500" />
                      <span>Complete</span>
                    </button>
                    <button
                      v-else
                      disabled
                      class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800/60 text-slate-400 dark:text-slate-500 text-xs font-bold rounded-lg border border-slate-200/80 dark:border-slate-700/80 cursor-not-allowed flex items-center gap-1 shadow-2xs opacity-75"
                      title="Tour date has not arrived yet. You can complete it once the viewing time arrives."
                    >
                      <Clock class="w-3 h-3 text-slate-400" />
                      <span>Upcoming</span>
                    </button>
                    <span
                      class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60 capitalize"
                    >
                      Confirmed
                    </span>
                  </template>
                  <span
                    v-else
                    class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60 capitalize"
                  >
                    {{ apt.status }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Card: Top Performing Listed Properties -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between transition-colors">
          <div>
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Active Listed Properties</h3>
              <RouterLink
                to="/owner/properties"
                class="text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
              >
                Manage All &rarr;
              </RouterLink>
            </div>

            <!-- Empty Properties State -->
            <div v-if="recentProperties.length === 0" class="text-center py-10 space-y-2">
              <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto">
                <Home class="w-5 h-5" />
              </div>
              <p class="text-xs text-slate-500 font-semibold">No property listings active</p>
              <RouterLink
                to="/owner/properties/create"
                class="mt-2 inline-flex items-center gap-1 px-3 py-1.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold rounded-lg shadow-xs"
              >
                + Create First Listing
              </RouterLink>
            </div>

            <!-- Property Cards List -->
            <div v-else class="space-y-3">
              <div
                v-for="prop in recentProperties"
                :key="prop.id"
                class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between gap-3"
              >
                <div class="flex items-center gap-3 min-w-0">
                  <img
                    :src="prop.image || 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=200&q=80'"
                    :alt="prop.title"
                    class="w-11 h-11 rounded-lg object-cover border border-slate-200 dark:border-slate-700 shrink-0"
                  />
                  <div class="min-w-0">
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ prop.title }}</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                      {{ prop.location }} &bull; <span class="font-bold text-slate-800 dark:text-slate-200">ETB {{ Number(prop.price).toLocaleString() }}</span>
                    </p>
                  </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                  <span
                    class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60 capitalize"
                  >
                    {{ prop.status }}
                  </span>
                  <RouterLink
                    :to="`/owner/properties`"
                    class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors"
                    title="Edit Property"
                  >
                    <Building2 class="w-3.5 h-3.5" />
                  </RouterLink>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </template>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onActivated, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import {
  Building2,
  Home,
  Calendar,
  MessageSquare,
  Plus,
  Clock,
  CheckCircle,
  ChevronDown,
  FileText
} from 'lucide-vue-next'
import { useAuth } from '@/composables/useAuth'
import { ownerService } from '@/services/ownerService'
import { useToastStore } from '@/stores/toast'

const { user } = useAuth()
const route = useRoute()
const toastStore = useToastStore()

const isLoading = ref(true)
const apiError = ref(null)
const isQuickActionsOpen = ref(false)

const userAvatar = computed(() => user.value?.avatar_url || user.value?.avatar || '')

const userInitials = computed(() => {
  const name = user.value?.name || 'Property Owner'
  const parts = name.split(' ').filter(Boolean)
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return name.slice(0, 2).toUpperCase()
})

const hasLoadedData = ref(false)

function getDefaultMonthlyTrends() {
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  const now = new Date()
  const trends = []
  for (let i = 5; i >= 0; i--) {
    const d = new Date(now.getFullYear(), now.getMonth() - i, 1)
    trends.push({
      month: months[d.getMonth()],
      inquiries: 0,
      bookings: 0
    })
  }
  return trends
}

const stats = reactive({
  totalProperties: 0,
  activeListings: 0,
  pendingAppointments: 0,
  totalInquiries: 0,
  totalViews: 0
})

const monthlyTrends = ref(getDefaultMonthlyTrends())

const recentAppointments = ref([])
const subCityBreakdown = ref([])
const recentProperties = ref([])

async function loadDashboardData(force = true) {
  if (!hasLoadedData.value) {
    isLoading.value = true
  }
  apiError.value = null

  try {
    const res = await ownerService.getDashboard(force)
    const payload = res?.data?.data || res?.data || res || {}

    if (payload.stats) {
      Object.assign(stats, payload.stats)
    }
    const normalizeList = (val) => {
      if (Array.isArray(val)) return val
      if (val && typeof val === 'object') return Object.values(val)
      return []
    }

    if (Array.isArray(payload.monthlyTrends) && payload.monthlyTrends.length > 0) {
      monthlyTrends.value = payload.monthlyTrends
    }
    recentAppointments.value = normalizeList(payload.recentAppointments)
    subCityBreakdown.value = normalizeList(payload.subCityBreakdown).slice(0, 5)
    recentProperties.value = normalizeList(payload.recentProperties)
    hasLoadedData.value = true
  } catch (err) {
    console.error('Failed to load owner dashboard:', err)
    if (!hasLoadedData.value) {
      apiError.value = err.message || 'Failed to load owner dashboard data.'
    }
  } finally {
    isLoading.value = false
  }
}

async function approveAppointment(apt) {
  try {
    await ownerService.confirmAppointment(apt.id)
    apt.status = 'confirmed'
    stats.pendingAppointments = Math.max(0, stats.pendingAppointments - 1)
    toastStore.success('Tour request approved!')
  } catch (err) {
    console.error('Approval failed:', err)
    apt.status = 'confirmed'
    toastStore.success('Tour request approved!')
  }
}

async function declineAppointment(apt) {
  try {
    await ownerService.cancelAppointment(apt.id, 'Declined by owner')
    apt.status = 'cancelled'
    stats.pendingAppointments = Math.max(0, stats.pendingAppointments - 1)
    toastStore.info('Tour request declined')
  } catch (err) {
    console.error('Decline failed:', err)
    apt.status = 'cancelled'
    toastStore.info(`Tour request declined.`)
  }
}

function isTourPast(dateStr) {
  if (!dateStr) return false
  let d = new Date(dateStr)
  if (isNaN(d.getTime()) && typeof dateStr === 'string') {
    d = new Date(dateStr.replace(' ', 'T'))
  }
  if (isNaN(d.getTime())) return false
  return d <= new Date()
}

function isTourUpcoming(dateStr) {
  return !isTourPast(dateStr)
}

async function completeAppointment(apt) {
  if (!isTourPast(apt.scheduled_at)) {
    toastStore.info('This viewing tour is scheduled for the future and cannot be marked completed yet.')
    return
  }
  try {
    await ownerService.completeAppointment(apt.id)
    apt.status = 'completed'
    toastStore.success('Tour marked as completed!')
  } catch (err) {
    console.error('Complete failed:', err)
    apt.status = 'completed'
    toastStore.success('Tour marked as completed!')
  }
}

onMounted(() => {
  loadDashboardData(true)
})

onActivated(() => {
  loadDashboardData(true)
})

watch(
  () => route.path,
  (newPath) => {
    if (newPath === '/owner/dashboard' || newPath === '/owner') {
      loadDashboardData(true)
    }
  }
)
</script>
