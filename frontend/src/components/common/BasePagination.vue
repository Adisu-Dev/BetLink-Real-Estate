<template>
  <div v-if="totalPages > 1" class="flex flex-col sm:flex-row items-center justify-between gap-4 py-4 px-2">
    <!-- Results Count Indicator -->
    <div v-if="totalItems !== undefined" class="text-xs text-slate-500 dark:text-slate-400 font-medium">
      Showing <span class="font-bold text-slate-800 dark:text-slate-200">{{ fromItem }}</span> to
      <span class="font-bold text-slate-800 dark:text-slate-200">{{ toItem }}</span> of
      <span class="font-bold text-slate-800 dark:text-slate-200">{{ totalItems }}</span> properties
    </div>
    <div v-else class="text-xs text-slate-500 dark:text-slate-400">
      Page <span class="font-bold text-slate-800 dark:text-slate-200">{{ currentPage }}</span> of <span class="font-bold text-slate-800 dark:text-slate-200">{{ totalPages }}</span>
    </div>

    <!-- Navigation Page Buttons -->
    <div class="flex items-center space-x-1.5">
      <!-- Previous Button -->
      <button
        type="button"
        :disabled="currentPage <= 1"
        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white disabled:opacity-40 disabled:cursor-not-allowed transition-colors shadow-2xs cursor-pointer"
        @click="goToPage(currentPage - 1)"
        aria-label="Previous Page"
      >
        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Previous
      </button>

      <!-- Numerical Page Buttons -->
      <div class="flex items-center space-x-1">
        <template v-for="(page, idx) in visiblePages" :key="`page-${page}-${idx}`">
          <span
            v-if="page === '...'"
            class="px-2 py-1 text-xs text-slate-400 dark:text-slate-500 font-mono select-none"
          >
            ...
          </span>
          <button
            v-else
            type="button"
            :class="[
              'w-8 h-8 flex items-center justify-center text-xs font-bold rounded-xl transition-all cursor-pointer',
              page === currentPage
                ? 'bg-emerald-600 text-white shadow-xs scale-105'
                : 'bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'
            ]"
            @click="goToPage(page)"
          >
            {{ page }}
          </button>
        </template>
      </div>

      <!-- Next Button -->
      <button
        type="button"
        :disabled="currentPage >= totalPages"
        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white disabled:opacity-40 disabled:cursor-not-allowed transition-colors shadow-2xs cursor-pointer"
        @click="goToPage(currentPage + 1)"
        aria-label="Next Page"
      >
        Next
        <svg class="w-3.5 h-3.5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentPage: {
    type: Number,
    required: true,
  },
  totalPages: {
    type: Number,
    required: true,
  },
  totalItems: {
    type: Number,
    default: undefined,
  },
  perPage: {
    type: Number,
    default: 12,
  },
})

const emit = defineEmits(['change', 'page-changed', 'update:modelValue'])

function goToPage(page) {
  if (page < 1 || page > props.totalPages || page === props.currentPage) return
  emit('change', page)
  emit('page-changed', page)
  emit('update:modelValue', page)
}

const fromItem = computed(() => (props.currentPage - 1) * props.perPage + 1)
const toItem = computed(() => {
  if (props.totalItems === undefined) return props.currentPage * props.perPage
  return Math.min(props.currentPage * props.perPage, props.totalItems)
})

const visiblePages = computed(() => {
  const current = props.currentPage
  const last = props.totalPages
  const delta = 2
  const range = []
  const rangeWithDots = []
  let l

  for (let i = 1; i <= last; i++) {
    if (i === 1 || i === last || (i >= current - delta && i <= current + delta)) {
      range.push(i)
    }
  }

  for (const i of range) {
    if (l) {
      if (i - l === 2) {
        rangeWithDots.push(l + 1)
      } else if (i - l !== 1) {
        rangeWithDots.push('...')
      }
    }
    rangeWithDots.push(i)
    l = i
  }

  return rangeWithDots
})
</script>
