<template>
  <div class="space-y-4">
    <!-- Tab Navigation -->
    <div class="bg-white rounded-lg border border-gray-200">
      <div class="flex border-b border-gray-200">
        <button
          v-for="tab in tabs" :key="tab.id"
          @click="activeTab = tab.id"
          :class="activeTab === tab.id ? 'border-b-2 border-emerald-600 text-emerald-600' : 'text-gray-600 hover:text-gray-900'"
          class="px-4 py-3 text-sm font-semibold transition-colors"
        >
          {{ tab.label }}
        </button>
      </div>
    </div>

    <!-- Report Content -->
    <div class="bg-white rounded-lg border border-gray-200 p-5">
      <!-- Export buttons -->
      <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-200">
        <span class="text-sm text-gray-600">Export:</span>
        <button
          @click="exportReport('csv')"
          class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-600 text-sm font-medium rounded-lg hover:bg-blue-100"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          CSV
        </button>
        <button
          @click="exportReport('pdf')"
          class="flex items-center gap-2 px-3 py-1.5 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          PDF
        </button>
        <button
          @click="exportReport('excel')"
          class="flex items-center gap-2 px-3 py-1.5 bg-green-50 text-green-600 text-sm font-medium rounded-lg hover:bg-green-100"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Excel
        </button>
        <button
          @click="printReport"
          class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2h-2.5A2.5 2.5 0 0012 5m0 0H9.5A2.5 2.5 0 007 7.5V9"/>
          </svg>
          Print
        </button>
        <div class="flex-1"></div>
        <span class="text-sm text-gray-600">{{ getReportData().length }} records</span>
      </div>

      <!-- Users Report Table -->
      <div v-if="activeTab === 'users'" class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Name</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Email</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Role</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Status</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Joined</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Last Active</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="user in usersReport" :key="user.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ user.name }}</td>
              <td class="px-4 py-3 text-sm text-gray-700">{{ user.email }}</td>
              <td class="px-4 py-3">
                <span :class="getRoleBadge(user.role)" class="px-2 py-0.5 rounded text-xs font-bold">
                  {{ user.role }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span :class="user.status === 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'" class="px-2 py-0.5 rounded text-xs font-bold">
                  {{ user.status }}
                </span>
              </td>
              <td class="px-4 py-3 text-xs text-gray-600">{{ user.joined_date }}</td>
              <td class="px-4 py-3 text-xs text-gray-600">{{ user.last_active }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Properties Report Table -->
      <div v-else-if="activeTab === 'properties'" class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Title</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Type</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Price</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Status</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Owner</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Views</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Listed</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="prop in propertiesReport" :key="prop.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ prop.title }}</td>
              <td class="px-4 py-3 text-sm text-gray-700">{{ prop.type }}</td>
              <td class="px-4 py-3 text-sm font-semibold text-gray-900">ETB {{ prop.price.toLocaleString() }}</td>
              <td class="px-4 py-3">
                <span :class="getStatusBadge(prop.status)" class="px-2 py-0.5 rounded text-xs font-bold">
                  {{ prop.status }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm text-gray-700">{{ prop.owner }}</td>
              <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ prop.views }}</td>
              <td class="px-4 py-3 text-xs text-gray-600">{{ prop.listed_date }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Appointments Report Table -->
      <div v-else-if="activeTab === 'appointments'" class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Property</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Visitor</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Date</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Time</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Status</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Notes</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="apt in appointmentsReport" :key="apt.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ apt.property }}</td>
              <td class="px-4 py-3 text-sm text-gray-700">{{ apt.visitor }}</td>
              <td class="px-4 py-3 text-sm text-gray-700">{{ apt.date }}</td>
              <td class="px-4 py-3 text-sm text-gray-700">{{ apt.time }}</td>
              <td class="px-4 py-3">
                <span :class="getAppointmentStatusBadge(apt.status)" class="px-2 py-0.5 rounded text-xs font-bold">
                  {{ apt.status }}
                </span>
              </td>
              <td class="px-4 py-3 text-xs text-gray-600">{{ apt.notes }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Financial Report -->
      <div v-else-if="activeTab === 'financial'" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
          <div class="bg-emerald-50 rounded-lg p-4">
            <p class="text-xs text-emerald-600 font-medium">Total Revenue</p>
            <p class="text-2xl font-bold text-emerald-900 mt-2">ETB 24.5M</p>
          </div>
          <div class="bg-blue-50 rounded-lg p-4">
            <p class="text-xs text-blue-600 font-medium">Completed Payments</p>
            <p class="text-2xl font-bold text-blue-900 mt-2">1,234</p>
          </div>
          <div class="bg-yellow-50 rounded-lg p-4">
            <p class="text-xs text-yellow-600 font-medium">Pending Payments</p>
            <p class="text-2xl font-bold text-yellow-900 mt-2">ETB 456K</p>
          </div>
          <div class="bg-red-50 rounded-lg p-4">
            <p class="text-xs text-red-600 font-medium">Failed Transactions</p>
            <p class="text-2xl font-bold text-red-900 mt-2">23</p>
          </div>
        </div>

        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Date</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Type</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Description</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Amount</th>
              <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="trans in transactionsReport" :key="trans.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 text-sm text-gray-700">{{ trans.date }}</td>
              <td class="px-4 py-3 text-sm text-gray-700">{{ trans.type }}</td>
              <td class="px-4 py-3 text-sm text-gray-700">{{ trans.description }}</td>
              <td class="px-4 py-3 text-sm font-semibold text-gray-900">ETB {{ trans.amount.toLocaleString() }}</td>
              <td class="px-4 py-3">
                <span :class="getPaymentStatusBadge(trans.status)" class="px-2 py-0.5 rounded text-xs font-bold">
                  {{ trans.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const activeTab = ref('users')

const tabs = [
  { id: 'users', label: 'User Report' },
  { id: 'properties', label: 'Property Report' },
  { id: 'appointments', label: 'Appointments' },
  { id: 'financial', label: 'Financial' },
]

// User Report Data
const usersReport = ref([
  { id: 1, name: 'Abebe Kebede', email: 'abebe@example.com', role: 'Buyer', status: 'Active', joined_date: '2024-01-15', last_active: '2 hours ago' },
  { id: 2, name: 'Sara Mohammed', email: 'sara@example.com', role: 'Owner', status: 'Active', joined_date: '2024-01-20', last_active: '30 min ago' },
  { id: 3, name: 'Daniel Tesfaye', email: 'daniel@example.com', role: 'Agent', status: 'Active', joined_date: '2024-02-01', last_active: 'Yesterday' },
  { id: 4, name: 'Hiwot Alemu', email: 'hiwot@example.com', role: 'Buyer', status: 'Inactive', joined_date: '2024-02-05', last_active: '1 week ago' },
])

// Properties Report Data
const propertiesReport = ref([
  { id: 1, title: 'Luxurious Villa in Bole', type: 'Residential', price: 5000000, status: 'Active', owner: 'Getachew Assefa', views: 342, listed_date: '2024-01-10' },
  { id: 2, title: 'Modern Apartment Kazanchis', type: 'Residential', price: 2500000, status: 'Active', owner: 'Sara Mohammed', views: 198, listed_date: '2024-01-15' },
  { id: 3, title: 'Commercial Space CMC', type: 'Commercial', price: 8000000, status: 'Pending', owner: 'Hiwot Alemu', views: 87, listed_date: '2024-02-01' },
])

// Appointments Report Data
const appointmentsReport = ref([
  { id: 1, property: 'Bole Villa', visitor: 'Abebe K.', date: '2024-08-14', time: '10:00 AM', status: 'Confirmed', notes: 'Very interested' },
  { id: 2, property: 'Kazanchis Apt', visitor: 'Meron T.', date: '2024-08-14', time: '02:00 PM', status: 'Completed', notes: 'Viewed property' },
  { id: 3, property: 'CMC Office', visitor: 'Yohannes B.', date: '2024-08-15', time: '11:00 AM', status: 'Pending', notes: '' },
])

// Financial Report Data
const transactionsReport = ref([
  { id: 1, date: '2024-08-14', type: 'Commission', description: 'Property sale commission', amount: 125000, status: 'Completed' },
  { id: 2, date: '2024-08-13', type: 'Subscription', description: 'Premium plan - Monthly', amount: 5000, status: 'Completed' },
  { id: 3, date: '2024-08-12', type: 'Refund', description: 'Refund for cancelled booking', amount: 75000, status: 'Completed' },
  { id: 4, date: '2024-08-11', type: 'Listing Fee', description: 'Property listing fee', amount: 10000, status: 'Pending' },
])

const getRoleBadge = (role) => {
  const badges = {
    'Buyer': 'bg-blue-100 text-blue-700',
    'Owner': 'bg-emerald-100 text-emerald-700',
    'Agent': 'bg-purple-100 text-purple-700',
    'Admin': 'bg-red-100 text-red-700',
  }
  return badges[role] || 'bg-gray-100 text-gray-700'
}

const getStatusBadge = (status) => {
  const badges = {
    'Active': 'bg-emerald-100 text-emerald-700',
    'Pending': 'bg-yellow-100 text-yellow-700',
    'Rejected': 'bg-red-100 text-red-700',
    'Sold': 'bg-blue-100 text-blue-700',
  }
  return badges[status] || 'bg-gray-100 text-gray-700'
}

const getAppointmentStatusBadge = (status) => {
  const badges = {
    'Confirmed': 'bg-emerald-100 text-emerald-700',
    'Pending': 'bg-yellow-100 text-yellow-700',
    'Completed': 'bg-blue-100 text-blue-700',
    'Cancelled': 'bg-red-100 text-red-700',
  }
  return badges[status] || 'bg-gray-100 text-gray-700'
}

const getPaymentStatusBadge = (status) => {
  const badges = {
    'Completed': 'bg-emerald-100 text-emerald-700',
    'Pending': 'bg-yellow-100 text-yellow-700',
    'Failed': 'bg-red-100 text-red-700',
  }
  return badges[status] || 'bg-gray-100 text-gray-700'
}

const getReportData = () => {
  const reports = {
    'users': usersReport.value,
    'properties': propertiesReport.value,
    'appointments': appointmentsReport.value,
    'financial': transactionsReport.value,
  }
  return reports[activeTab.value] || []
}

const exportReport = (format) => {
  alert(`Exporting ${format.toUpperCase()} report with ${getReportData().length} records`)
}

const printReport = () => {
  window.print()
}
</script>
