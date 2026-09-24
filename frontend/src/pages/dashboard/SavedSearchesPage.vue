<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">Saved Searches</h1>
      <RouterLink 
        to="/search"
        class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-xs transition-colors"
      >
        + New Search
      </RouterLink>
    </div>

    <div v-if="searches.length" class="space-y-4">
      <div 
        v-for="search in searches" 
        :key="search.id" 
        class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-5 sm:p-6"
      >
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
          <div class="flex-1">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ search.name }}</h3>
            <div class="mt-2.5 flex flex-wrap gap-2">
              <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                📍 {{ search.location }}
              </span>
              <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                ETB {{ (search.minPrice / 1000).toFixed(0) }}k - {{ (search.maxPrice / 1000).toFixed(0) }}k
              </span>
              <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold">
                🛏️ {{ search.bedrooms }}+ beds
              </span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-3">Created {{ search.created }}</p>
          </div>
          <div class="flex sm:flex-col items-center sm:items-end justify-between gap-2">
            <div v-if="search.notifications" class="text-xs text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
              <span>🔔</span> Alerts Enabled
            </div>
            <div class="flex gap-2">
              <RouterLink to="/properties" class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 rounded-lg font-bold text-xs">
                View Results
              </RouterLink>
              <button 
                @click="promptDeleteSearch(search)"
                class="px-3 py-1.5 border border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg font-bold text-xs transition-colors"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <EmptyState
      v-else
      title="No saved searches"
      description="Create a saved search to track properties matching your criteria"
      actionLabel="Create Search"
    />

    <!-- Delete Saved Search Confirmation Modal -->
    <ConfirmModal
      :isOpen="showDeleteModal"
      title="Delete Saved Search"
      :message="`Are you sure you want to delete '${searchToDelete?.name || 'this saved search'}'?`"
      confirmLabel="Delete"
      cancelLabel="Cancel"
      :danger="true"
      @confirm="confirmDeleteSearch"
      @cancel="showDeleteModal = false; searchToDelete = null"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import EmptyState from '../../components/dashboard/EmptyState.vue'
import ConfirmModal from '../../components/dashboard/ConfirmModal.vue'
import { useToastStore } from '../../stores/toast'

const toastStore = useToastStore()
const showDeleteModal = ref(false)
const searchToDelete = ref(null)

const searches = ref([])

const promptDeleteSearch = (search) => {
  searchToDelete.value = search
  showDeleteModal.value = true
}

const confirmDeleteSearch = () => {
  if (searchToDelete.value) {
    searches.value = searches.value.filter(s => s.id !== searchToDelete.value.id)
    toastStore.success('Deleted successfully!')
  }
  showDeleteModal.value = false
  searchToDelete.value = null
}
</script>

