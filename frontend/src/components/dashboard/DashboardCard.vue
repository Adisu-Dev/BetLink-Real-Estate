<template>
  <div class="bg-white dark:bg-slate-900 rounded-lg shadow-sm transition-colors duration-200" :class="{ 'p-6': !noPadding }">
    <!-- Header -->
    <div v-if="title || $slots.header" class="flex items-center justify-between mb-4 transition-colors duration-200" :class="{ 'pb-4 border-b border-slate-200/80 dark:border-slate-800': divider }">
      <div v-if="title" class="flex-1">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ title }}</h3>
        <p v-if="description" class="text-sm text-slate-600 dark:text-slate-400 mt-0.5">{{ description }}</p>
      </div>
      <slot name="header" />
      <slot name="actions" />
    </div>

    <!-- Content -->
    <div>
      <slot />
    </div>

    <!-- Footer -->
    <div v-if="$slots.footer" class="mt-4 pt-4 border-t border-slate-200/80 dark:border-slate-800 transition-colors duration-200">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  divider: {
    type: Boolean,
    default: false
  },
  noPadding: {
    type: Boolean,
    default: false
  }
})
</script>
