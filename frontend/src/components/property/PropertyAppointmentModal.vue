<template>
  <BaseModal
    :model-value="modelValue"
    title="Schedule a Property Tour"
    max-width="md"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <div v-if="!authStore.isAuthenticated" class="text-center py-4">
      <div class="w-12 h-12 rounded-full bg-slate-800 text-slate-300 mx-auto flex items-center justify-center mb-3">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>
      <h4 class="text-base font-semibold text-slate-100 mb-1">Login Required</h4>
      <p class="text-xs text-slate-400 mb-6">
        Please sign in or create an account to schedule property viewings.
      </p>
      <RouterLink
        :to="{ name: 'login', query: { redirect: $route.fullPath } }"
        class="inline-flex items-center justify-center w-full px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-white text-slate-950 text-sm font-bold transition-colors shadow-xs"
      >
        Sign In to Continue
      </RouterLink>
    </div>

    <form v-else @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Property Summary -->
      <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 flex items-center space-x-3">
        <div class="w-12 h-12 rounded-lg bg-slate-800 overflow-hidden flex-shrink-0">
          <img :src="propertyThumbnail" :alt="property.title" class="w-full h-full object-cover" @error="handleImgError" />
        </div>
        <div class="flex-1 min-w-0">
          <h5 class="text-xs font-semibold text-slate-200 truncate">{{ property.title }}</h5>
          <p class="text-xs text-slate-400 truncate">{{ locationText }}</p>
        </div>
      </div>

      <!-- Tour Type Selection -->
      <div>
        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
          Tour Type
        </label>
        <div class="grid grid-cols-2 gap-2">
          <button
            type="button"
            :class="[
              'py-2 px-3 rounded-lg text-xs font-semibold border transition-all flex items-center justify-center gap-1.5 cursor-pointer',
              tourType === 'in_person'
                ? 'bg-slate-100 text-slate-950 border-slate-100 shadow-xs'
                : 'bg-slate-900/80 border-slate-700 text-slate-300 hover:bg-slate-800 hover:text-white'
            ]"
            @click="selectTourType('in_person')"
          >
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            </svg>
            In-Person Tour
          </button>
          <button
            type="button"
            :class="[
              'py-2 px-3 rounded-lg text-xs font-semibold border transition-all flex items-center justify-center gap-1.5 cursor-pointer',
              tourType === 'virtual'
                ? 'bg-slate-100 text-slate-950 border-slate-100 shadow-xs'
                : 'bg-slate-900/80 border-slate-700 text-slate-300 hover:bg-slate-800 hover:text-white'
            ]"
            @click="selectTourType('virtual')"
          >
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            Virtual Tour
          </button>
        </div>
      </div>

      <!-- Date Selection -->
      <div>
        <label for="tour-date" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
          Select Date <span class="text-rose-400">*</span>
        </label>
        <input
          id="tour-date"
          v-model="tourDate"
          type="date"
          :min="minDate"
          required
          @input="error = ''"
          class="w-full rounded-lg text-sm bg-slate-900 border border-slate-700 focus:border-slate-400 focus:ring-2 focus:ring-slate-400/20 text-slate-100 p-2.5 focus:outline-none transition-colors"
        />
      </div>

      <!-- Available Slot Selection -->
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">
            Available Viewing Slot <span class="text-rose-400">*</span>
          </label>
          <span v-if="slotDuration" class="text-[10px] font-semibold text-emerald-400 bg-emerald-950/60 px-2 py-0.5 rounded border border-emerald-800/60">
            {{ slotDuration }}-min slots
          </span>
        </div>

        <div v-if="isLoadingSlots" class="py-4 text-center text-xs text-slate-400 flex items-center justify-center gap-2">
          <span class="w-3.5 h-3.5 rounded-full border-2 border-emerald-400 border-t-transparent animate-spin"></span>
          Checking seller's open schedule...
        </div>

        <div v-else-if="!isWorkingDay" class="p-3 bg-amber-950/30 border border-amber-800/60 rounded-xl text-center">
          <p class="text-xs text-amber-300 font-medium">Owner is not available for tours on this day.</p>
          <p class="text-[11px] text-slate-400 mt-1">Please select another date on the calendar above.</p>
        </div>

        <div v-else-if="availableSlots.length === 0" class="p-3 bg-slate-900 rounded-xl border border-slate-800 text-center text-xs text-slate-400">
          No remaining open slots for this date.
        </div>

        <div v-else class="grid grid-cols-3 sm:grid-cols-4 gap-1.5 max-h-44 overflow-y-auto pr-1">
          <button
            v-for="slot in availableSlots"
            :key="slot.time"
            type="button"
            :disabled="!slot.available"
            @click="selectSlot(slot)"
            :class="[
              'py-2 px-1.5 rounded-lg text-xs font-semibold border transition-all text-center flex flex-col items-center justify-center cursor-pointer',
              selectedSlot?.time === slot.time
                ? 'bg-emerald-600 text-white border-emerald-500 shadow-md ring-2 ring-emerald-400/40'
                : slot.available
                  ? 'bg-slate-900 border-slate-700 text-slate-200 hover:bg-slate-800 hover:border-slate-500'
                  : 'bg-slate-900/40 border-slate-800/50 text-slate-600 cursor-not-allowed opacity-40 line-through'
            ]"
            :title="slot.available ? 'Click to book ' + slot.display_time : (slot.reason === 'booked' ? 'Already booked' : 'Past slot')"
          >
            <span>{{ slot.display_time }}</span>
            <span v-if="!slot.available" class="text-[8px] no-underline font-normal text-slate-500">
              {{ slot.reason === 'booked' ? 'Booked' : 'Passed' }}
            </span>
          </button>
        </div>
      </div>

      <!-- Message / Notes -->
      <div>
        <label for="tour-message" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
          Message / Notes (Optional)
        </label>
        <textarea
          id="tour-message"
          v-model="tourMessage"
          rows="2"
          maxlength="1000"
          placeholder="Any specific questions or requests for the visit..."
          @input="error = ''"
          class="w-full rounded-lg text-sm bg-slate-900 border border-slate-700 focus:border-slate-400 focus:ring-2 focus:ring-slate-400/20 text-slate-100 placeholder-slate-500 p-2.5 focus:outline-none transition-colors resize-none"
        />
        <p v-if="error" class="mt-1.5 text-xs text-rose-400 font-medium flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          {{ error }}
        </p>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-end space-x-3 pt-2">
        <BaseButton variant="secondary" size="md" @click="$emit('update:modelValue', false)">
          Cancel
        </BaseButton>
        <BaseButton type="submit" variant="primary" size="md" :loading="isSubmitting">
          Confirm Appointment
        </BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import BaseModal from '../common/BaseModal.vue'
import BaseButton from '../common/BaseButton.vue'
import { useAuthStore } from '../../stores/auth'
import { useToastStore } from '../../stores/toast'
import { appointmentService } from '../../services/appointmentService'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  property: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['update:modelValue', 'success'])

const route = useRoute()
const authStore = useAuthStore()
const toastStore = useToastStore()

// Local date calculation for minimum booking date (tomorrow)
const getTomorrowString = () => {
  const d = new Date()
  d.setDate(d.getDate() + 1)
  const y = d.getFullYear()
  const m = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${y}-${m}-${day}`
}
const minDate = getTomorrowString()

const tourType = ref('in_person')
const tourDate = ref(minDate)
const tourTime = ref('')
const tourMessage = ref('')
const isSubmitting = ref(false)
const error = ref('')

// Seller-availability slot state
const availableSlots = ref([])
const isLoadingSlots = ref(false)
const isWorkingDay = ref(true)
const slotDuration = ref(30)
const selectedSlot = ref(null)

const selectSlot = (slot) => {
  if (!slot.available) return
  selectedSlot.value = slot
  tourTime.value = slot.time
  error.value = ''
}

const loadSlots = async () => {
  if (!props.property?.id || !tourDate.value) return
  isLoadingSlots.value = true
  selectedSlot.value = null
  tourTime.value = ''
  try {
    const res = await appointmentService.getAvailableSlots(props.property.id, tourDate.value)
    const data = res.data || res || {}
    isWorkingDay.value = data.is_working_day !== false
    slotDuration.value = data.slot_duration_minutes || 30
    availableSlots.value = data.slots || []
    // Auto-select first available open slot
    const firstOpen = availableSlots.value.find(s => s.available)
    if (firstOpen) {
      selectSlot(firstOpen)
    }
  } catch (err) {
    console.warn('Failed to load slots:', err)
    availableSlots.value = [
      { time: '09:00', display_time: '9:00 AM', available: true },
      { time: '10:00', display_time: '10:00 AM', available: true },
      { time: '11:00', display_time: '11:00 AM', available: true },
      { time: '14:00', display_time: '2:00 PM', available: true },
      { time: '15:00', display_time: '3:00 PM', available: true },
      { time: '16:00', display_time: '4:00 PM', available: true },
    ]
    selectSlot(availableSlots.value[0])
  } finally {
    isLoadingSlots.value = false
  }
}

watch(() => tourDate.value, () => {
  loadSlots()
})

watch(() => props.modelValue, (isOpen) => {
  if (isOpen) {
    loadSlots()
  }
})

onMounted(() => {
  if (props.modelValue) {
    loadSlots()
  }
})

const selectTourType = (type) => {
  tourType.value = type
  error.value = ''
}

const fallbackThumbnail = 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=200&q=80'

const propertyThumbnail = computed(() => {
  const p = props.property
  return (
    p.primary_image?.image_url ||
    p.primary_image?.url ||
    p.primaryImage?.url ||
    p.primaryImage?.image_url ||
    p.images?.[0]?.image_url ||
    p.images?.[0]?.url ||
    fallbackThumbnail
  )
})

const locationText = computed(() => {
  const p = props.property
  if (!p) return 'Addis Ababa, Ethiopia'
  const parts = []

  const extractName = (val) => {
    if (!val) return ''
    if (typeof val === 'string') return val.trim()
    if (typeof val === 'object') return val.name || val.title || val.slug || ''
    return String(val)
  }

  const subCity = extractName(p.address?.subCity) || extractName(p.address?.sub_city) || extractName(p.subcity) || extractName(p.sub_city)
  if (subCity && !subCity.includes('[object Object]')) parts.push(subCity)

  const city = extractName(p.address?.city) || extractName(p.city)
  if (city && !city.includes('[object Object]')) parts.push(city)

  if (parts.length > 0) return parts.join(', ')
  if (p.location && typeof p.location === 'string' && !p.location.includes('[object Object]')) return p.location
  return 'Addis Ababa, Ethiopia'
})

const handleImgError = (e) => {
  e.target.src = fallbackThumbnail
}

const handleSubmit = async () => {
  if (authStore.user?.id) {
    const role = (authStore.user?.role || authStore.user?.roles?.[0]?.name || '').toLowerCase()
    if (role === 'admin') {
      error.value = 'Administrators cannot book tours. Please use a buyer account.'
      toastStore.error(error.value)
      return
    }

    const currentUserId = Number(authStore.user.id)
    const propertyOwnerId = Number(props.property.user_id || props.property.user?.id || props.property.owner_id || props.property.owner?.id)
    if (propertyOwnerId && currentUserId === propertyOwnerId) {
      error.value = 'You cannot book a tour for your own property.'
      toastStore.error(error.value)
      return
    }
  }

  if (!tourDate.value) {
    error.value = 'Please select an appointment date.'
    return
  }

  const today = new Date().toISOString().split('T')[0]
  if (tourDate.value < today) {
    error.value = 'Appointment date cannot be in the past.'
    return
  }

  if (!tourTime.value) {
    error.value = 'Please select an open viewing slot.'
    return
  }

  isSubmitting.value = true
  error.value = ''

  try {
    const scheduledAt = `${tourDate.value} ${tourTime.value}:00`

    await appointmentService.bookAppointment({
      property_id: props.property.id,
      scheduled_at: scheduledAt,
      type: tourType.value,
      message: tourMessage.value.trim() || undefined,
    })

    toastStore.success('Appointment requested!')
    emit('update:modelValue', false)
    emit('success')
  } catch (err) {
    error.value = err.message || 'Failed to book appointment.'
    toastStore.error(error.value)
  } finally {
    isSubmitting.value = false
  }
}
</script>
