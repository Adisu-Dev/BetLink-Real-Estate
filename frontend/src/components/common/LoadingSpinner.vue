<template>
  <div :class="['flex flex-col items-center justify-center', center && 'py-12']">
    <div
      :class="[
        'animate-spin rounded-full border-2 border-slate-700 border-t-emerald-500',
        sizeClasses[size] || sizeClasses.md
      ]"
    />
    <p v-if="text" class="mt-3 text-xs text-slate-400 font-medium tracking-wide">
      {{ text }}
    </p>
  </div>
</template>

<script setup>
defineProps({
  size: {
    type: String,
    default: 'md',
    validator: (val) => ['sm', 'md', 'lg', 'xl'].includes(val),
  },
  text: {
    type: String,
    default: '',
  },
  center: {
    type: Boolean,
    default: true,
  },
})

const sizeClasses = {
  sm: 'w-4 h-4 border-2',
  md: 'w-8 h-8 border-2',
  lg: 'w-12 h-12 border-3',
  xl: 'w-16 h-16 border-4',
}
</script>
