<template>
  <button 
    @click="handleClick"
    class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-all duration-200 flex items-center gap-3 text-left group border border-transparent hover:border-active-blue"
    :class="{ 'bg-betlink-green text-white hover:bg-betlink-green-light': isPrimary }"
  >
    <div 
      :class="[isPrimary ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-active-blue/10', 'w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors']"
    >
      <DashboardIcon 
        :name="icon" 
        :class="[isPrimary ? 'text-white' : 'text-navy-950 group-hover:text-active-blue']" 
        class="w-5 h-5 transition-colors"
      />
    </div>
    <div class="flex-1">
      <p :class="[isPrimary ? 'text-white' : 'text-navy-950']" class="font-semibold text-sm">{{ title }}</p>
      <p v-if="description" :class="[isPrimary ? 'text-white/80' : 'text-gray-500']" class="text-xs mt-0.5">{{ description }}</p>
    </div>
    <DashboardIcon 
      name="chevron-right"
      :class="[isPrimary ? 'text-white' : 'text-gray-400 group-hover:text-active-blue']" 
      class="w-5 h-5 flex-shrink-0 transition-colors"
    />
  </button>
</template>

<script setup>
import DashboardIcon from './DashboardIcon.vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: 'plus'
  },
  isPrimary: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['click'])

function handleClick() {
  emit('click')
}
</script>
