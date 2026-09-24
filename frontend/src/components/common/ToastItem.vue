<template>
  <div
    :class="[
      'pointer-events-auto flex items-start p-4 rounded-xl border shadow-lg backdrop-blur-md transition-all duration-200',
      colorClasses[toast.type] || colorClasses.info
    ]"
  >
    <div class="flex-shrink-0 mr-3 mt-0.5">
      <!-- Success -->
      <svg v-if="toast.type === 'success'" class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
      <!-- Error -->
      <svg v-else-if="toast.type === 'error'" class="w-5 h-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
      <!-- Warning -->
      <svg v-else-if="toast.type === 'warning'" class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
      <!-- Info -->
      <svg v-else class="w-5 h-5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
    </div>

    <div class="flex-1 w-0">
      <p v-if="toast.title" class="text-sm font-semibold text-slate-100">
        {{ toast.title }}
      </p>
      <p class="text-xs text-slate-300 mt-0.5 leading-relaxed">
        {{ toast.message }}
      </p>
    </div>

    <button
      type="button"
      class="ml-3 flex-shrink-0 text-slate-400 hover:text-slate-200 focus:outline-none"
      @click="$emit('close', toast.id)"
    >
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
  </div>
</template>

<script setup>
defineProps({
  toast: {
    type: Object,
    required: true,
  },
})

defineEmits(['close'])

const colorClasses = {
  success: 'bg-slate-900/95 border-emerald-500/50 text-slate-100',
  error: 'bg-slate-900/95 border-rose-500/50 text-slate-100',
  warning: 'bg-slate-900/95 border-amber-500/50 text-slate-100',
  info: 'bg-slate-900/95 border-sky-500/50 text-slate-100',
}
</script>
