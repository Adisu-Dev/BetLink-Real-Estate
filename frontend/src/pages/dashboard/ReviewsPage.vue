<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Client & Property Reviews</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Verified feedback and ratings left by clients across your properties.</p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="space-y-4">
      <div v-for="n in 3" :key="n" class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 animate-pulse h-28"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="reviews.length === 0" class="bg-white dark:bg-slate-900 rounded-2xl p-10 border border-slate-200/80 dark:border-slate-800 text-center space-y-3">
      <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center">
        <Star class="w-6 h-6" />
      </div>
      <h3 class="text-sm font-bold text-slate-900 dark:text-white">No reviews yet</h3>
      <p class="text-xs text-slate-500 max-w-sm mx-auto">When clients review your property tours or closed agreements, their feedback will show here.</p>
    </div>

    <!-- Reviews List -->
    <div v-else class="space-y-4">
      <div 
        v-for="review in reviews" 
        :key="review.id" 
        class="bg-white dark:bg-slate-900 rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 transition-colors space-y-3"
      >
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ review.property_title || 'Property Review' }}</h3>
            <p class="text-xs text-slate-400">By {{ review.author_name || review.author || 'Verified Client' }} &bull; {{ review.date || 'Recent' }}</p>
          </div>
          
          <!-- Rating -->
          <div class="flex items-center gap-1 text-amber-400 text-xs font-bold shrink-0">
            <Star class="w-4 h-4 fill-amber-400 text-amber-400" />
            <span>{{ review.rating || 5 }}.0</span>
          </div>
        </div>

        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">{{ review.comment }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Star } from 'lucide-vue-next'
import api from '@/services/api'

const isLoading = ref(true)
const reviews = ref([])

async function fetchReviews() {
  isLoading.value = true
  try {
    const res = await api.get('/properties/1/reviews').catch(() => null)
    if (res?.data) {
      reviews.value = Array.isArray(res.data) ? res.data : (res.data.data || [])
    }
  } catch (err) {
    console.error('Failed to load reviews:', err)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchReviews()
})
</script>
