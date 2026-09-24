<template>
  <div class="bg-white dark:bg-slate-900 rounded-lg shadow border border-slate-200/80 dark:border-slate-800 overflow-hidden transition-colors duration-200">
    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800">
          <tr>
            <th v-for="column in columns" :key="column.key" class="px-6 py-3 text-left text-xs font-medium text-slate-700 dark:text-slate-300 uppercase tracking-wider">
              {{ column.label }}
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200/80 dark:divide-slate-800">
          <tr v-for="row in rows" :key="row.id" class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors duration-200">
            <td v-for="column in columns" :key="column.key" class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white">
              <slot :name="`cell-${column.key}`" :row="row" :value="row[column.key]">
                {{ row[column.key] }}
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Empty State -->
    <div v-if="rows.length === 0" class="py-8 text-center text-slate-500 dark:text-slate-400 transition-colors duration-200">
      No data available
    </div>

    <!-- Pagination -->
    <div v-if="showPagination" class="flex items-center justify-between px-6 py-4 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 transition-colors duration-200">
      <div class="text-sm text-slate-600 dark:text-slate-400">
        Showing {{ rows.length }} of {{ total }} results
      </div>
      <div class="flex gap-2">
        <button class="px-3 py-1 border border-slate-300 dark:border-slate-700 rounded text-sm hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors duration-200" :disabled="currentPage === 1">
          Previous
        </button>
        <button class="px-3 py-1 border border-slate-300 dark:border-slate-700 rounded text-sm hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors duration-200">
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  columns: {
    type: Array,
    required: true
  },
  rows: {
    type: Array,
    required: true
  },
  total: {
    type: Number,
    default: 0
  },
  showPagination: {
    type: Boolean,
    default: false
  },
  currentPage: {
    type: Number,
    default: 1
  }
})
</script>
