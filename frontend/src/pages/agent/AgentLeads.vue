<template>
  <div class="space-y-5">
    <!-- Filters -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 flex items-center gap-3 flex-wrap">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Search leads by name or phone..."
        class="flex-1 min-w-48 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
      />
      <select v-model="filterStatus" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        <option value="">All Status</option>
        <option value="new">New</option>
        <option value="contacted">Contacted</option>
        <option value="interested">Interested</option>
        <option value="converted">Converted</option>
      </select>
    </div>

    <!-- Leads Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
              <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase">Client</th>
              <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase">Contact</th>
              <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase">Interest</th>
              <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase">Status</th>
              <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase">Last Interaction</th>
              <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="lead in filteredLeads" :key="lead.id" class="hover:bg-gray-50">
              <td class="px-5 py-3">
                <div class="flex items-center gap-3">
                  <img :src="lead.avatar" :alt="lead.name" class="w-8 h-8 rounded-full" />
                  <span class="text-sm font-medium text-gray-900">{{ lead.name }}</span>
                </div>
              </td>
              <td class="px-5 py-3">
                <div class="text-xs">
                  <p class="text-gray-700">{{ lead.phone }}</p>
                  <p class="text-gray-500">{{ lead.email }}</p>
                </div>
              </td>
              <td class="px-5 py-3 text-sm text-gray-700">{{ lead.interest }}</td>
              <td class="px-5 py-3">
                <span :class="lead.statusBadge" class="px-2 py-0.5 text-xs font-bold rounded-full">
                  {{ lead.status }}
                </span>
              </td>
              <td class="px-5 py-3 text-xs text-gray-500">{{ lead.lastInteraction }}</td>
              <td class="px-5 py-3">
                <div class="flex gap-2">
                  <button
                    @click="contactLead(lead)"
                    class="px-2.5 py-1 bg-emerald-600 text-white text-xs font-semibold rounded hover:bg-emerald-700 transition-colors"
                  >
                    Call
                  </button>
                  <button
                    @click="updateStatus(lead)"
                    class="px-2.5 py-1 border border-gray-300 text-gray-700 text-xs font-semibold rounded hover:bg-gray-100 transition-colors"
                  >
                    Update
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-if="filteredLeads.length === 0" class="px-5 py-12 text-center text-gray-500">
        <p class="font-medium">No leads found</p>
      </div>
    </div>

    <!-- Conversion Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-semibold mb-1">Total Leads</p>
        <p class="text-2xl font-bold text-gray-900">{{ leads.length }}</p>
      </div>
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-semibold mb-1">Conversion Rate</p>
        <p class="text-2xl font-bold text-emerald-600">{{ conversionRate }}%</p>
      </div>
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-semibold mb-1">This Month</p>
        <p class="text-2xl font-bold text-gray-900">{{ thisMonthConversions }}</p>
      </div>
      <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-semibold mb-1">Avg Time to Sale</p>
        <p class="text-2xl font-bold text-gray-900">18 days</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const searchQuery = ref('')
const filterStatus = ref('')

const leads = ref([
  {
    id: 1,
    name: 'Hiwot Alemu',
    avatar: 'https://ui-avatars.com/api/?name=Hiwot+Alemu&background=f59e0b&color=fff&size=64',
    phone: '+251-911-234-567',
    email: 'hiwot@example.com',
    interest: '2BR in Bole, 10K-15K ETB',
    status: 'Converted',
    statusBadge: 'bg-emerald-100 text-emerald-700',
    lastInteraction: '2 days ago',
  },
  {
    id: 2,
    name: 'Yohannes Bekele',
    avatar: 'https://ui-avatars.com/api/?name=Yohannes+Bekele&background=3b82f6&color=fff&size=64',
    phone: '+251-922-345-678',
    email: 'yohannes@example.com',
    interest: 'Office space, 25K+ ETB',
    status: 'Interested',
    statusBadge: 'bg-blue-100 text-blue-700',
    lastInteraction: '5 hours ago',
  },
  {
    id: 3,
    name: 'Sara Mohammed',
    avatar: 'https://ui-avatars.com/api/?name=Sara+Mohammed&background=1e293b&color=fff&size=64',
    phone: '+251-933-456-789',
    email: 'sara@example.com',
    interest: 'Studio, affordable, <10K ETB',
    status: 'Contacted',
    statusBadge: 'bg-yellow-100 text-yellow-700',
    lastInteraction: '1 day ago',
  },
  {
    id: 4,
    name: 'Daniel Tesfaye',
    avatar: 'https://ui-avatars.com/api/?name=Daniel+Tesfaye&background=8b5cf6&color=fff&size=64',
    phone: '+251-944-567-890',
    email: 'daniel@example.com',
    interest: 'Villa, 4BR+, Bole area',
    status: 'Interested',
    statusBadge: 'bg-blue-100 text-blue-700',
    lastInteraction: '3 hours ago',
  },
  {
    id: 5,
    name: 'Meron Tadesse',
    avatar: 'https://ui-avatars.com/api/?name=Meron+Tadesse&background=ec4899&color=fff&size=64',
    phone: '+251-955-678-901',
    email: 'meron@example.com',
    interest: '1BR Apartment, Piazza',
    status: 'New',
    statusBadge: 'bg-gray-100 text-gray-700',
    lastInteraction: '30 minutes ago',
  },
])

const filteredLeads = computed(() => {
  let result = leads.value

  if (searchQuery.value) {
    result = result.filter(l =>
      l.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      l.phone.includes(searchQuery.value) ||
      l.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  if (filterStatus.value) {
    result = result.filter(l => l.status.toLowerCase() === filterStatus.value)
  }

  return result
})

const conversionRate = computed(() => {
  const converted = leads.value.filter(l => l.status === 'Converted').length
  return Math.round((converted / leads.value.length) * 100)
})

const thisMonthConversions = computed(() => {
  return leads.value.filter(l => l.status === 'Converted').length
})

const contactLead = (lead) => {
  alert(`Calling ${lead.name} at ${lead.phone}`)
}

const updateStatus = (lead) => {
  const statuses = ['New', 'Contacted', 'Interested', 'Converted']
  const currentIndex = statuses.indexOf(lead.status)
  const nextIndex = (currentIndex + 1) % statuses.length
  lead.status = statuses[nextIndex]
  
  const badgeColors = {
    'New': 'bg-gray-100 text-gray-700',
    'Contacted': 'bg-yellow-100 text-yellow-700',
    'Interested': 'bg-blue-100 text-blue-700',
    'Converted': 'bg-emerald-100 text-emerald-700',
  }
  lead.statusBadge = badgeColors[lead.status] || ''
  alert(`Status updated to: ${lead.status}`)
}
</script>
