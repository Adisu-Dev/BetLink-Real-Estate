<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2',
      sizeClasses[size] || sizeClasses.md,
      variantClasses[variant] || variantClasses.primary,
      (disabled || loading) && 'opacity-60 cursor-not-allowed pointer-events-none',
      block && 'w-full'
    ]"
    @click="$emit('click', $event)"
  >
    <svg
      v-if="loading"
      class="animate-spin -ml-1 mr-2 h-4 w-4 text-current"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      ></circle>
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
      ></path>
    </svg>
    <slot />
  </button>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'button',
  },
  variant: {
    type: String,
    default: 'primary',
    validator: (value) =>
      ['primary', 'secondary', 'danger', 'success', 'outline', 'ghost'].includes(value),
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
  loading: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  block: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['click'])

const sizeClasses = {
  sm: 'px-3 py-1.5 text-xs',
  md: 'px-4 py-2 text-sm',
  lg: 'px-6 py-3 text-base',
}

const variantClasses = {
  primary: 'bg-slate-100 hover:bg-white text-slate-900 border border-transparent font-bold focus:ring-slate-400 shadow-xs cursor-pointer',
  secondary: 'bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white focus:ring-slate-500 border border-slate-700 cursor-pointer',
  danger: 'bg-rose-600 hover:bg-rose-700 text-white focus:ring-rose-500 shadow-sm cursor-pointer',
  success: 'bg-teal-600 hover:bg-teal-700 text-white focus:ring-teal-500 shadow-sm cursor-pointer',
  outline: 'bg-transparent hover:bg-slate-800 text-slate-300 border border-slate-600 focus:ring-slate-400 cursor-pointer',
  ghost: 'bg-transparent hover:bg-slate-800/60 text-slate-300 focus:ring-slate-500 cursor-pointer',
}
</script>
