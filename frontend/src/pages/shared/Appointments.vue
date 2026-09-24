<template>
  <div class="space-y-5">
    <!-- Tabs -->
    <div class="bg-white rounded-lg border border-gray-200">
      <div class="flex border-b border-gray-200">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'px-6 py-3 font-semibold text-sm transition-colors border-b-2 -mb-px',
            activeTab === tab.id
              ? 'border-emerald-600 text-emerald-600'
              : 'border-transparent text-gray-500 hover:text-gray-700'
          ]"
        >
          {{ tab.label }}
          <span v-if="tab.count" class="ml-2 px-2 py-0.5 bg-gray-100 rounded-full text-xs">{{ tab.count }}</span>
        </button>
      </div>

      <!-- Appointments List -->
      <div class="divide-y divide-gray-100">
        <div v-for="apt in filteredAppointments" :key="apt.id" class="p-4 hover:bg-gray-50 transition-colors">
          <div class="flex gap-4 items-start">
            <!-- Avatar/Icon -->
            <img
              :src="apt.avatar"
              :alt="apt.personName"
              class="w-12 h-12 rounded-lg object-cover flex-shrink-0"
            />

            <!-- Details -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between gap-2 mb-1">
                <h3 class="font-semibold text-gray-900 truncate">{{ apt.personName }}</h3>
                <span :class="apt.statusBadge" class="px-2 py-0.5 text-xs font-bold rounded-full flex-shrink-0">
                  {{ apt.status }}
                </span>
              </div>
              <p class="text-sm text-gray-600 mb-2">{{ apt.propertyName }}</p>
              <div class="flex flex-wrap gap-4 text-xs text-gray-500 mb-3">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  {{ apt.date }}
                </span>
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  {{ apt.time }}
                </span>
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  {{ apt.location }}
                </span>
              </div>

              <!-- Action Buttons (context-based) -->
              <div class="flex gap-2 flex-wrap">
                <template v-if="apt.status === 'Pending'">
                  <button @click="approveAppointment(apt.id)" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded hover:bg-emerald-700 transition-colors">
                    Approve
                  </button>
                  <button @click="rejectAppointment(apt.id)" class="px-3 py-1.5 border border-gray-300 text-gray-700 text-xs font-semibold rounded hover:bg-gray-50 transition-colors">
                    Reject
                  </button>
                </template>

                <template v-else-if="apt.status === 'Confirmed'">
                  <button @click="rescheduleAppointment(apt.id)" class="px-3 py-1.5 border border-gray-300 text-gray-700 text-xs font-semibold rounded hover:bg-gray-50 transition-colors">
                    Reschedule
                  </button>
                  <button @click="cancelAppointment(apt.id)" class="px-3 py-1.5 border border-red-300 text-red-600 text-xs font-semibold rounded hover:bg-red-50 transition-colors">
                    Cancel
                  </button>
                </template>

                <template v-else>
                  <button class="px-3 py-1.5 border border-gray-300 text-gray-700 text-xs font-semibold rounded hover:bg-gray-50 transition-colors">
                    View Details
                  </button>
                </template>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredAppointments.length === 0" class="px-6 py-12 text-center">
          <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <p class="text-gray-500 font-medium">No appointments {{ activeTab }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const activeTab = ref('all')

const tabs = [
  { id: 'all', label: 'All', count: 12 },
  { id: 'pending', label: 'Pending', count: 3 },
  { id: 'confirmed', label: 'Confirmed', count: 7 },
  { id: 'completed', label: 'Completed', count: 2 },
  { id: 'cancelled', label: 'Cancelled', count: 0 },
]

const appointments = ref([
  {
    id: 1,
    personName: 'Hiwot Alemu',
    propertyName: 'Bole Luxury Apartment',
    status: 'Pending',
    statusBadge: 'bg-yellow-100 text-yellow-700',
    date: 'Dec 15, 2024',
    time: '2:00 PM',
    location: 'Bole, Addis Ababa',
    avatar: 'https://ui-avatars.com/api/?name=Hiwot+Alemu&background=f59e0b&color=fff&size=64',
  },
  {
    id: 2,
    personName: 'Yohannes Bekele',
    propertyName: 'CMC Villa',
    status: 'Confirmed',
    statusBadge: 'bg-blue-100 text-blue-700',
    date: 'Dec 16, 2024',
    time: '10:30 AM',
    location: 'CMC, Addis Ababa',
    avatar: 'https://ui-avatars.com/api/?name=Yohannes+Bekele&background=3b82f6&color=fff&size=64',
  },
  {
    id: 3,
    personName: 'Sara Mohammed',
    propertyName: 'Nifas Silk Office Space',
    status: 'Completed',
    statusBadge: 'bg-emerald-100 text-emerald-700',
    date: 'Dec 10, 2024',
    time: '3:00 PM',
    location: 'Nifas Silk, Addis Ababa',
    avatar: 'https://ui-avatars.com/api/?name=Sara+Mohammed&background=1e293b&color=fff&size=64',
  },
  {
    id: 4,
    personName: 'Daniel Tesfaye',
    propertyName: 'Piazza Contemporary',
    status: 'Pending',
    statusBadge: 'bg-yellow-100 text-yellow-700',
    date: 'Dec 17, 2024',
    time: '4:00 PM',
    location: 'Piazza, Addis Ababa',
    avatar: 'https://ui-avatars.com/api/?name=Daniel+Tesfaye&background=8b5cf6&color=fff&size=64',
  },
  {
    id: 5,
    personName: 'Meron Tadesse',
    propertyName: 'Summit Residence',
    status: 'Confirmed',
    statusBadge: 'bg-blue-100 text-blue-700',
    date: 'Dec 18, 2024',
    time: '1:00 PM',
    location: 'Summit, Addis Ababa',
    avatar: 'https://ui-avatars.com/api/?name=Meron+Tadesse&background=ec4899&color=fff&size=64',
  },
])

const filteredAppointments = computed(() => {
  if (activeTab.value === 'all') return appointments.value
  return appointments.value.filter(apt => apt.status.toLowerCase() === activeTab.value)
})

const approveAppointment = (id) => {
  const apt = appointments.value.find(a => a.id === id)
  if (apt) {
    apt.status = 'Confirmed'
    apt.statusBadge = 'bg-blue-100 text-blue-700'
    alert('Appointment approved!')
  }
}

const rejectAppointment = (id) => {
  const apt = appointments.value.find(a => a.id === id)
  if (apt) {
    apt.status = 'Cancelled'
    apt.statusBadge = 'bg-red-100 text-red-700'
    alert('Appointment rejected!')
  }
}

const rescheduleAppointment = (id) => {
  const apt = appointments.value.find(a => a.id === id)
  if (apt) {
    alert(`Reschedule appointment with ${apt.personName}?`)
  }
}

const cancelAppointment = (id) => {
  const apt = appointments.value.find(a => a.id === id)
  if (apt) {
    apt.status = 'Cancelled'
    apt.statusBadge = 'bg-red-100 text-red-700'
    alert('Appointment cancelled!')
  }
}
</script>
