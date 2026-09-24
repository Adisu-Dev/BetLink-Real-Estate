<template>
  <div class="space-y-6">
    <!-- Header with Live Metric Chips -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Client Leads CRM</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Track prospective buyer inquiries and sales pipeline progression.</p>
      </div>

      <!-- Quick Metrics -->
      <div class="flex items-center gap-2 text-xs">
        <div class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 shadow-xs">
          <span class="text-slate-400 font-bold uppercase text-[10px]">Total: </span>
          <span class="font-black text-slate-900 dark:text-white">{{ leads.length }}</span>
        </div>
        <div class="px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300/80 dark:border-emerald-800 shadow-xs">
          <span class="text-emerald-600 dark:text-emerald-400 font-bold uppercase text-[10px]">Active: </span>
          <span class="font-black text-emerald-700 dark:text-emerald-300">{{ activeLeadsCount }}</span>
        </div>
      </div>
    </div>

    <!-- Search & Pipeline Filter Bar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 transition-colors">
      <div class="relative flex-1 max-w-md">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by client name, phone, or property..."
          class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-xl focus:ring-2 focus:ring-slate-400 focus:outline-none transition-colors"
        />
        <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
      </div>

      <div class="flex items-center gap-2">
        <select
          v-model="selectedStage"
          class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold focus:outline-none cursor-pointer"
        >
          <option value="all">Pipeline</option>
          <option value="New">New</option>
          <option value="Contacted">Contacted</option>
          <option value="Tour Scheduled">Tour Scheduled</option>
          <option value="Negotiating">Negotiating</option>
          <option value="Closed">Closed</option>
          <option value="Lost">Lost</option>
        </select>
      </div>
    </div>

    <!-- Error State with Retry -->
    <div v-if="apiError" class="p-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl text-center space-y-3">
      <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">{{ apiError }}</p>
      <button 
        @click="fetchLeads" 
        class="px-3.5 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold rounded-xl hover:bg-slate-800 transition-colors shadow-xs cursor-pointer"
      >
        Retry Loading
      </button>
    </div>

    <!-- Loading Skeleton State -->
    <div v-else-if="isLoading" class="space-y-4">
      <div v-for="n in 3" :key="n" class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-300 dark:border-slate-800 animate-pulse h-20"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredLeads.length === 0" class="bg-white dark:bg-slate-900 rounded-2xl p-10 border border-slate-300 dark:border-slate-800 text-center space-y-3">
      <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center">
        <Users class="w-6 h-6" />
      </div>
      <h3 class="text-sm font-bold text-slate-900 dark:text-white">No client leads found</h3>
      <p class="text-xs text-slate-500 max-w-sm mx-auto">When prospective buyers inquire about your properties, they will appear in this pipeline.</p>
    </div>

    <!-- Leads CRM Table -->
    <div v-else class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-300 dark:border-slate-800 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-300 dark:border-slate-800">
            <tr>
              <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Client</th>
              <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Interest & Property</th>
              <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Stage</th>
              <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Inquiry Note</th>
              <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
            <tr v-for="lead in filteredLeads" :key="lead.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
              <!-- Client Column -->
              <td class="px-5 py-3.5">
                <div class="flex items-center gap-3">
                  <img :src="lead.clientAvatar" :alt="lead.clientName" class="w-10 h-10 rounded-full object-cover border border-slate-300 dark:border-slate-700 shrink-0" />
                  <div class="min-w-0">
                    <p class="font-bold text-slate-900 dark:text-white truncate">{{ lead.clientName }}</p>
                    <p class="text-[11px] text-slate-500 truncate">{{ lead.phone }}</p>
                  </div>
                </div>
              </td>

              <!-- Property Column -->
              <td class="px-5 py-3.5">
                <p class="font-bold text-slate-900 dark:text-white truncate max-w-[200px]">{{ lead.propertyTitle }}</p>
                <p class="text-[11px] text-slate-400">{{ lead.budget }} &bull; {{ lead.interest }}</p>
              </td>

              <!-- Stage Selector Dropdown -->
              <td class="px-5 py-3.5">
                <select
                  v-model="lead.status"
                  @change="changeLeadStage(lead)"
                  class="px-2.5 py-1 text-[11px] font-bold rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:outline-none cursor-pointer"
                >
                  <option value="New">New</option>
                  <option value="Contacted">Contacted</option>
                  <option value="Tour Scheduled">Tour Scheduled</option>
                  <option value="Negotiating">Negotiating</option>
                  <option value="Closed">Closed</option>
                  <option value="Lost">Lost</option>
                </select>
              </td>

              <!-- Last Message -->
              <td class="px-5 py-3.5 max-w-xs">
                <p class="text-slate-600 dark:text-slate-400 truncate">{{ lead.lastMessage }}</p>
                <span class="text-[10px] text-slate-400">{{ lead.time }}</span>
              </td>

              <!-- Action Buttons -->
              <td class="px-5 py-3.5 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <a
                    :href="`tel:${lead.phone}`"
                    class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors"
                    title="Call Client"
                  >
                    <Phone class="w-3.5 h-3.5" />
                  </a>
                  <RouterLink
                    to="/agent/messages"
                    class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200/80 dark:border-slate-700/80 transition-colors"
                    title="Open Chat"
                  >
                    <MessageSquare class="w-3.5 h-3.5 text-slate-500" />
                  </RouterLink>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { Search, Users, Phone, MessageSquare } from 'lucide-vue-next'
import { agentService } from '@/services/agentService'
import { useToastStore } from '@/stores/toast'

const toast = useToastStore()

const isLoading = ref(true)
const apiError = ref(null)
const leads = ref([])
const searchQuery = ref('')
const selectedStage = ref('all')

const activeLeadsCount = computed(() => {
  return leads.value.filter(l => !['Closed', 'Lost'].includes(l.status)).length
})

const filteredLeads = computed(() => {
  return leads.value.filter(l => {
    const matchSearch = !searchQuery.value ||
      l.clientName?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      l.phone?.includes(searchQuery.value) ||
      l.propertyTitle?.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchStage = selectedStage.value === 'all' || l.status === selectedStage.value
    return matchSearch && matchStage
  })
})

async function fetchLeads() {
  isLoading.value = true
  apiError.value = null

  try {
    const res = await agentService.getLeads()
    const data = res.data?.data || res.data || []
    leads.value = data
  } catch (err) {
    console.error('Failed to fetch client leads:', err)
    apiError.value = 'Failed to load client leads.'
  } finally {
    isLoading.value = false
  }
}

async function changeLeadStage(lead) {
  try {
    await agentService.updateLeadStatus(lead.id, { status: lead.status })
    toast.success(`Lead stage updated to ${lead.status.toUpperCase()}`)
  } catch (err) {
    toast.error('Failed to update lead stage.')
  }
}

onMounted(() => {
  fetchLeads()
})
</script>
