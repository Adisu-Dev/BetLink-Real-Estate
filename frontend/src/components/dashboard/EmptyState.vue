<template>
  <div class="flex flex-col items-center justify-center py-12 px-4 transition-colors duration-200">
    <div class="text-center">
      <svg class="mx-auto h-12 w-12 text-slate-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
      </svg>
      <h3 class="mt-4 text-lg font-medium text-slate-900 dark:text-white">{{ title }}</h3>
      <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ description }}</p>
      <button
        v-if="actionLabel"
        @click="$emit('action')"
        class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-xl transition-all shadow-xs cursor-pointer active:scale-95"
      >
        {{ actionLabel }}
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: 'No data found'
  },
  description: {
    type: String,
    default: 'Get started by creating a new item'
  },
  actionLabel: {
    type: String,
    default: ''
  }
})

defineEmits(['action'])
</script>
