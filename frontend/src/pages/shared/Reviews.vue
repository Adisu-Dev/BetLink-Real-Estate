<template>
  <div class="space-y-5">
    <!-- Tabs -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors duration-200">
      <div class="flex border-b border-slate-200 dark:border-slate-700">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'px-6 py-3 font-semibold text-sm transition-colors border-b-2 -mb-px',
            activeTab === tab.id
              ? 'border-emerald-600 text-emerald-600'
              : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300'
          ]"
        >
          {{ tab.label }}
          <span class="ml-2 px-2 py-0.5 bg-slate-100 dark:bg-slate-800 rounded-full text-xs">{{ tab.count }}</span>
        </button>
      </div>

      <!-- Reviews List -->
      <div class="divide-y divide-slate-100 dark:divide-slate-700">
        <div v-for="review in filteredReviews" :key="review.id" class="p-6 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
          <!-- Header -->
          <div class="flex items-start justify-between gap-4 mb-3">
            <div class="flex items-center gap-3 flex-1 min-w-0">
              <img
                :src="review.avatar"
                :alt="review.reviewer"
                class="w-10 h-10 rounded-full object-cover flex-shrink-0"
              />
              <div class="min-w-0">
                <h4 class="font-semibold text-slate-900 dark:text-slate-100">{{ review.reviewer }}</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ review.date }}</p>
              </div>
            </div>
            <div class="flex-shrink-0">
              <span :class="review.statusBadge" class="px-3 py-1 text-xs font-bold rounded-full">
                {{ review.status }}
              </span>
            </div>
          </div>

          <!-- Property/Person Reference -->
          <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">{{ review.reference }}</p>

          <!-- Rating -->
          <div class="flex items-center gap-2 mb-3">
            <div class="flex gap-0.5">
              <svg
                v-for="i in 5"
                :key="i"
                :class="[
                  'w-4 h-4',
                  i <= review.rating ? 'text-yellow-400 fill-yellow-400' : 'text-slate-300 dark:text-slate-600'
                ]"
                viewBox="0 0 20 20"
              >
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
            </div>
            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ review.rating }}.0</span>
          </div>

          <!-- Review Content -->
          <p class="text-sm text-slate-700 dark:text-slate-300 mb-4 leading-relaxed">{{ review.content }}</p>

          <!-- Action Buttons -->
          <div class="flex gap-2">
            <template v-if="review.canEdit">
              <button
                @click="editReview(review)"
                class="px-3 py-1.5 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex items-center gap-1"
              >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
              </button>
              <button
                @click="deleteReview(review.id)"
                class="px-3 py-1.5 border border-red-300 dark:border-red-900 text-red-600 dark:text-red-400 text-xs font-semibold rounded hover:bg-red-50 dark:hover:bg-red-950 transition-colors flex items-center gap-1"
              >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete
              </button>
            </template>
            <template v-else>
              <button class="px-3 py-1.5 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                Report
              </button>
            </template>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredReviews.length === 0" class="px-6 py-12 text-center">
          <svg class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.068 3.29a1 1 0 00.95.69h3.461c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.068 3.29c.3.921-.755 1.688-1.54 1.118l-2.8-2.035a1 1 0 00-1.175 0l-2.8 2.035c-.784.57-1.838-.197-1.539-1.118l1.068-3.29a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.068-3.29z"/>
          </svg>
          <p class="text-slate-500 dark:text-slate-400 font-medium">No reviews yet</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const activeTab = ref('received')

const tabs = [
  { id: 'received', label: 'Reviews Received', count: 5 },
  { id: 'submitted', label: 'Reviews Submitted', count: 3 },
]

const reviews = ref([
  {
    id: 1,
    reviewer: 'Hiwot Alemu',
    avatar: 'https://ui-avatars.com/api/?name=Hiwot+Alemu&background=f59e0b&color=fff&size=64',
    date: '2 days ago',
    reference: 'For: Bole Luxury Apartment',
    status: 'Published',
    statusBadge: 'bg-emerald-100 text-emerald-700',
    rating: 5,
    content: 'Excellent property! Very clean and well-maintained. The location is perfect for my work. Highly recommend to anyone looking for quality accommodation.',
    canEdit: false,
  },
  {
    id: 2,
    reviewer: 'Yohannes Bekele',
    avatar: 'https://ui-avatars.com/api/?name=Yohannes+Bekele&background=3b82f6&color=fff&size=64',
    date: '5 days ago',
    reference: 'For: CMC Villa',
    status: 'Published',
    statusBadge: 'bg-emerald-100 text-emerald-700',
    rating: 4,
    content: 'Great property with modern amenities. The only downside is the distance to the nearest market, but overall very satisfied with the experience.',
    canEdit: false,
  },
  {
    id: 3,
    reviewer: 'You',
    avatar: 'https://ui-avatars.com/api/?name=You&background=1e293b&color=fff&size=64',
    date: '1 week ago',
    reference: 'For: Nifas Silk Office Space',
    status: 'Published',
    statusBadge: 'bg-emerald-100 text-emerald-700',
    rating: 5,
    content: 'Fantastic office space! Great natural lighting and spacious layout. The landlord was very cooperative and helpful throughout the process.',
    canEdit: true,
  },
  {
    id: 4,
    reviewer: 'Sara Mohammed',
    avatar: 'https://ui-avatars.com/api/?name=Sara+Mohammed&background=1e293b&color=fff&size=64',
    date: '10 days ago',
    reference: 'For: Piazza Contemporary',
    status: 'Pending',
    statusBadge: 'bg-yellow-100 text-yellow-700',
    rating: 3,
    content: 'Good location and modern design. Could use better parking facilities. Overall decent experience.',
    canEdit: false,
  },
])

const filteredReviews = computed(() => {
  if (activeTab.value === 'received') {
    return reviews.value
  } else {
    return reviews.value.filter(r => r.canEdit || r.reviewer === 'You')
  }
})

const editReview = (review) => {
  alert(`Edit review for ${review.reference}`)
}

const deleteReview = (id) => {
  if (confirm('Are you sure you want to delete this review?')) {
    const idx = reviews.value.findIndex(r => r.id === id)
    if (idx !== -1) {
      reviews.value.splice(idx, 1)
      alert('Review deleted successfully!')
    }
  }
}
</script>
