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
          <span class="ml-2 px-2 py-0.5 bg-gray-100 rounded-full text-xs">{{ tab.count }}</span>
        </button>
      </div>

      <!-- Bookings List -->
      <div class="divide-y divide-gray-100">
        <div v-for="booking in filteredBookings" :key="booking.id" class="p-5 hover:bg-gray-50 transition-colors">
          <div class="flex gap-4 items-start mb-4">
            <!-- Property Image -->
            <img
              :src="booking.image"
              :alt="booking.propertyName"
              class="w-24 h-24 rounded-lg object-cover flex-shrink-0"
            />

            <!-- Booking Details -->
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-2 mb-2">
                <div>
                  <h3 class="font-bold text-gray-900">{{ booking.propertyName }}</h3>
                  <p class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    {{ booking.location }}
                  </p>
                </div>
                <span :class="booking.statusBadge" class="px-3 py-1 text-xs font-bold rounded-full flex-shrink-0">
                  {{ booking.status }}
                </span>
              </div>

              <!-- Booking Info Grid -->
              <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3">
                <div>
                  <p class="text-xs text-gray-500">Check-in</p>
                  <p class="text-sm font-semibold text-gray-900">{{ booking.checkIn }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-500">Check-out</p>
                  <p class="text-sm font-semibold text-gray-900">{{ booking.checkOut }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-500">Duration</p>
                  <p class="text-sm font-semibold text-gray-900">{{ booking.duration }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-500">Total Amount</p>
                  <p class="text-sm font-bold text-emerald-600">ETB {{ booking.amount.toLocaleString() }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-2 flex-wrap">
            <button
              @click="viewBooking(booking)"
              class="px-4 py-2 border border-emerald-600 text-emerald-600 font-semibold rounded-lg hover:bg-emerald-50 transition-colors text-sm"
            >
              View Details
            </button>
            <template v-if="booking.status === 'Confirmed'">
              <button
                @click="rescheduleBooking(booking.id)"
                class="px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-100 transition-colors text-sm"
              >
                Reschedule
              </button>
              <button
                @click="cancelBooking(booking.id)"
                class="px-4 py-2 border border-red-300 text-red-600 font-semibold rounded-lg hover:bg-red-50 transition-colors text-sm"
              >
                Cancel
              </button>
            </template>
            <template v-else-if="booking.status === 'Completed'">
              <button
                class="px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-100 transition-colors text-sm"
              >
                Leave Review
              </button>
            </template>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredBookings.length === 0" class="px-5 py-12 text-center">
          <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <p class="text-gray-500 font-medium">No bookings {{ activeTab }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const activeTab = ref('confirmed')

const tabs = [
  { id: 'confirmed', label: 'Confirmed', count: 2 },
  { id: 'pending', label: 'Pending', count: 1 },
  { id: 'completed', label: 'Completed', count: 4 },
  { id: 'cancelled', label: 'Cancelled', count: 0 },
]

const bookings = ref([
  {
    id: 1,
    propertyName: 'Bole Luxury Apartment',
    location: 'Bole, Addis Ababa',
    image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=300&h=300&fit=crop',
    status: 'Confirmed',
    statusBadge: 'bg-blue-100 text-blue-700',
    checkIn: 'Dec 15, 2024',
    checkOut: 'Dec 30, 2024',
    duration: '15 days',
    amount: 225000,
  },
  {
    id: 2,
    propertyName: 'CMC Commercial Office',
    location: 'CMC, Addis Ababa',
    image: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=300&h=300&fit=crop',
    status: 'Pending',
    statusBadge: 'bg-yellow-100 text-yellow-700',
    checkIn: 'Dec 20, 2024',
    checkOut: 'Jan 5, 2025',
    duration: '16 days',
    amount: 400000,
  },
  {
    id: 3,
    propertyName: 'Nifas Silk Studio',
    location: 'Nifas Silk, Addis Ababa',
    image: 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=300&h=300&fit=crop',
    status: 'Completed',
    statusBadge: 'bg-emerald-100 text-emerald-700',
    checkIn: 'Oct 1, 2024',
    checkOut: 'Oct 30, 2024',
    duration: '30 days',
    amount: 240000,
  },
  {
    id: 4,
    propertyName: 'Piazza Contemporary Suite',
    location: 'Piazza, Addis Ababa',
    image: 'https://images.unsplash.com/photo-1534080564897-61f3b3f786d7?w=300&h=300&fit=crop',
    status: 'Confirmed',
    statusBadge: 'bg-blue-100 text-blue-700',
    checkIn: 'Jan 10, 2025',
    checkOut: 'Jan 20, 2025',
    duration: '10 days',
    amount: 180000,
  },
  {
    id: 5,
    propertyName: 'Summit Residence Villa',
    location: 'Summit, Addis Ababa',
    image: 'https://images.unsplash.com/photo-1512216548029-956a92485718?w=300&h=300&fit=crop',
    status: 'Completed',
    statusBadge: 'bg-emerald-100 text-emerald-700',
    checkIn: 'Jul 15, 2024',
    checkOut: 'Aug 15, 2024',
    duration: '31 days',
    amount: 1085000,
  },
  {
    id: 6,
    propertyName: 'Bole Garden Apartment',
    location: 'Bole, Addis Ababa',
    image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=300&h=300&fit=crop',
    status: 'Completed',
    statusBadge: 'bg-emerald-100 text-emerald-700',
    checkIn: 'May 1, 2024',
    checkOut: 'Jun 1, 2024',
    duration: '31 days',
    amount: 450000,
  },
])

const filteredBookings = computed(() => {
  if (activeTab.value === 'confirmed') {
    return bookings.value.filter(b => b.status === 'Confirmed')
  } else if (activeTab.value === 'pending') {
    return bookings.value.filter(b => b.status === 'Pending')
  } else if (activeTab.value === 'completed') {
    return bookings.value.filter(b => b.status === 'Completed')
  } else if (activeTab.value === 'cancelled') {
    return bookings.value.filter(b => b.status === 'Cancelled')
  }
  return bookings.value
})

const viewBooking = (booking) => {
  alert(`View details for booking: ${booking.propertyName}`)
}

const rescheduleBooking = (id) => {
  alert('Reschedule booking')
}

const cancelBooking = (id) => {
  if (confirm('Cancel this booking?')) {
    const booking = bookings.value.find(b => b.id === id)
    if (booking) {
      booking.status = 'Cancelled'
      booking.statusBadge = 'bg-red-100 text-red-700'
      alert('Booking cancelled successfully!')
    }
  }
}
</script>
