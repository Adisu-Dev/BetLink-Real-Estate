<template>
  <div class="space-y-5">
    <div class="flex gap-3 mb-4">
      <button
        v-for="period in periods"
        :key="period"
        @click="selectedPeriod = period"
        :class="[
          'px-4 py-2 rounded-lg font-semibold text-sm transition-colors',
          selectedPeriod === period
            ? 'bg-emerald-600 text-white'
            : 'border border-gray-300 text-gray-700 hover:bg-gray-50'
        ]"
      >
        {{ period }}
      </button>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-semibold mb-1">Properties Listed</p>
        <p class="text-2xl font-bold text-gray-900">24</p>
        <p class="text-xs text-emerald-600 mt-2">↑ 3 new this month</p>
      </div>
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-semibold mb-1">Active Leads</p>
        <p class="text-2xl font-bold text-gray-900">47</p>
        <p class="text-xs text-emerald-600 mt-2">↑ 8% from last month</p>
      </div>
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-semibold mb-1">Conversions</p>
        <p class="text-2xl font-bold text-gray-900">12</p>
        <p class="text-xs text-emerald-600 mt-2">↑ 2 this month</p>
      </div>
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-semibold mb-1">Avg Time to Sale</p>
        <p class="text-2xl font-bold text-gray-900">18 days</p>
        <p class="text-xs text-gray-500 mt-2">↓ 2 days improvement</p>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      <div class="bg-white rounded-lg border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Monthly Conversions</h3>
        <div class="flex items-end gap-1.5 h-40">
          <div v-for="(bar, i) in conversionsTrend" :key="i" class="flex-1 flex flex-col items-center gap-1">
            <span class="text-[9px] text-gray-500">{{ bar.value }}</span>
            <div
              class="w-full rounded-t bg-emerald-500 transition-all duration-300"
              :style="{ height: (bar.value / maxConversions * 100) + '%', minHeight: '4px' }"
            ></div>
            <span class="text-[9px] text-gray-500">{{ bar.month }}</span>
          </div>
        </div>
      </div>

      <!-- Lead Status Distribution -->
      <div class="bg-white rounded-lg border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Lead Status Distribution</h3>
        <div class="space-y-3">
          <div v-for="status in leadStatuses" :key="status.label" class="flex items-center gap-3">
            <span class="w-20 text-xs text-gray-600 font-medium">{{ status.label }}</span>
            <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
              <div
                :class="status.color"
                class="h-3 rounded-full transition-all"
                :style="{ width: (status.count / maxLeads * 100) + '%' }"
              ></div>
            </div>
            <span class="text-xs font-bold text-gray-700 w-8 text-right">{{ status.count }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Properties Performance Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
      <div class="px-5 py-3 border-b border-gray-100">
        <h3 class="text-sm font-bold text-gray-900">Top Performing Properties</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
              <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase">Property</th>
              <th class="px-5 py-3 text-right text-[11px] font-semibold text-gray-500 uppercase">Views</th>
              <th class="px-5 py-3 text-right text-[11px] font-semibold text-gray-500 uppercase">Inquiries</th>
              <th class="px-5 py-3 text-right text-[11px] font-semibold text-gray-500 uppercase">Conversions</th>
              <th class="px-5 py-3 text-right text-[11px] font-semibold text-gray-500 uppercase">Conversion Rate</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="prop in topProperties" :key="prop.id" class="hover:bg-gray-50">
              <td class="px-5 py-3 text-sm font-medium text-gray-900">{{ prop.name }}</td>
              <td class="px-5 py-3 text-sm text-right text-gray-700">{{ prop.views }}</td>
              <td class="px-5 py-3 text-sm text-right text-gray-700">{{ prop.inquiries }}</td>
              <td class="px-5 py-3 text-sm text-right font-semibold text-emerald-600">{{ prop.conversions }}</td>
              <td class="px-5 py-3 text-sm text-right font-semibold text-gray-900">{{ prop.conversionRate }}%</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Revenue Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-semibold mb-1">Total Revenue</p>
        <p class="text-2xl font-bold text-emerald-600">ETB 450K</p>
        <p class="text-xs text-gray-500 mt-2">Commission earned this year</p>
      </div>
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-semibold mb-1">Avg Commission per Sale</p>
        <p class="text-2xl font-bold text-gray-900">ETB 15K</p>
        <p class="text-xs text-gray-500 mt-2">Based on 12 conversions</p>
      </div>
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-semibold mb-1">Bonus Eligibility</p>
        <p class="text-2xl font-bold text-blue-600">85%</p>
        <p class="text-xs text-gray-500 mt-2">Meet target: 90% to unlock bonus</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const selectedPeriod = ref('This Month')
const periods = ['Last 7 Days', 'This Month', 'Last 3 Months', 'All Time']

const conversionsTrend = ref([
  { month: 'Jan', value: 6 },
  { month: 'Feb', value: 8 },
  { month: 'Mar', value: 7 },
  { month: 'Apr', value: 9 },
  { month: 'May', value: 11 },
  { month: 'Jun', value: 12 },
])

const maxConversions = computed(() => Math.max(...conversionsTrend.value.map(c => c.value)))

const leadStatuses = ref([
  { label: 'New', count: 15, color: 'bg-gray-500' },
  { label: 'Contacted', count: 18, color: 'bg-yellow-500' },
  { label: 'Interested', count: 10, color: 'bg-blue-500' },
  { label: 'Converted', count: 4, color: 'bg-emerald-500' },
])

const maxLeads = computed(() => Math.max(...leadStatuses.value.map(s => s.count)))

const topProperties = ref([
  { id: 1, name: 'Bole Luxury Apartment', views: 450, inquiries: 34, conversions: 5, conversionRate: 14.7 },
  { id: 2, name: 'CMC Office Space', views: 380, inquiries: 28, conversions: 4, conversionRate: 14.2 },
  { id: 3, name: 'Piazza Contemporary', views: 290, inquiries: 18, conversions: 2, conversionRate: 11.1 },
  { id: 4, name: 'Summit Villa', views: 240, inquiries: 12, conversions: 1, conversionRate: 8.3 },
])
</script>
