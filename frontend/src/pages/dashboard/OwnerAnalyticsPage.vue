<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Portfolio Analytics & Performance</h1>
      </div>
      <button
        type="button"
        @click="loadAnalytics(true)"
        class="px-4 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition-colors shadow-xs flex items-center gap-1.5 cursor-pointer"
      >
        <RotateCcw class="w-3.5 h-3.5" />
        <span>Refresh Analytics</span>
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="space-y-6 animate-pulse">
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div v-for="n in 4" :key="n" class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 h-28"></div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 h-64"></div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 h-64"></div>
      </div>
    </div>

    <!-- Analytics Dashboard Content -->
    <template v-else>
      <!-- Core Metric Cards -->
      <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
          <div class="flex items-center justify-between text-slate-400 mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wider">Total Views</span>
            <Eye class="w-5 h-5 text-slate-900 dark:text-white" />
          </div>
          <p class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ metrics.totalViews }}</p>
          <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-1">Cumulative listing clicks</p>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
          <div class="flex items-center justify-between text-slate-400 mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wider">Total Favorites</span>
            <Heart class="w-5 h-5 text-rose-500" />
          </div>
          <p class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ metrics.totalFavorites }}</p>
          <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-1">Saved by buyers</p>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
          <div class="flex items-center justify-between text-slate-400 mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wider">Inquiries & Leads</span>
            <MessageSquare class="w-5 h-5 text-slate-900 dark:text-white" />
          </div>
          <p class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ metrics.totalInquiries }}</p>
          <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-1">Direct chat threads</p>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
          <div class="flex items-center justify-between text-slate-400 mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wider">Tours Booked</span>
            <Calendar class="w-5 h-5 text-slate-900 dark:text-white" />
          </div>
          <p class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ metrics.totalTours }}</p>
          <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-1">Viewing appointments</p>
        </div>
      </div>

      <!-- Charts & Breakdown Section -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 2 Cols: Monthly Engagement Trends Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
              <h2 class="text-sm font-bold text-slate-900 dark:text-white">Listing Views & Inquiries Trend</h2>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Monthly visitor traffic and direct tenant inquiries</p>
            </div>
            <div class="flex items-center gap-3">
              <!-- Chart Legend -->
              <div class="flex items-center gap-3 text-xs font-semibold">
                <span class="flex items-center gap-1.5 text-slate-700 dark:text-slate-300 text-[11px]">
                  <span class="w-2.5 h-2.5 rounded-full bg-slate-900 dark:bg-white inline-block"></span>
                  Views
                </span>
                <span class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 text-[11px]">
                  <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                  Inquiries
                </span>
              </div>
              <span class="text-xs font-bold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg">
                Avg {{ metrics.inquiryRate }}% Conversion
              </span>
            </div>
          </div>

          <!-- Bar Chart Visualization -->
          <div v-if="viewsTrends.length === 0" class="h-48 sm:h-56 flex items-center justify-center text-xs text-slate-400">
            No historical views data recorded yet.
          </div>
          <div v-else class="pt-2 flex items-end justify-between gap-3 h-48 sm:h-56">
            <div
              v-for="trend in viewsTrends"
              :key="trend.month"
              class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end group relative"
            >
              <!-- Explicit count badge above bar -->
              <div class="text-[11px] font-extrabold text-slate-900 dark:text-white tracking-tight">
                {{ trend.views }}
              </div>

              <!-- Bar Capsule -->
              <div class="w-full max-w-[40px] bg-slate-100 dark:bg-slate-800 rounded-t-xl overflow-hidden flex flex-col justify-end h-full relative">
                <div
                  class="w-full bg-slate-900 dark:bg-white rounded-t-xl group-hover:opacity-85 transition-all duration-300"
                  :style="{ height: trend.views > 0 ? `${Math.min(100, Math.max(14, (trend.views / Math.max(maxViews, 1)) * 100))}%` : '4px' }"
                  :title="`${trend.views} views • ${trend.inquiries || 0} inquiries in ${trend.month}`"
                ></div>
              </div>

              <!-- Month Label & Inquiries Pill -->
              <div class="text-center pt-0.5">
                <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300">{{ trend.month }}</span>
                <span v-if="trend.inquiries > 0" class="block text-[9px] font-bold text-emerald-600 dark:text-emerald-400">
                  {{ trend.inquiries }} inq
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- 1 Col: Top Performing Properties -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white">Top Performing Listings</h2>
            <span class="text-xs text-slate-400 font-semibold">By views</span>
          </div>

          <div v-if="topListings.length === 0" class="text-center py-10 text-slate-400 text-xs">
            No listing activity data yet.
          </div>

          <div v-else class="divide-y divide-slate-100 dark:divide-slate-800">
            <div
              v-for="prop in topListings"
              :key="prop.id"
              class="py-3 flex items-center justify-between gap-3"
            >
              <div class="flex items-center gap-3 min-w-0">
                <img :src="prop.image" class="w-10 h-10 rounded-lg object-cover bg-slate-100 shrink-0" />
                <div class="min-w-0">
                  <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ prop.title }}</p>
                  <p class="text-[11px] text-slate-400 truncate">{{ prop.location }}</p>
                </div>
              </div>
              <div class="text-right shrink-0">
                <span class="text-xs font-black text-slate-900 dark:text-white">{{ prop.views_count }} views</span>
                <p class="text-[10px] text-slate-400">{{ prop.favorites_count }} saves</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import {
  RotateCcw,
  Eye,
  Heart,
  MessageSquare,
  Calendar
} from 'lucide-vue-next'
import { ownerService } from '@/services/ownerService'

const isLoading = ref(true)
const apiError = ref(null)

const metrics = reactive({
  totalViews: 0,
  totalFavorites: 0,
  totalTours: 0,
  totalInquiries: 0,
  inquiryRate: 0
})

const viewsTrends = ref([])
const topListings = ref([])

const maxViews = computed(() => {
  if (!viewsTrends.value.length) return 10
  const highest = Math.max(...viewsTrends.value.map(t => t.views || 0))
  return Math.max(highest, 10)
})

async function loadAnalytics(forceRefresh = false) {
  isLoading.value = true
  apiError.value = null

  try {
    const res = await ownerService.getAnalytics(forceRefresh ? { refresh: 1 } : {})
    const payload = res?.data?.data ? res.data.data : (res?.data || res || {})

    if (payload.metrics) {
      Object.assign(metrics, payload.metrics)
    }
    if (Array.isArray(payload.viewsTrends)) {
      viewsTrends.value = payload.viewsTrends
    }
    if (Array.isArray(payload.topListings)) {
      topListings.value = payload.topListings
    }
  } catch (err) {
    console.error('Failed to load analytics:', err)
    apiError.value = 'Failed to load analytics metrics.'
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  loadAnalytics()
})
</script>
