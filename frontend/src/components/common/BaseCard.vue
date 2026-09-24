<template>
  <div
    :class="[
      'bg-slate-900 border rounded-xl overflow-hidden transition-all duration-200',
      bordered ? 'border-slate-800' : 'border-transparent',
      hover && 'hover:border-slate-700 hover:shadow-lg',
      paddingClasses[padding] || paddingClasses.md,
    ]"
  >
    <div v-if="$slots.header || title" class="pb-4 mb-4 border-b border-slate-800/80 flex items-center justify-between">
      <slot name="header">
        <div>
          <h3 v-if="title" class="text-base font-semibold text-slate-100">{{ title }}</h3>
          <p v-if="subtitle" class="text-xs text-slate-400 mt-0.5">{{ subtitle }}</p>
        </div>
      </slot>
      <div v-if="$slots.action" class="flex items-center space-x-2">
        <slot name="action" />
      </div>
    </div>

    <slot />

    <div v-if="$slots.footer" class="pt-4 mt-4 border-t border-slate-800/80">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: '',
  },
  subtitle: {
    type: String,
    default: '',
  },
  bordered: {
    type: Boolean,
    default: true,
  },
  hover: {
    type: Boolean,
    default: false,
  },
  padding: {
    type: String,
    default: 'md',
    validator: (val) => ['none', 'sm', 'md', 'lg'].includes(val),
  },
})

const paddingClasses = {
  none: 'p-0',
  sm: 'p-4',
  md: 'p-6',
  lg: 'p-8',
}
</script>
