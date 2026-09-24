<template>
  <div class="space-y-5">
    <!-- Period Selector -->
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

    <!-- Key Metrics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-semibold mb-1">Total Views</p>
        <p class="text-2xl font-bold text-gray-900">1,234</p>
        <p class="text-xs text-emerald-600 mt-2">↑ 12% from last month</p>
      </div>
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-semibold mb-1">Total Favorites</p>
        <p class="text-2xl font-bold text-gray-900">456</p>
        <p class="text-xs text-emerald-600 mt-2">↑ 8% from last month</p>
      </div>
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-semibold mb-1">Inquiries</p>
        <p class="text-2xl font-bold text-gray-900">89</p>
        <p class="text-xs text-emerald-600 mt-2">↑ 23% from last month</p>
      </div>
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-semibold mb-1">Bookings</p>
        <p class="text-2xl font-bold text-gray-900">12</p>
        <p class="text-xs text-emerald-600 mt-2">↑ 5% from last month</p>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      <!-- Property Views Chart -->
      <div class="bg-white rounded-lg border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Property Views Trend</h3>
        <div class="flex items-end gap-1.5 h-40">
          <div v-for="(bar, i) in viewsTrend" :key="i" class="flex-1 flex flex-col items-center gap-1">
            <span class="text-[9px] text-gray-500">{{ bar.value }}</span>
            <div
              class="w-full rounded-t bg-emerald-500 transition-all duration-300"
              :style="{ height: (bar.value / maxViews * 100) + '%', minHeight: '4px' }"
            ></div>
            <span class="text-[9px] text-gray-500">{{ bar.day }}</span>
          </div>
        </div>
      </div>

      <!-- Favorites Breakdown -->
      <div class="bg-white rounded-lg border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Favorites by Property</h3>
        <div class="space-y-3">
          <div v-for="prop in favoritesByProperty" :key="prop.name" class="flex items-center gap-3">
            <span class="w-20 text-xs text-gray-600 font-medium truncate">{{ prop.name }}</span>
            <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
              <div
                class="h-3 bg-emerald-500 rounded-full transition-all"
                :style="{ width: (prop.favorites / maxFavorites * 100) + '%' }"
              ></div>
            </div>
            <span class="text-xs font-bold text-gray-700 w-8 text-right">{{ prop.favorites }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Appointments Trends -->
    <div class="bg-white rounded-lg border border-gray-200 p-5">
      <h3 class="text-sm font-bold text-gray-900 mb-4">Appointment Requests Trend</h3>
      <div class="flex items-end gap-1.5 h-40">
        <div v-for="(bar, i) in appointmentsTrend" :key="i" class="flex-1 flex flex-col items-center gap-1">
          <span class="text-[9px] text-gray-500">{{ bar.value }}</span>
          <div
            class="w-full rounded-t bg-blue-500 transition-all duration-300"
            :style="{ height: (bar.value / maxAppointments * 100) + '%', minHeight: '4px' }"
          ></div>
          <span class="text-[9px] text-gray-500">{{ bar.week }}</span>
        </div>
      </div>
    </div>

    <!-- Performance Summary Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
              <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase">Property</th>
              <th class="px-5 py-3 text-right text-[11px] font-semibold text-gray-500 uppercase">Views</th>
              <th class="px-5 py-3 text-right text-[11px] font-semibold text-gray-500 uppercase">Favorites</th>
              <th class="px-5 py-3 text-right text-[11px] font-semibold text-gray-500 uppercase">Inquiries</th>
              <th class="px-5 py-3 text-right text-[11px] font-semibold text-gray-500 uppercase">Bookings</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="prop in propertyPerformance" :key="prop.id" class="hover:bg-gray-50">
              <td class="px-5 py-3 text-sm font-medium text-gray-800">{{ prop.name }}</td>
              <td class="px-5 py-3 text-sm text-right text-gray-700">{{ prop.views }}</td>
              <td class="px-5 py-3 text-sm text-right text-gray-700">{{ prop.favorites }}</td>
              <td class="px-5 py-3 text-sm text-right text-gray-700">{{ prop.inquiries }}</td>
              <td class="px-5 py-3 text-sm text-right font-semibold text-emerald-600">{{ prop.bookings }}</td>
            </tr>
            <tr class="bg-gray-50 font-bold">
              <td class="px-5 py-3 text-sm text-gray-900">Total</td>
              <td class="px-5 py-3 text-sm text-right text-gray-900">1,234</td>
              <td class="px-5 py-3 text-sm text-right text-gray-900">456</td>
              <td class="px-5 py-3 text-sm text-right text-gray-900">89</td>
              <td class="px-5 py-3 text-sm text-right text-emerald-600">12</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const selectedPeriod = ref('This Month')
const periods = ['Last 7 Days', 'This Month', 'Last 3 Months', 'All Time']

const viewsTrend = ref([
  { day: 'Mon', value: 45 },
  { day: 'Tue', value: 52 },
  { day: 'Wed', value: 48 },
  { day: 'Thu', value: 67 },
  { day: 'Fri', value: 78 },
  { day: 'Sat', value: 89 },
  { day: 'Sun', value: 95 },
])

const maxViews = computed(() => Math.max(...viewsTrend.value.map(v => v.value)))

const favoritesByProperty = ref([
  { name: 'Bole Luxury Apt', favorites: 156 },
  { name: 'CMC Villa', favorites: 123 },
  { name: 'Nifas Silk Office', favorites: 89 },
  { name: 'Piazza Suite', favorites: 78 },
  { name: 'Summit Residence', favorites: 10 },
])

const maxFavorites = computed(() => Math.max(...favoritesByProperty.value.map(f => f.favorites)))

const appointmentsTrend = ref([
  { week: 'W1', value: 12 },
  { week: 'W2', value: 15 },
  { week: 'W3', value: 10 },
  { week: 'W4', value: 18 },
])

const maxAppointments = computed(() => Math.max(...appointmentsTrend.value.map(a => a.value)))

const propertyPerformance = ref([
  { id: 1, name: 'Bole Luxury Apartment', views: 450, favorites: 156, inquiries: 34, bookings: 5 },
  { id: 2, name: 'CMC Villa', views: 380, favorites: 123, inquiries: 28, bookings: 4 },
  { id: 3, name: 'Nifas Silk Office', views: 220, favorites: 89, inquiries: 15, bookings: 2 },
  { id: 4, name: 'Piazza Contemporary', views: 184, favorites: 78, inquiries: 12, bookings: 1 },
  { id: 5, name: 'Summit Residence', views: 0, favorites: 10, inquiries: 0, bookings: 0 },
])
</script>
