<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 cursor-pointer disabled:cursor-not-allowed disabled:opacity-60"
    :class="[
      variant === 'primary'
        ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs focus:ring-emerald-500 focus:ring-offset-white dark:focus:ring-offset-slate-900'
        : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60 focus:ring-slate-400 focus:ring-offset-white dark:focus:ring-offset-slate-900'
    ]"
  >
    <svg
      v-if="loading"
      class="animate-spin h-5 w-5"
      :class="variant === 'primary' ? 'text-white' : 'text-slate-700 dark:text-slate-200'"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    <slot />
  </button>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'button'
  },
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary'].includes(value)
  },
  disabled: Boolean,
  loading: Boolean
})
</script>
