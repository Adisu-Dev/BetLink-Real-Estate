<template>
  <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-200">
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <p class="text-sm font-medium text-gray-600 mb-1">{{ title }}</p>
        <p class="text-3xl font-bold text-navy-950">{{ value }}</p>
        <p v-if="subtitle" class="text-xs text-gray-500 mt-2">{{ subtitle }}</p>
      </div>
      <div v-if="icon" :class="iconBgColor" class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0">
        <DashboardIcon :name="icon" :class="iconColor" class="w-6 h-6" />
      </div>
    </div>
    
    <!-- Trend indicator -->
    <div v-if="trend" class="mt-4 flex items-center text-sm">
      <svg v-if="trend.direction === 'up'" class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
      </svg>
      <svg v-else-if="trend.direction === 'down'" class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
      </svg>
      <span :class="trend.direction === 'up' ? 'text-green-600' : 'text-red-600'" class="font-medium">
        {{ trend.value }}
      </span>
      <span class="text-gray-500 ml-1">{{ trend.label }}</span>
    </div>

    <!-- Action button -->
    <button v-if="action" @click="handleAction" class="mt-4 text-sm text-active-blue hover:text-blue-700 font-medium">
      {{ action }}
    </button>
  </div>
</template>

<script setup>
import DashboardIcon from './DashboardIcon.vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: ''
  },
  iconColor: {
    type: String,
    default: 'text-navy-950'
  },
  iconBgColor: {
    type: String,
    default: 'bg-gray-100'
  },
  trend: {
    type: Object,
    default: null
  },
  action: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['action'])

function handleAction() {
  emit('action')
}
</script>
