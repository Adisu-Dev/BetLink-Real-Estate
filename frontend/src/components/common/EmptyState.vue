<template>
  <div class="flex flex-col items-center justify-center text-center p-8 rounded-xl border border-slate-800 bg-slate-900/50">
    <div class="w-14 h-14 mb-4 rounded-full bg-slate-800 flex items-center justify-center text-slate-400">
      <slot name="icon">
        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
      </slot>
    </div>

    <h4 class="text-base font-semibold text-slate-100">
      {{ title }}
    </h4>

    <p v-if="description" class="mt-1 text-xs text-slate-400 max-w-sm leading-relaxed">
      {{ description }}
    </p>

    <div v-if="$slots.action" class="mt-5">
      <slot name="action" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: 'No items found',
  },
  description: {
    type: String,
    default: '',
  },
})
</script>
