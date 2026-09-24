<template>
  <div class="space-y-6">
    <!-- Header with Time Range Selector -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Business Analytics</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Performance metrics, sales volume, and client conversion funnel.</p>
      </div>

      <div class="flex items-center gap-2">
        <select
          v-model="timeRange"
          @change="fetchAnalytics"
          class="px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold focus:outline-none cursor-pointer shadow-xs"
        >
          <option value="7_days">7 Days</option>
          <option value="30_days">30 Days</option>
          <option value="quarter">Quarter</option>
          <option value="year">Year</option>
        </select>
      </div>
    </div>

    <!-- Error State with Retry -->
    <div v-if="apiError" class="p-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl text-center space-y-3">
      <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">{{ apiError }}</p>
      <button 
        @click="fetchAnalytics" 
        class="px-3.5 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold rounded-xl hover:bg-slate-800 transition-colors shadow-xs cursor-pointer"
      >
        Retry Loading
      </button>
    </div>

    <!-- Loading Skeleton State -->
    <div v-else-if="isLoading" class="space-y-6">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        <div v-for="n in 4" :key="n" class="bg-white dark:bg-slate-900 rounded-xl p-4 border border-slate-300 dark:border-slate-800 animate-pulse h-24"></div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 h-64 border border-slate-300 dark:border-slate-800 animate-pulse"></div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 h-64 border border-slate-300 dark:border-slate-800 animate-pulse"></div>
      </div>
    </div>

    <template v-else>
      <!-- Stats Overview (Clean Non-Truncating 4-Column Grid) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Managed Listings -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-xs border border-slate-300 dark:border-slate-800 flex items-center gap-3.5">
          <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0">
            <Building2 class="w-5 h-5" />
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">Managed Listings</p>
            <p class="text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ metrics.managedProperties || 0 }}</p>
          </div>
        </div>

        <!-- 2. Listing Impressions -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-xs border border-slate-300 dark:border-slate-800 flex items-center gap-3.5">
          <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0">
            <Eye class="w-5 h-5" />
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">Listing Views</p>
            <p class="text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ metrics.totalViews || 0 }}</p>
          </div>
        </div>

        <!-- 3. Client Inquiries -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-xs border border-slate-300 dark:border-slate-800 flex items-center gap-3.5">
          <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0">
            <Users class="w-5 h-5" />
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">Client Inquiries</p>
            <p class="text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ metrics.totalLeads || 0 }}</p>
          </div>
        </div>

        <!-- 4. Tours & Deals -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-xs border border-slate-300 dark:border-slate-800 flex items-center gap-3.5">
          <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0">
            <Calendar class="w-5 h-5" />
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">Tours Conducted</p>
            <div class="flex items-baseline gap-2 mt-0.5">
              <span class="text-xl font-black text-slate-900 dark:text-white leading-tight">{{ metrics.toursScheduled || 0 }}</span>
              <span class="text-[11px] font-bold text-slate-400">({{ ((Number(metrics.totalSalesVolume || 0)) / 1000000).toFixed(1) }}M ETB)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts & Funnel Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Monthly Sales Trend Chart -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-300 dark:border-slate-800 flex flex-col justify-between">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Monthly Sales Pipeline</h3>
            <span class="text-[10px] font-bold text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full">
              Volume (ETB)
            </span>
          </div>

          <div class="h-48 w-full flex items-end justify-between gap-3 pt-4 border-b border-slate-100 dark:border-slate-800">
            <div v-for="(item, idx) in monthlySales" :key="idx" class="flex flex-col items-center gap-2 flex-1">
              <div class="w-full flex items-end justify-center h-36">
                <div 
                  class="w-full max-w-[36px] bg-slate-900 dark:bg-slate-100 rounded-t-lg transition-all duration-500 hover:opacity-80" 
                  :style="{ height: `${item.volume > 0 ? Math.min(100, Math.max(15, (item.volume / maxVolume) * 100)) : (item.inquiries > 0 ? 15 : 6)}%` }"
                  :title="`ETB ${Number(item.volume || 0).toLocaleString()} (${item.inquiries || 0} inquiries)`"
                ></div>
              </div>
              <span class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold">{{ item.month }}</span>
            </div>
          </div>
        </div>

        <!-- Conversion Funnel Breakdown -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-300 dark:border-slate-800 flex flex-col justify-between">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Lead Conversion Funnel</h3>
            <span class="text-[11px] font-black text-emerald-600 dark:text-emerald-400">
              {{ metrics.conversionRate ?? 0 }}% Win Rate
            </span>
          </div>

          <div class="space-y-4 my-auto">
            <div v-for="step in funnel" :key="step.stage" class="space-y-1.5">
              <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-slate-700 dark:text-slate-300">{{ step.stage }}</span>
                <span class="font-black text-slate-900 dark:text-white">{{ step.count }} ({{ step.percentage }}%)</span>
              </div>
              <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                <div 
                  class="bg-slate-900 dark:bg-white h-2 rounded-full transition-all duration-500" 
                  :style="{ width: `${Math.max(step.count > 0 ? 8 : 2, step.percentage)}%` }"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Building2, Eye, Users, Calendar, TrendingUp } from 'lucide-vue-next'
import { agentService } from '@/services/agentService'

const isLoading = ref(true)
const apiError = ref(null)
const timeRange = ref('30_days')

const metrics = ref({})
const monthlySales = ref([])
const funnel = ref([])

const maxVolume = computed(() => {
  if (!monthlySales.value.length) return 1
  const max = Math.max(...monthlySales.value.map(s => Number(s.volume) || 0))
  return max > 0 ? max : 1
})

async function fetchAnalytics() {
  isLoading.value = true
  apiError.value = null

  try {
    const res = await agentService.getAnalytics({ time_range: timeRange.value })
    const data = res.data || {}
    metrics.value = data.metrics || {}
    monthlySales.value = data.chartData?.monthlySales || []
    funnel.value = data.funnel || []
  } catch (err) {
    console.error('Failed to load agent analytics:', err)
    apiError.value = 'Failed to load business analytics.'
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchAnalytics()
})
</script>
