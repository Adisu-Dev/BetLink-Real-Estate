<template>
  <div>
    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mb-6">My Bookings</h1>

    <div v-if="bookings.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="booking in bookings" 
        :key="booking.id" 
        class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden"
      >
        <div class="relative h-44 overflow-hidden bg-slate-200 dark:bg-slate-800">
          <img :src="booking.image" :alt="booking.property" class="w-full h-full object-cover" />
          <StatusBadge :status="booking.status" class="absolute top-3 right-3" />
        </div>
        <div class="p-5">
          <h3 class="font-bold text-slate-900 dark:text-white mb-2 truncate">{{ booking.property }}</h3>
          <div class="space-y-1.5 text-xs text-slate-600 dark:text-slate-400 mb-4">
            <p>📅 {{ booking.checkIn }} to {{ booking.checkOut }}</p>
            <p>💰 Total: <span class="font-bold text-slate-900 dark:text-white">ETB {{ Number(booking.total).toLocaleString() }}</span></p>
            <p class="text-[11px] text-slate-400">🔑 Reference: {{ booking.propertyId }}</p>
          </div>
          <div class="flex gap-2">
            <RouterLink 
              :to="`/properties`"
              class="flex-1 py-2.5 px-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-200 font-bold text-xs text-center transition-colors"
            >
              View Details
            </RouterLink>
            <button 
              v-if="booking.status === 'confirmed' || booking.status === 'pending'" 
              @click="promptCancelBooking(booking)"
              class="flex-1 py-2.5 px-3 border border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-400 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/40 font-bold text-xs transition-colors"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <EmptyState
      v-else
      title="No bookings yet"
      description="Start booking properties to see them here"
      actionLabel="Browse Properties"
    />

    <!-- Cancel Booking Confirmation Modal -->
    <ConfirmModal
      :isOpen="showCancelModal"
      title="Cancel Booking"
      :message="`Are you sure you want to cancel your booking for '${bookingToCancel?.property || 'this stay'}'?`"
      confirmLabel="Yes, Cancel Booking"
      cancelLabel="Keep Booking"
      :danger="true"
      @confirm="confirmCancelBooking"
      @cancel="showCancelModal = false; bookingToCancel = null"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import StatusBadge from '../../components/dashboard/StatusBadge.vue'
import EmptyState from '../../components/dashboard/EmptyState.vue'
import ConfirmModal from '../../components/dashboard/ConfirmModal.vue'
import { useToastStore } from '../../stores/toast'

const toastStore = useToastStore()
const showCancelModal = ref(false)
const bookingToCancel = ref(null)

const bookings = ref([
  {
    id: 1,
    property: 'Modern Apartment Downtown',
    propertyId: 'PROP-001',
    checkIn: '2026-03-20',
    checkOut: '2026-03-25',
    total: 15000,
    status: 'confirmed',
    image: 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=400&h=300&fit=crop'
  },
  {
    id: 2,
    property: 'Cozy Studio Apartment',
    propertyId: 'PROP-003',
    checkIn: '2026-04-01',
    checkOut: '2026-04-08',
    total: 12000,
    status: 'pending',
    image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop'
  }
])

const promptCancelBooking = (booking) => {
  bookingToCancel.value = booking
  showCancelModal.value = true
}

const confirmCancelBooking = () => {
  if (bookingToCancel.value) {
    const idx = bookings.value.findIndex(b => b.id === bookingToCancel.value.id)
    if (idx !== -1) {
      bookings.value[idx].status = 'cancelled'
      toastStore.success('Booking cancelled successfully!')
    }
  }
  showCancelModal.value = false
  bookingToCancel.value = null
}
</script>

