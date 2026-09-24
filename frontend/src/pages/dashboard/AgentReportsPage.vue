<template>
  <div class="space-y-6">
    <!-- Header with Title & Export Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
          {{ t('agent_reports_title', 'Agent Performance & Sales Reports') }}
        </h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
          {{ t('agent_reports_subtitle', 'Assigned listings metrics, client view requests, leads pipeline, and commission stats.') }}
        </p>
      </div>

      <!-- Toolbar: Time Filter & Export Buttons -->
      <div class="flex flex-wrap items-center gap-2.5 shrink-0">
        <!-- Time Filter Dropdown -->
        <select
          v-model="selectedTimeRange"
          @change="loadReport"
          class="px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold focus:outline-none cursor-pointer shadow-xs"
        >
          <option value="today">Today</option>
          <option value="week">Week</option>
          <option value="month">Month</option>
          <option value="year">Year</option>
          <option value="all">All</option>
        </select>

        <button
          @click="openExportModal('excel')"
          :disabled="isExporting"
          class="flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold transition-all shadow-xs disabled:opacity-60 cursor-pointer"
        >
          <FileSpreadsheet class="w-4 h-4 text-slate-700 dark:text-slate-300" />
          <span>Export Excel</span>
        </button>

        <button
          @click="openExportModal('pdf')"
          :disabled="isExporting"
          class="flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold transition-all shadow-xs disabled:opacity-60 cursor-pointer"
        >
          <FileText class="w-4 h-4 text-slate-700 dark:text-slate-300" />
          <span>Export PDF</span>
        </button>
      </div>
    </div>

    <!-- KPI Metric Cards Grid (Interactive Category Switchers) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
      <!-- 1. Assigned Properties -->
      <button
        type="button"
        @click="activeCategory = 'properties'"
        :class="[
          'p-4 rounded-2xl shadow-xs transition-all text-left cursor-pointer active:scale-[0.99]',
          activeCategory === 'properties'
            ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 ring-2 ring-slate-900 dark:ring-white border-transparent'
            : 'bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600'
        ]"
      >
        <div class="flex items-center justify-between mb-2" :class="activeCategory === 'properties' ? 'text-slate-300 dark:text-slate-600' : 'text-slate-400'">
          <span class="text-[11px] font-bold uppercase tracking-wider">Managed Listings</span>
          <Building2 class="w-4 h-4" />
        </div>
        <div class="text-xl font-black" :class="activeCategory === 'properties' ? 'text-white dark:text-slate-900' : 'text-slate-900 dark:text-white'">
          {{ reportData.summary?.assignedProperties ?? 0 }}
        </div>
        <p class="text-[10px] mt-1 font-medium" :class="activeCategory === 'properties' ? 'text-slate-300 dark:text-slate-600' : 'text-slate-500'">
          {{ reportData.summary?.activeListings ?? 0 }} currently active
        </p>
      </button>

      <!-- 2. Leads Generated -->
      <button
        type="button"
        @click="activeCategory = 'leads'"
        :class="[
          'p-4 rounded-2xl shadow-xs transition-all text-left cursor-pointer active:scale-[0.99]',
          activeCategory === 'leads'
            ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 ring-2 ring-slate-900 dark:ring-white border-transparent'
            : 'bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600'
        ]"
      >
        <div class="flex items-center justify-between mb-2" :class="activeCategory === 'leads' ? 'text-slate-300 dark:text-slate-600' : 'text-slate-400'">
          <span class="text-[11px] font-bold uppercase tracking-wider">Client Leads</span>
          <Users class="w-4 h-4" />
        </div>
        <div class="text-xl font-black" :class="activeCategory === 'leads' ? 'text-white dark:text-slate-900' : 'text-slate-900 dark:text-white'">
          {{ reportData.summary?.totalLeads ?? 0 }}
        </div>
        <p class="text-[10px] mt-1 font-medium" :class="activeCategory === 'leads' ? 'text-slate-300 dark:text-slate-600' : 'text-slate-500'">
          {{ reportData.summary?.conversionRate ?? 0 }}% conversion rate
        </p>
      </button>

      <!-- 3. Tour Requests Conducted -->
      <button
        type="button"
        @click="activeCategory = 'appointments'"
        :class="[
          'p-4 rounded-2xl shadow-xs transition-all text-left cursor-pointer active:scale-[0.99]',
          activeCategory === 'appointments'
            ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 ring-2 ring-slate-900 dark:ring-white border-transparent'
            : 'bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600'
        ]"
      >
        <div class="flex items-center justify-between mb-2" :class="activeCategory === 'appointments' ? 'text-slate-300 dark:text-slate-600' : 'text-slate-400'">
          <span class="text-[11px] font-bold uppercase tracking-wider">Tours Conducted</span>
          <Calendar class="w-4 h-4" />
        </div>
        <div class="text-xl font-black" :class="activeCategory === 'appointments' ? 'text-white dark:text-slate-900' : 'text-slate-900 dark:text-white'">
          {{ reportData.summary?.tourRequests ?? 0 }}
        </div>
        <p class="text-[10px] mt-1 font-medium" :class="activeCategory === 'appointments' ? 'text-slate-300 dark:text-slate-600' : 'text-slate-500'">
          {{ reportData.summary?.dealsClosed ?? 0 }} deals closed
        </p>
      </button>

      <!-- 4. Estimated Commission -->
      <button
        type="button"
        @click="activeCategory = 'deals'"
        :class="[
          'p-4 rounded-2xl shadow-xs transition-all text-left cursor-pointer active:scale-[0.99]',
          activeCategory === 'deals'
            ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 ring-2 ring-slate-900 dark:ring-white border-transparent'
            : 'bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600'
        ]"
      >
        <div class="flex items-center justify-between mb-2" :class="activeCategory === 'deals' ? 'text-slate-300 dark:text-slate-600' : 'text-slate-400'">
          <span class="text-[11px] font-bold uppercase tracking-wider">Est. Commissions</span>
          <DollarSign class="w-4 h-4" />
        </div>
        <div class="text-xl font-black truncate" :class="activeCategory === 'deals' ? 'text-white dark:text-slate-900' : 'text-slate-900 dark:text-white'">
          ETB {{ reportData.summary?.estimatedCommissions ? ((reportData.summary.estimatedCommissions) / 1000).toFixed(0) : '0' }}k
        </div>
        <p class="text-[10px] mt-1 font-medium" :class="activeCategory === 'deals' ? 'text-slate-300 dark:text-slate-600' : 'text-slate-500'">
          Net agency earnings
        </p>
      </button>
    </div>

    <!-- Search & Filters Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div class="relative flex-1 max-w-sm">
        <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="`Search ${activeCategoryLabel.toLowerCase()} records...`"
          class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-slate-400"
        />
      </div>

      <div class="flex items-center gap-2">
        <!-- Category Dropdown Switcher -->
        <select
          v-model="activeCategory"
          class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold focus:outline-none cursor-pointer"
        >
          <option value="leads">Leads ({{ reportData.leadsBreakdown?.length || 0 }})</option>
          <option value="properties">Listings ({{ reportData.propertyBreakdown?.length || 0 }})</option>
          <option value="appointments">Tours ({{ reportData.appointmentsList?.length || 0 }})</option>
          <option value="deals">Commissions</option>
        </select>

        <!-- Transaction Type Filter (For Listings & Leads) -->
        <select
          v-if="activeCategory === 'leads' || activeCategory === 'properties'"
          v-model="typeFilter"
          class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold focus:outline-none cursor-pointer"
        >
          <option value="">Type</option>
          <option value="Sale">Sale</option>
          <option value="Rent">Rent</option>
          <option value="Short-Rent">Short-Rent</option>
        </select>

        <!-- Status Filter -->
        <select
          v-model="statusFilter"
          class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold focus:outline-none cursor-pointer"
        >
          <option value="">Status</option>
          <template v-if="activeCategory === 'leads'">
            <option value="Hot Lead">Hot</option>
            <option value="Contacted">Contacted</option>
            <option value="Tour Scheduled">Tour</option>
            <option value="Closed Deal">Closed</option>
          </template>
          <template v-else-if="activeCategory === 'properties'">
            <option value="Active">Active</option>
            <option value="Pending">Pending</option>
            <option value="Draft">Draft</option>
          </template>
          <template v-else-if="activeCategory === 'appointments'">
            <option value="Pending">Pending</option>
            <option value="Confirmed">Confirmed</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
          </template>
        </select>
      </div>
    </div>

    <!-- 1. Client Leads Pipeline Table View -->
    <div v-if="activeCategory === 'leads'" class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-300 dark:border-slate-800 overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between">
        <h3 class="font-black text-sm text-slate-900 dark:text-white">Client Inquiries & Leads Pipeline Table</h3>
        <span class="text-[11px] text-slate-500 font-semibold">{{ filteredLeads.length }} Records</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800">
            <tr>
              <th class="px-4 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider w-12 text-center">No</th>
              <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Client Name</th>
              <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Property</th>
              <th class="px-4 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Type</th>
              <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Pipeline Status</th>
              <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs sm:text-sm">
            <tr v-for="(lead, idx) in filteredLeads" :key="lead.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
              <td class="px-4 py-3.5 text-center font-bold text-slate-400 text-xs">
                {{ idx + 1 }}
              </td>
              <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                {{ lead.client }}
              </td>
              <td class="px-5 py-3.5 text-slate-600 dark:text-slate-400 font-medium">
                {{ lead.property }}
              </td>
              <td class="px-4 py-3.5 text-slate-500 font-semibold text-xs">
                {{ lead.listing_type || 'Sale' }}
              </td>
              <td class="px-5 py-3.5">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                  {{ lead.status }}
                </span>
              </td>
              <td class="px-5 py-3.5 text-slate-600 dark:text-slate-400 font-medium">
                {{ lead.date }}
              </td>
            </tr>
            <tr v-if="filteredLeads.length === 0">
              <td colspan="6" class="px-6 py-8 text-center text-slate-400 text-xs font-semibold">
                No client leads found matching your criteria.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 2. Managed Listings Table View -->
    <div v-else-if="activeCategory === 'properties'" class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-300 dark:border-slate-800 overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between">
        <h3 class="font-black text-sm text-slate-900 dark:text-white">Managed Portfolio Listings Table</h3>
        <span class="text-[11px] text-slate-500 font-semibold">{{ filteredProperties.length }} Listings</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800">
            <tr>
              <th class="px-4 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider w-12 text-center">No</th>
              <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Title</th>
              <th class="px-4 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Category</th>
              <th class="px-4 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Type</th>
              <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Price (ETB)</th>
              <th class="px-4 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Views</th>
              <th class="px-4 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
              <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs sm:text-sm">
            <tr v-for="(prop, idx) in filteredProperties" :key="prop.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
              <td class="px-4 py-3.5 text-center font-bold text-slate-400 text-xs">
                {{ idx + 1 }}
              </td>
              <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                {{ prop.title }}
              </td>
              <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400 font-medium">
                {{ prop.type }}
              </td>
              <td class="px-4 py-3.5 text-slate-500 font-semibold text-xs">
                {{ prop.listing_type }}
              </td>
              <td class="px-5 py-3.5 font-black text-slate-900 dark:text-white">
                ETB {{ Number(prop.price).toLocaleString() }}
              </td>
              <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400 font-bold">
                {{ prop.views }} views
              </td>
              <td class="px-4 py-3.5">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                  {{ prop.status }}
                </span>
              </td>
              <td class="px-5 py-3.5 text-slate-500 font-medium">
                {{ prop.created_at }}
              </td>
            </tr>
            <tr v-if="filteredProperties.length === 0">
              <td colspan="8" class="px-6 py-8 text-center text-slate-400 text-xs font-semibold">
                No managed properties found matching your criteria.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 3. Tours Conducted Table View -->
    <div v-else-if="activeCategory === 'appointments'" class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-300 dark:border-slate-800 overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between">
        <h3 class="font-black text-sm text-slate-900 dark:text-white">Tour Bookings & Appointments Table</h3>
        <span class="text-[11px] text-slate-500 font-semibold">{{ filteredAppointments.length }} Tours</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800">
            <tr>
              <th class="px-4 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider w-12 text-center">No</th>
              <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Property</th>
              <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Client Name</th>
              <th class="px-4 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Phone</th>
              <th class="px-4 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Format</th>
              <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Scheduled Time</th>
              <th class="px-4 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs sm:text-sm">
            <tr v-for="(tour, idx) in filteredAppointments" :key="tour.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
              <td class="px-4 py-3.5 text-center font-bold text-slate-400 text-xs">
                {{ idx + 1 }}
              </td>
              <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                {{ tour.property }}
              </td>
              <td class="px-5 py-3.5 text-slate-700 dark:text-slate-300 font-semibold">
                {{ tour.visitor }}
              </td>
              <td class="px-4 py-3.5 text-slate-500 font-medium">
                {{ tour.visitor_phone }}
              </td>
              <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400 font-medium">
                {{ tour.type }}
              </td>
              <td class="px-5 py-3.5 text-slate-600 dark:text-slate-400 font-medium">
                {{ tour.scheduled_at }}
              </td>
              <td class="px-4 py-3.5">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                  {{ tour.status }}
                </span>
              </td>
            </tr>
            <tr v-if="filteredAppointments.length === 0">
              <td colspan="7" class="px-6 py-8 text-center text-slate-400 text-xs font-semibold">
                No tour viewing appointments found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 4. Estimated Commissions & Closed Deals View -->
    <div v-else class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-300 dark:border-slate-800 p-6 space-y-4">
      <div class="flex items-center justify-between border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
          <h3 class="font-black text-base text-slate-900 dark:text-white">Agency Commissions & Closed Deals Summary</h3>
          <p class="text-xs text-slate-400 mt-0.5">Calculated net earnings based on finalized transactions and contracts.</p>
        </div>
        <div class="text-right">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Net Revenue</span>
          <span class="text-2xl font-black text-slate-900 dark:text-white">ETB {{ Number(reportData.summary?.estimatedCommissions || 0).toLocaleString() }}</span>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
          <span class="text-xs font-bold text-slate-400 uppercase">Closed Transactions</span>
          <p class="text-xl font-black text-slate-900 dark:text-white mt-1">{{ reportData.summary?.dealsClosed ?? 0 }} Deals</p>
        </div>
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
          <span class="text-xs font-bold text-slate-400 uppercase">Inquiry Conversion</span>
          <p class="text-xl font-black text-slate-900 dark:text-white mt-1">{{ reportData.summary?.conversionRate ?? 0 }}%</p>
        </div>
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
          <span class="text-xs font-bold text-slate-400 uppercase">Avg Commission / Deal</span>
          <p class="text-xl font-black text-slate-900 dark:text-white mt-1">ETB 25,000</p>
        </div>
      </div>
    </div>

    <!-- Export Confirmation Modal -->
    <ConfirmModal
      :isOpen="showExportModal"
      :title="`Export Agent ${activeCategoryLabel} Report (${exportType.toUpperCase()})`"
      :message="`Are you sure you want to export your agent ${activeCategoryLabel.toLowerCase()} report as ${exportType === 'excel' ? 'an Excel Spreadsheet (.csv)' : 'a PDF Document'}?`"
      :confirmLabel="exportType === 'excel' ? 'Download Excel' : 'Download PDF'"
      cancelLabel="Cancel"
      @confirm="confirmExport"
      @cancel="showExportModal = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  FileSpreadsheet,
  FileText,
  Building2,
  Users,
  Calendar,
  DollarSign,
  Search
} from 'lucide-vue-next'
import { reportService } from '@/services/reportService'
import { useLanguage } from '@/composables/useLanguage'
import { useToastStore } from '@/stores/toast'
import ConfirmModal from '@/components/dashboard/ConfirmModal.vue'

const { t } = useLanguage()
const toast = useToastStore()

const isExporting = ref(false)
const showExportModal = ref(false)
const exportType = ref('excel')
const selectedTimeRange = ref('all')

const activeCategory = ref('leads')
const searchQuery = ref('')
const statusFilter = ref('')
const typeFilter = ref('')

const reportData = ref({})

const activeCategoryLabel = computed(() => {
  if (activeCategory.value === 'properties') return 'Listings'
  if (activeCategory.value === 'appointments') return 'Tours'
  if (activeCategory.value === 'deals') return 'Commissions'
  return 'Leads'
})

const filteredLeads = computed(() => {
  let list = reportData.value.leadsBreakdown || []
  if (typeFilter.value) {
    list = list.filter(l => (l.listing_type || '').toLowerCase() === typeFilter.value.toLowerCase())
  }
  if (statusFilter.value) {
    list = list.filter(l => l.status?.toLowerCase() === statusFilter.value.toLowerCase())
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(l =>
      l.client?.toLowerCase().includes(q) ||
      l.property?.toLowerCase().includes(q) ||
      l.status?.toLowerCase().includes(q)
    )
  }
  return list
})

const filteredProperties = computed(() => {
  let list = reportData.value.propertyBreakdown || []
  if (typeFilter.value) {
    list = list.filter(p => (p.listing_type || '').toLowerCase() === typeFilter.value.toLowerCase())
  }
  if (statusFilter.value) {
    list = list.filter(p => (p.status || '').toLowerCase() === statusFilter.value.toLowerCase())
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(p =>
      p.title?.toLowerCase().includes(q) ||
      p.type?.toLowerCase().includes(q) ||
      p.status?.toLowerCase().includes(q)
    )
  }
  return list
})

const filteredAppointments = computed(() => {
  let list = reportData.value.appointmentsList || []
  if (statusFilter.value) {
    list = list.filter(a => (a.status || '').toLowerCase() === statusFilter.value.toLowerCase())
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(a =>
      a.property?.toLowerCase().includes(q) ||
      a.visitor?.toLowerCase().includes(q) ||
      a.status?.toLowerCase().includes(q)
    )
  }
  return list
})

async function loadReport() {
  try {
    const res = await reportService.getAgentReport({ time_range: selectedTimeRange.value })
    reportData.value = res.data || {}
  } catch (err) {
    console.error('Failed to load agent reports:', err)
  }
}

async function loadData() {
  loadReport()
}

function openExportModal(type) {
  exportType.value = type
  showExportModal.value = true
}

async function confirmExport() {
  showExportModal.value = false
  isExporting.value = true

  try {
    if (exportType.value === 'excel') {
      await reportService.exportAgentReportExcel({ category: activeCategory.value })
      toast.success(`Agent ${activeCategoryLabel.value} Excel report downloaded successfully!`)
    } else {
      await reportService.exportAgentReportPdf({ category: activeCategory.value })
      toast.success(`Agent ${activeCategoryLabel.value} PDF report generated successfully!`)
    }
  } catch (err) {
    toast.error('Failed to export report.')
  } finally {
    isExporting.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>
