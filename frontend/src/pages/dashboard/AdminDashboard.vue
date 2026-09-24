<template>
  <div class="space-y-6">

    <!-- Error State with Retry -->
    <div v-if="apiError" class="p-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl text-center space-y-3">
      <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">{{ apiError }}</p>
      <button 
        @click="fetchAdminData" 
        class="px-4 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold rounded-xl hover:bg-slate-800 transition-colors shadow-xs cursor-pointer"
      >
        Retry Loading
      </button>
    </div>

    <!-- Loading Skeleton State -->
    <div v-else-if="isLoading" class="space-y-6">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        <div v-for="n in 4" :key="n" class="bg-white dark:bg-slate-900 rounded-xl p-4 border border-slate-200/80 dark:border-slate-800 animate-pulse h-24"></div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl p-6 h-64 border border-slate-200/80 dark:border-slate-800 animate-pulse"></div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 h-64 border border-slate-200/80 dark:border-slate-800 animate-pulse"></div>
      </div>
    </div>

    <template v-else>
      <!-- 2. System KPI Cards (Exact Uniform Design with Buyer Dashboard) -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        
        <!-- Card 1: Total Users -->
        <RouterLink 
          to="/admin/users"
          class="bg-white dark:bg-slate-900 rounded-xl p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600 transition-all flex items-center gap-3 group cursor-pointer"
        >
          <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center group-hover:scale-105 transition-transform shrink-0">
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
          </div>
          <div class="min-w-0">
            <p class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider truncate">Total Users</p>
            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ stats.total_users ?? 0 }}</p>
          </div>
        </RouterLink>

        <!-- Card 2: Active Listings -->
        <RouterLink 
          to="/admin/properties?status=active"
          class="bg-white dark:bg-slate-900 rounded-xl p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600 transition-all flex items-center gap-3 group cursor-pointer"
        >
          <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center group-hover:scale-105 transition-transform shrink-0">
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
          </div>
          <div class="min-w-0">
            <p class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider truncate">Active Properties</p>
            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ stats.active_properties ?? 0 }}</p>
          </div>
        </RouterLink>

        <!-- Card 3: Pending Verifications -->
        <RouterLink 
          to="/admin/verifications"
          class="bg-white dark:bg-slate-900 rounded-xl p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600 transition-all flex items-center gap-3 group cursor-pointer"
        >
          <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center group-hover:scale-105 transition-transform shrink-0">
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </div>
          <div class="min-w-0">
            <p class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider truncate">Verifications</p>
            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ stats.pending_verifications ?? 0 }}</p>
          </div>
        </RouterLink>

        <!-- Card 4: Reports & Moderation -->
        <RouterLink 
          to="/admin/reports"
          class="bg-white dark:bg-slate-900 rounded-xl p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600 transition-all flex items-center gap-3 group cursor-pointer"
        >
          <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center group-hover:scale-105 transition-transform shrink-0">
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
          <div class="min-w-0">
            <p class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider truncate">Safety Reports</p>
            <p class="text-lg sm:text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ stats.pending_reports ?? 0 }}</p>
          </div>
        </RouterLink>

      </div>

      <!-- 3. Quick Action Controls Toolbar -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-xs border border-slate-200/80 dark:border-slate-800 transition-colors">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-700 dark:text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            System Control Panel
          </h2>
          <span class="text-[11px] font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 rounded-full border border-slate-200 dark:border-slate-700">
            Live Database
          </span>
        </div>
        <div class="flex flex-wrap gap-2.5">
          <RouterLink
            to="/admin/users"
            class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 transition-colors shadow-xs"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Manage Users
          </RouterLink>

          <RouterLink
            to="/admin/properties"
            class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 transition-colors shadow-xs"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Moderate Properties
          </RouterLink>

          <RouterLink
            to="/admin/verifications"
            class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 transition-colors shadow-xs"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Review Verifications
          </RouterLink>

          <RouterLink
            to="/admin/reports"
            class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 transition-colors shadow-xs"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
            </svg>
            Fraud & Safety Reports
          </RouterLink>

          <RouterLink
            to="/admin/settings"
            class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 transition-colors shadow-xs"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            System Settings
          </RouterLink>
        </div>
      </div>

      <!-- 4. Charts Row: Monthly Listing Trends & Property Status Distribution (2-to-1 Asymmetrical Grid) -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Monthly Listing Trends (2 Cols) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 transition-colors flex flex-col justify-between">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">Monthly Listing Creation Trends</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Live volume of verified properties added over the past 6 months.</p>
            </div>
            <span class="text-[10px] font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700">
              Last 6 Months
            </span>
          </div>

          <!-- Bar Chart -->
          <div class="h-44 w-full flex items-end justify-between gap-3 pt-4 border-b border-slate-100 dark:border-slate-800">
            <div v-for="(m, idx) in monthlyListings" :key="idx" class="flex flex-col items-center gap-2 flex-1">
              <div class="w-full flex items-end justify-center h-32">
                <div 
                  class="w-5 sm:w-7 bg-slate-900 dark:bg-slate-100 rounded-t-md transition-all duration-500 hover:opacity-80" 
                  :style="{ height: `${Math.min(100, Math.max(12, (m.value / Math.max(1, maxMonthlyListing)) * 100))}%` }"
                  :title="`${m.month}: ${m.value} listings`"
                ></div>
              </div>
              <span class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold">{{ m.month }}</span>
              <span class="text-[11px] font-black text-slate-700 dark:text-slate-300">{{ m.value }}</span>
            </div>
          </div>
        </div>

        <!-- Property Status Distribution (1 Col) -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 transition-colors flex flex-col justify-between">
          <div>
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-1">Property Status Distribution</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-5">Breakdown of inventory currently on BetLink platform.</p>

            <div class="space-y-4">
              <div v-for="item in statusItems" :key="item.label">
                <div class="flex justify-between text-xs font-semibold mb-1.5 text-slate-700 dark:text-slate-300">
                  <span>{{ item.label }}</span>
                  <span class="font-bold text-slate-900 dark:text-white">{{ item.count }}</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden">
                  <div 
                    :class="item.color" 
                    class="h-full rounded-full transition-all duration-500" 
                    :style="{ width: `${item.percentage}%` }"
                  ></div>
                </div>
              </div>
            </div>
          </div>

          <div class="pt-4 border-t border-slate-100 dark:border-slate-800 mt-6 flex items-center justify-between text-xs text-slate-500">
            <span>Total Listings:</span>
            <span class="font-bold text-slate-900 dark:text-white text-sm">{{ stats.total_properties ?? 0 }}</span>
          </div>
        </div>
      </div>

      <!-- 5. Two Panel Row: Pending Verifications & Recent Reports -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <!-- Verification Requests -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-xs">
          <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-slate-900 dark:bg-slate-100"></span>
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">Pending Verification Requests</h3>
            </div>
            <RouterLink to="/admin/verifications" class="text-xs font-bold text-slate-900 dark:text-white hover:underline">
              View All &rarr;
            </RouterLink>
          </div>

          <div class="divide-y divide-slate-100 dark:divide-slate-800">
            <div 
              v-for="req in (stats.recent_verifications || [])" 
              :key="req.id" 
              class="flex items-center justify-between px-6 py-3.5 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors"
            >
              <div class="flex items-center gap-3">
                <img :src="req.avatar || 'https://ui-avatars.com/api/?name=User'" :alt="req.name" class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-slate-700" />
                <div>
                  <p class="text-xs font-bold text-slate-900 dark:text-white">{{ req.name }}</p>
                  <p class="text-[10px] text-slate-400">{{ req.type }} &bull; {{ req.created_at }}</p>
                </div>
              </div>
              <RouterLink
                to="/admin/verifications"
                class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-semibold shadow-xs transition-colors"
              >
                Review
              </RouterLink>
            </div>

            <div v-if="!stats.recent_verifications || stats.recent_verifications.length === 0" class="px-6 py-8 text-center text-xs text-slate-400">
              No pending verification requests at this time.
            </div>
          </div>
        </div>

        <!-- Recent Fraud Reports -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-xs">
          <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-slate-900 dark:bg-slate-100"></span>
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">Recent Fraud & Safety Reports</h3>
            </div>
            <RouterLink to="/admin/reports" class="text-xs font-bold text-slate-900 dark:text-white hover:underline">
              View All &rarr;
            </RouterLink>
          </div>

          <div class="divide-y divide-slate-100 dark:divide-slate-800">
            <div 
              v-for="rep in (stats.recent_reports || [])" 
              :key="rep.id" 
              class="flex items-center justify-between px-6 py-3.5 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors"
            >
              <div class="min-w-0 pr-3">
                <div class="flex items-center gap-2 mb-0.5">
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                    {{ rep.reason }}
                  </span>
                  <span class="text-[10px] text-slate-400">{{ rep.time }}</span>
                </div>
                <p class="text-xs text-slate-700 dark:text-slate-300 truncate">{{ rep.description }}</p>
              </div>
              <RouterLink
                to="/admin/reports"
                class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-semibold shadow-xs transition-colors shrink-0"
              >
                Inspect
              </RouterLink>
            </div>

            <div v-if="!stats.recent_reports || stats.recent_reports.length === 0" class="px-6 py-8 text-center text-xs text-slate-400">
              No recent fraud reports filed.
            </div>
          </div>
        </div>

      </div>

      <!-- 6. User Distribution by Role -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Platform User Community by Role</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 text-center">
            <p class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.users_by_role?.buyers ?? 0 }}</p>
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1">Buyers & Renters</p>
          </div>
          <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 text-center">
            <p class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.users_by_role?.owners ?? 0 }}</p>
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1">Property Owners</p>
          </div>
          <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 text-center">
            <p class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.users_by_role?.agents ?? 0 }}</p>
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1">Licensed Agents</p>
          </div>
          <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 text-center">
            <p class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.users_by_role?.admins ?? 0 }}</p>
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1">Platform Admins</p>
          </div>
        </div>
      </div>

    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuth } from '../../composables/useAuth'
import { adminService } from '../../services/adminService'

const { user } = useAuth()

const isLoading = ref(true)
const apiError = ref(null)

const userAvatar = computed(() => user.value?.avatar_url || user.value?.avatar || '')

const userInitials = computed(() => {
  const name = user.value?.name || 'BetLink Admin'
  const parts = name.split(' ').filter(Boolean)
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return name.slice(0, 2).toUpperCase()
})

const stats = ref({
  total_users: 0,
  total_properties: 0,
  active_properties: 0,
  pending_properties: 0,
  pending_verifications: 0,
  pending_reports: 0,
  total_appointments: 0,
  new_users_this_month: 0,
  new_properties_this_month: 0,
  users_by_role: { buyers: 0, owners: 0, agents: 0, admins: 0 },
  monthly_listings: [],
  recent_verifications: [],
  recent_reports: [],
  properties_by_status: {},
})

const monthlyListings = computed(() => stats.value.monthly_listings || [])
const maxMonthlyListing = computed(() => {
  const vals = monthlyListings.value.map(m => m.value)
  return vals.length > 0 ? Math.max(...vals) : 1
})

const statusItems = computed(() => {
  const total = Math.max(1, stats.value.total_properties || 1)
  const active = stats.value.active_properties || 0
  const pending = stats.value.pending_properties || 0
  const other = Math.max(0, total - active - pending)

  return [
    {
      label: 'Active & Verified',
      count: active,
      percentage: Math.round((active / total) * 100),
      color: 'bg-slate-900 dark:bg-slate-100',
    },
    {
      label: 'Pending Moderation',
      count: pending,
      percentage: Math.round((pending / total) * 100),
      color: 'bg-slate-500 dark:bg-slate-400',
    },
    {
      label: 'Draft / Inactive',
      count: other,
      percentage: Math.round((other / total) * 100),
      color: 'bg-slate-300 dark:bg-slate-700',
    },
  ]
})

const fetchAdminData = async () => {
  isLoading.value = true
  apiError.value = null

  try {
    const res = await adminService.getDashboard()
    if (res && res.success && res.data) {
      stats.value = res.data
    } else if (res && res.data) {
      stats.value = res.data
    } else if (res && typeof res.total_users !== 'undefined') {
      stats.value = res
    }
  } catch (err) {
    apiError.value = err.message || 'Failed to fetch admin dashboard data.'
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchAdminData()
})
</script>
