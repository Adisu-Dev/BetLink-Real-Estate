<template>
  <span
    :class="[
      'inline-flex items-center font-medium rounded-full',
      sizeClasses[size] || sizeClasses.sm,
      variantClasses[variant] || variantClasses.default
    ]"
  >
    <span v-if="dot" class="w-1.5 h-1.5 mr-1.5 rounded-full bg-current opacity-80" />
    <slot />
  </span>
</template>

<script setup>
defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: (val) =>
      ['default', 'success', 'warning', 'danger', 'info', 'purple', 'emerald', 'secondary'].includes(val),
  },
  size: {
    type: String,
    default: 'sm',
    validator: (val) => ['xs', 'sm', 'md', 'lg'].includes(val),
  },
  dot: {
    type: Boolean,
    default: false,
  },
})

const sizeClasses = {
  xs: 'px-2 py-0.5 text-[10px]',
  sm: 'px-2.5 py-0.5 text-xs',
  md: 'px-3 py-1 text-sm',
  lg: 'px-3.5 py-1.5 text-sm',
}

const variantClasses = {
  default: 'bg-slate-100 text-slate-700 border border-slate-200',
  secondary: 'bg-slate-100 text-slate-600 border border-slate-200',
  success: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
  emerald: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
  warning: 'bg-amber-50 text-amber-700 border border-amber-200',
  danger: 'bg-rose-50 text-rose-700 border border-rose-200',
  info: 'bg-sky-50 text-sky-700 border border-sky-200',
  purple: 'bg-purple-50 text-purple-700 border border-purple-200',
}
</script>
