<template>
  <span
    class="px-2.5 py-0.5 rounded-full text-xs font-bold inline-flex items-center gap-1.5 border bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200/80 dark:border-slate-700/80"
  >
    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 bg-slate-400 dark:bg-slate-500" />
    {{ localizedLabel }}
  </span>
</template>

<script setup>
import { computed } from 'vue'
import { useLanguage } from '../../composables/useLanguage'

const props = defineProps({
  status: {
    type: String,
    required: true
  }
})

const { t } = useLanguage()

const localizedLabel = computed(() => {
  if (!props.status) return '—'
  const key = props.status.toLowerCase()
  const fallback = key.charAt(0).toUpperCase() + key.slice(1)
  return t(`status_${key}`, t(key, fallback))
})
</script>
