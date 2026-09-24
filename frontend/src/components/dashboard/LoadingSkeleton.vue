<template>
  <div class="animate-pulse">
    <!-- Stat Cards Skeleton -->
    <div v-if="type === 'stats'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="i in count" :key="i" class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
          <div class="flex-1">
            <div class="h-4 bg-gray-200 rounded w-24 mb-2"></div>
            <div class="h-8 bg-gray-300 rounded w-16"></div>
          </div>
          <div class="w-12 h-12 bg-gray-200 rounded-lg"></div>
        </div>
      </div>
    </div>

    <!-- Table Skeleton -->
    <div v-else-if="type === 'table'" class="bg-white rounded-lg shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="h-6 bg-gray-200 rounded w-32"></div>
      </div>
      <div class="divide-y divide-gray-200">
        <div v-for="i in count" :key="i" class="px-6 py-4 flex items-center gap-4">
          <div class="w-12 h-12 bg-gray-200 rounded-lg"></div>
          <div class="flex-1 space-y-2">
            <div class="h-4 bg-gray-300 rounded w-3/4"></div>
            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Card Skeleton -->
    <div v-else-if="type === 'card'" class="bg-white rounded-lg shadow-sm p-6">
      <div class="h-6 bg-gray-200 rounded w-40 mb-4"></div>
      <div class="space-y-3">
        <div v-for="i in count" :key="i" class="h-4 bg-gray-200 rounded"></div>
      </div>
    </div>

    <!-- List Skeleton -->
    <div v-else-if="type === 'list'" class="bg-white rounded-lg shadow-sm divide-y divide-gray-200">
      <div v-for="i in count" :key="i" class="px-6 py-4 flex items-center gap-4">
        <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
        <div class="flex-1 space-y-2">
          <div class="h-4 bg-gray-300 rounded w-2/3"></div>
          <div class="h-3 bg-gray-200 rounded w-1/2"></div>
        </div>
      </div>
    </div>

    <!-- Property Cards Skeleton -->
    <div v-else-if="type === 'properties'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="i in count" :key="i" class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="h-48 bg-gray-200"></div>
        <div class="p-4 space-y-3">
          <div class="h-5 bg-gray-300 rounded w-3/4"></div>
          <div class="h-4 bg-gray-200 rounded w-1/2"></div>
          <div class="h-6 bg-gray-300 rounded w-24"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'card', // stats, table, card, list, properties
  },
  count: {
    type: Number,
    default: 4
  }
})
</script>
