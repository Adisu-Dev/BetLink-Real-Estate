<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div
      @click="closeModal"
      class="absolute inset-0 bg-black/50 transition-opacity duration-200"
    ></div>

    <!-- Modal -->
    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-900">Schedule Appointment</h2>
        <button
          @click="closeModal"
          class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="p-6 space-y-5">
        <!-- Property -->
        <div>
          <label for="property" class="block text-sm font-medium text-gray-700 mb-2">
            Select Property <span class="text-red-600">*</span>
          </label>
          <select
            id="property"
            v-model="form.property_id"
            :class="[
              'w-full px-4 py-2 rounded-lg border transition-all duration-200',
              'focus:outline-none focus:ring-2',
              errors.property_id 
                ? 'border-red-300 focus:ring-red-500' 
                : 'border-gray-300 focus:ring-blue-500'
            ]"
            @blur="validateField('property_id')"
            @input="errors.property_id = ''"
          >
            <option value="">Choose a property</option>
            <option v-for="prop in properties" :key="prop.id" :value="prop.id">
              {{ prop.title }}
            </option>
          </select>
          <p v-if="errors.property_id" class="mt-1 text-sm text-red-600">{{ errors.property_id }}</p>
        </div>

        <!-- Date -->
        <div>
          <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
            Date <span class="text-red-600">*</span>
          </label>
          <input
            id="date"
            v-model="form.date"
            type="date"
            :class="[
              'w-full px-4 py-2 rounded-lg border transition-all duration-200',
              'focus:outline-none focus:ring-2',
              errors.date 
                ? 'border-red-300 focus:ring-red-500' 
                : 'border-gray-300 focus:ring-blue-500'
            ]"
            @blur="validateField('date')"
            @input="errors.date = ''"
          />
          <p v-if="errors.date" class="mt-1 text-sm text-red-600">{{ errors.date }}</p>
        </div>

        <!-- Time -->
        <div>
          <label for="time" class="block text-sm font-medium text-gray-700 mb-2">
            Time <span class="text-red-600">*</span>
          </label>
          <input
            id="time"
            v-model="form.time"
            type="time"
            :class="[
              'w-full px-4 py-2 rounded-lg border transition-all duration-200',
              'focus:outline-none focus:ring-2',
              errors.time 
                ? 'border-red-300 focus:ring-red-500' 
                : 'border-gray-300 focus:ring-blue-500'
            ]"
            @blur="validateField('time')"
            @input="errors.time = ''"
          />
          <p v-if="errors.time" class="mt-1 text-sm text-red-600">{{ errors.time }}</p>
        </div>

        <!-- Type -->
        <div>
          <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
            Appointment Type <span class="text-red-600">*</span>
          </label>
          <select
            id="type"
            v-model="form.type"
            :class="[
              'w-full px-4 py-2 rounded-lg border transition-all duration-200',
              'focus:outline-none focus:ring-2',
              errors.type 
                ? 'border-red-300 focus:ring-red-500' 
                : 'border-gray-300 focus:ring-blue-500'
            ]"
            @blur="validateField('type')"
            @input="errors.type = ''"
          >
            <option value="">Select type</option>
            <option value="viewing">Property Viewing</option>
            <option value="inspection">Inspection</option>
            <option value="negotiation">Negotiation</option>
            <option value="other">Other</option>
          </select>
          <p v-if="errors.type" class="mt-1 text-sm text-red-600">{{ errors.type }}</p>
        </div>

        <!-- Notes -->
        <div>
          <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
            Notes
          </label>
          <textarea
            id="notes"
            v-model="form.notes"
            rows="3"
            placeholder="Any additional details about the appointment..."
            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <!-- Error Message -->
        <transition name="fade">
          <div v-if="submitError" class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-700">{{ submitError }}</p>
          </div>
        </transition>

        <!-- Buttons -->
        <div class="flex gap-3 pt-4 border-t border-gray-200">
          <button
            type="button"
            @click="closeModal"
            :disabled="loading"
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <svg v-if="loading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ loading ? 'Scheduling...' : 'Schedule Appointment' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useAppointment } from '../../composables/useAppointment'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  properties: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close', 'appointment-scheduled'])

const { createAppointment } = useAppointment()
const loading = ref(false)
const submitError = ref('')

const form = reactive({
  property_id: '',
  date: '',
  time: '',
  type: '',
  notes: ''
})

const errors = reactive({
  property_id: '',
  date: '',
  time: '',
  type: ''
})

const validateField = (fieldName) => {
  errors[fieldName] = ''

  switch (fieldName) {
    case 'property_id':
      if (!form.property_id) {
        errors.property_id = 'Please select a property'
      }
      break

    case 'date':
      if (!form.date) {
        errors.date = 'Date is required'
      } else {
        const selectedDate = new Date(form.date)
        const today = new Date()
        today.setHours(0, 0, 0, 0)
        if (selectedDate < today) {
          errors.date = 'Please select a future date'
        }
      }
      break

    case 'time':
      if (!form.time) {
        errors.time = 'Time is required'
      }
      break

    case 'type':
      if (!form.type) {
        errors.type = 'Appointment type is required'
      }
      break
  }
}

const validateForm = () => {
  const fieldsToValidate = ['property_id', 'date', 'time', 'type']
  fieldsToValidate.forEach(field => validateField(field))

  return Object.values(errors).every(error => !error)
}

const handleSubmit = async () => {
  submitError.value = ''

  if (!validateForm()) {
    return
  }

  loading.value = true

  try {
    const appointmentData = {
      property_id: form.property_id,
      scheduled_date: form.date,
      scheduled_time: form.time,
      type: form.type,
      notes: form.notes
    }

    const result = await createAppointment(appointmentData)

    if (result.success) {
      emit('appointment-scheduled', result.data)
      resetForm()
      closeModal()
    } else {
      submitError.value = result.error || 'Failed to schedule appointment'
    }
  } catch (err) {
    console.error('Error creating appointment:', err)
    submitError.value = 'An unexpected error occurred. Please try again.'
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  form.property_id = ''
  form.date = ''
  form.time = ''
  form.type = ''
  form.notes = ''

  Object.keys(errors).forEach(key => {
    errors[key] = ''
  })

  submitError.value = ''
}

const closeModal = () => {
  resetForm()
  emit('close')
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
